<?php

namespace App\Services\Contratos;

use App\Models\ContratoLocacao;
use App\Models\ContratoRescisao;
use App\Models\Imovel;
use App\Models\MovimentacaoCaucao;
use App\Models\ParcelaAluguel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RescisaoContratoService
{
    public function __construct(
        private ContratoStatusService $statusService,
        private CalcularMultaRescisaoService $calcularMultaRescisaoService,
        private MovimentacaoCaucaoService $movimentacaoCaucaoService,
        private ContratoHistoricoService $historicoService,
    ) {}

    public function rescindir(ContratoLocacao $contrato, array $dados, ?string $usuarioId = null): ContratoRescisao
    {
        $this->statusService->garantirTransicaoValida($contrato, ContratoLocacao::STATUS_RESCINDIDO);

        return DB::transaction(function () use ($contrato, $dados, $usuarioId) {
            $dataRescisao = Carbon::parse($dados['data_rescisao']);

            $calculoMulta = $this->calcularMultaRescisaoService->calcular($contrato, $dataRescisao);

            $debitosEmAberto = (float) $contrato->parcelas()
                ->whereIn('status', [ParcelaAluguel::STATUS_PENDENTE, ParcelaAluguel::STATUS_VENCIDO])
                ->where('data_vencimento', '<', $dataRescisao)
                ->sum('valor_total');

            $valorDesconto = (float) ($dados['valor_desconto'] ?? 0);
            $valorFinalMulta = round(max($calculoMulta['multa_proporcional'] - $valorDesconto, 0), 2);

            $caucao = $contrato->caucao;
            $valorCaucaoRetida = null;
            $valorCaucaoAbatida = null;
            $valorCaucaoDevolvida = null;

            if ($caucao && $caucao->possui_caucao && $caucao->saldo_atual > 0) {
                if (!empty($dados['valor_caucao_abatida']) && $dados['valor_caucao_abatida'] > 0) {
                    $valorCaucaoAbatida = (float) $dados['valor_caucao_abatida'];
                    $this->movimentacaoCaucaoService->registrar(
                        $caucao,
                        MovimentacaoCaucao::TIPO_ABATIMENTO,
                        $valorCaucaoAbatida,
                        [
                            'data_movimentacao' => $dataRescisao->toDateString(),
                            'descricao' => 'Abatimento de débitos na rescisão do contrato.',
                            'referencia_debito' => $dados['referencia_debito_caucao'] ?? null,
                        ],
                        $usuarioId,
                    );
                }

                if (!empty($dados['valor_caucao_retida']) && $dados['valor_caucao_retida'] > 0) {
                    $valorCaucaoRetida = (float) $dados['valor_caucao_retida'];
                    $tipoRetencao = $valorCaucaoRetida >= (float) $caucao->fresh()->saldo_atual
                        ? MovimentacaoCaucao::TIPO_RETENCAO_INTEGRAL
                        : MovimentacaoCaucao::TIPO_RETENCAO_PARCIAL;

                    $this->movimentacaoCaucaoService->registrar(
                        $caucao,
                        $tipoRetencao,
                        $valorCaucaoRetida,
                        [
                            'data_movimentacao' => $dataRescisao->toDateString(),
                            'descricao' => $dados['motivo_retencao_caucao'] ?? 'Retenção na rescisão do contrato.',
                        ],
                        $usuarioId,
                    );
                }

                if (!empty($dados['valor_caucao_devolvida']) && $dados['valor_caucao_devolvida'] > 0) {
                    $valorCaucaoDevolvida = (float) $dados['valor_caucao_devolvida'];
                    $this->movimentacaoCaucaoService->registrar(
                        $caucao,
                        MovimentacaoCaucao::TIPO_DEVOLUCAO,
                        $valorCaucaoDevolvida,
                        [
                            'data_movimentacao' => $dataRescisao->toDateString(),
                            'descricao' => 'Devolução do saldo remanescente na rescisão do contrato.',
                        ],
                        $usuarioId,
                    );
                }
            }

            $imovel = Imovel::lockForUpdate()->find($contrato->imovel_id);
            $imovel->update(['status' => $dados['destino_imovel']]);

            if ($dados['acao_parcelas_futuras'] === 'cancelar_parcelas_futuras') {
                ParcelaAluguel::cancelarFuturas($contrato, $dataRescisao);
            }

            $contrato->update(['status' => ContratoLocacao::STATUS_RESCINDIDO]);

            $rescisao = ContratoRescisao::create([
                'contrato_id' => $contrato->id,
                'data_rescisao' => $dataRescisao->toDateString(),
                'motivo' => $dados['motivo'],
                'solicitado_por' => $dados['solicitado_por'],
                'meses_restantes' => $calculoMulta['meses_restantes'],
                'valor_multa_rescisao' => $calculoMulta['multa_proporcional'],
                'valor_desconto' => $valorDesconto,
                'valor_final_multa' => $valorFinalMulta,
                'debitos_em_aberto' => $debitosEmAberto,
                'valor_caucao_retida' => $valorCaucaoRetida,
                'valor_caucao_abatida' => $valorCaucaoAbatida,
                'valor_caucao_devolvida' => $valorCaucaoDevolvida,
                'destino_imovel' => $dados['destino_imovel'],
                'acao_parcelas_futuras' => $dados['acao_parcelas_futuras'],
                'observacoes' => $dados['observacoes'] ?? null,
                'criado_por' => $usuarioId,
            ]);

            $this->historicoService->registrar(
                $contrato,
                'rescisao',
                "Contrato rescindido em {$dataRescisao->toDateString()}.",
                ['status' => ContratoLocacao::STATUS_ATIVO],
                ['status' => ContratoLocacao::STATUS_RESCINDIDO, 'multa' => $valorFinalMulta],
                $usuarioId,
            );

            return $rescisao;
        });
    }
}

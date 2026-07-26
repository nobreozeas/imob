<?php

namespace App\Services\Contratos;

use App\Models\ContratoLocacao;
use App\Models\Imovel;
use App\Models\MovimentacaoCaucao;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ContratoStatusService
{
    public function __construct(
        private ContratoHistoricoService $historico,
        private GerarParcelasContratoService $gerarParcelasService,
        private MovimentacaoCaucaoService $movimentacaoCaucaoService,
    ) {}

    public function enviarParaAssinatura(ContratoLocacao $contrato, ?string $usuarioId = null): void
    {
        $this->validarTransicao($contrato, ContratoLocacao::STATUS_AGUARDANDO_ASSINATURA);

        DB::transaction(function () use ($contrato, $usuarioId) {
            $statusAnterior = $contrato->status;
            $contrato->update(['status' => ContratoLocacao::STATUS_AGUARDANDO_ASSINATURA]);

            $this->historico->registrar(
                $contrato,
                'assinatura_pendente',
                'Contrato enviado para assinatura.',
                ['status' => $statusAnterior],
                ['status' => ContratoLocacao::STATUS_AGUARDANDO_ASSINATURA],
                $usuarioId,
            );
        });
    }

    public function ativar(ContratoLocacao $contrato, ?string $usuarioId = null): void
    {
        $this->validarTransicao($contrato, ContratoLocacao::STATUS_ATIVO);

        DB::transaction(function () use ($contrato, $usuarioId) {
            $statusAnterior = $contrato->status;

            $imovel = Imovel::lockForUpdate()->find($contrato->imovel_id);
            $imovel->update(['status' => Imovel::STATUS_ALUGADO]);

            $contrato->update(['status' => ContratoLocacao::STATUS_ATIVO]);

            if ($contrato->gerar_parcelas_automaticamente) {
                $this->gerarParcelasService->gerar($contrato);
            }

            $this->historico->registrar(
                $contrato,
                'ativacao',
                'Contrato ativado. Imóvel marcado como alugado.',
                ['status' => $statusAnterior],
                ['status' => ContratoLocacao::STATUS_ATIVO],
                $usuarioId,
            );
        });
    }

    public function cancelar(ContratoLocacao $contrato, ?string $usuarioId = null): void
    {
        $this->validarTransicao($contrato, ContratoLocacao::STATUS_CANCELADO);

        DB::transaction(function () use ($contrato, $usuarioId) {
            $statusAnterior = $contrato->status;
            $contrato->update(['status' => ContratoLocacao::STATUS_CANCELADO]);

            $this->historico->registrar(
                $contrato,
                'cancelamento',
                'Contrato cancelado.',
                ['status' => $statusAnterior],
                ['status' => ContratoLocacao::STATUS_CANCELADO],
                $usuarioId,
            );
        });
    }

    public function encerrar(ContratoLocacao $contrato, array $dados, ?string $usuarioId = null): void
    {
        $this->validarTransicao($contrato, ContratoLocacao::STATUS_ENCERRADO);

        DB::transaction(function () use ($contrato, $dados, $usuarioId) {
            $imovel = Imovel::find($contrato->imovel_id);
            $imovel->update(['status' => Imovel::STATUS_DISPONIVEL]);

            $contrato->update([
                'status'             => ContratoLocacao::STATUS_ENCERRADO,
                'data_encerramento'  => $dados['data_encerramento'],
                'motivo_encerramento' => $dados['motivo_encerramento'] ?? null,
            ]);

            $caucao = $contrato->caucao;
            $valorDevolvido = (float) ($dados['valor_devolvido'] ?? 0);

            if ($caucao && $caucao->possui_caucao && $valorDevolvido > 0) {
                $this->movimentacaoCaucaoService->registrar(
                    $caucao,
                    MovimentacaoCaucao::TIPO_DEVOLUCAO,
                    $valorDevolvido,
                    [
                        'data_movimentacao' => $dados['data_devolucao_caucao'] ?? $dados['data_encerramento'],
                        'descricao' => $dados['observacao_caucao'] ?? 'Devolução no encerramento do contrato.',
                    ],
                    $usuarioId,
                );
            }

            $this->historico->registrar(
                $contrato,
                'encerramento',
                "Contrato encerrado em {$dados['data_encerramento']}.",
                ['status' => ContratoLocacao::STATUS_ATIVO],
                ['status' => ContratoLocacao::STATUS_ENCERRADO, 'data_encerramento' => $dados['data_encerramento']],
                $usuarioId,
            );
        });
    }

    public function garantirTransicaoValida(ContratoLocacao $contrato, string $novoStatus): void
    {
        $this->validarTransicao($contrato, $novoStatus);
    }

    private function validarTransicao(ContratoLocacao $contrato, string $novoStatus): void
    {
        $transicoesPermitidas = [
            ContratoLocacao::STATUS_RASCUNHO              => [ContratoLocacao::STATUS_AGUARDANDO_ASSINATURA, ContratoLocacao::STATUS_ATIVO, ContratoLocacao::STATUS_CANCELADO],
            ContratoLocacao::STATUS_AGUARDANDO_ASSINATURA => [ContratoLocacao::STATUS_ATIVO, ContratoLocacao::STATUS_CANCELADO],
            ContratoLocacao::STATUS_ATIVO                 => [ContratoLocacao::STATUS_VENCIDO, ContratoLocacao::STATUS_ENCERRADO, ContratoLocacao::STATUS_RESCINDIDO],
            ContratoLocacao::STATUS_VENCIDO               => [ContratoLocacao::STATUS_ENCERRADO, ContratoLocacao::STATUS_RESCINDIDO],
            ContratoLocacao::STATUS_ENCERRADO             => [],
            ContratoLocacao::STATUS_RESCINDIDO            => [],
            ContratoLocacao::STATUS_CANCELADO             => [],
        ];

        if (!in_array($novoStatus, $transicoesPermitidas[$contrato->status] ?? [])) {
            throw ValidationException::withMessages([
                'status' => "Transição de '{$contrato->status}' para '{$novoStatus}' não permitida.",
            ]);
        }
    }
}

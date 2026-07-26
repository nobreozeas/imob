<?php

namespace App\Services\Contratos;

use App\Models\ContratoLocacao;
use App\Models\ContratoRenovacao;
use App\Models\MovimentacaoCaucao;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RenovacaoContratoService
{
    public function __construct(
        private ContratoLocacaoService $contratoLocacaoService,
        private GerarParcelasContratoService $gerarParcelasService,
        private MovimentacaoCaucaoService $movimentacaoCaucaoService,
        private ContratoHistoricoService $historicoService,
    ) {}

    public function renovar(ContratoLocacao $contratoOriginal, array $dados, User $usuario): ContratoLocacao
    {
        if (!in_array($contratoOriginal->status, [ContratoLocacao::STATUS_ATIVO, ContratoLocacao::STATUS_VENCIDO], true)) {
            throw ValidationException::withMessages([
                'status' => 'Apenas contratos ativos ou vencidos podem ser renovados.',
            ]);
        }

        return DB::transaction(function () use ($contratoOriginal, $dados, $usuario) {
            $novoContrato = ContratoLocacao::create([
                'numero' => $this->contratoLocacaoService->gerarNumero(),
                'status' => ContratoLocacao::STATUS_ATIVO,
                'tipo_contrato' => $contratoOriginal->tipo_contrato,
                'objetivo_contrato' => $contratoOriginal->objetivo_contrato,
                'imovel_id' => $contratoOriginal->imovel_id,
                'proprietario_id' => $contratoOriginal->proprietario_id,
                'inquilino_id' => $contratoOriginal->inquilino_id,
                'corretor_id' => $contratoOriginal->corretor_id,
                'contrato_anterior_id' => $contratoOriginal->id,
                'criado_por' => $usuario->id,
                'data_inicio' => $dados['nova_data_inicio'],
                'data_fim' => $dados['nova_data_fim'] ?? null,
                'dia_vencimento' => $contratoOriginal->dia_vencimento,
                'duracao_meses' => $dados['duracao_meses'] ?? $contratoOriginal->duracao_meses,
                'valor_aluguel' => $dados['valor_aluguel_novo'] ?? $contratoOriginal->valor_aluguel,
                'indice_reajuste' => $contratoOriginal->indice_reajuste,
                'periodicidade_reajuste' => $contratoOriginal->periodicidade_reajuste,
                'tipo_taxa_administracao' => $dados['tipo_taxa_administracao'] ?? $contratoOriginal->tipo_taxa_administracao,
                'valor_taxa_administracao' => $dados['valor_taxa_administracao'] ?? $contratoOriginal->valor_taxa_administracao,
                'gerar_parcelas_automaticamente' => (bool) ($dados['gerar_novas_parcelas'] ?? true),
                'dia_repasse' => $contratoOriginal->dia_repasse,
                'forma_repasse' => $contratoOriginal->forma_repasse,
                'banco' => $contratoOriginal->banco,
                'agencia' => $contratoOriginal->agencia,
                'conta' => $contratoOriginal->conta,
                'tipo_conta' => $contratoOriginal->tipo_conta,
                'pix_key' => $contratoOriginal->pix_key,
                'observacoes' => $dados['observacoes'] ?? null,
            ]);

            $manterEncargos = (bool) ($dados['manter_encargos'] ?? true);
            if ($manterEncargos) {
                foreach ($contratoOriginal->encargos as $encargo) {
                    $novoContrato->encargos()->create([
                        'tipo_encargo' => $encargo->tipo_encargo,
                        'responsavel' => $encargo->responsavel,
                        'valor_estimado' => $encargo->valor_estimado,
                        'cobrar_junto_aluguel' => $encargo->cobrar_junto_aluguel,
                        'observacao' => $encargo->observacao,
                    ]);
                }
            }

            $manterMultas = (bool) ($dados['manter_regras_multa'] ?? true);
            if ($manterMultas && $contratoOriginal->multas) {
                $novoContrato->multas()->create([
                    'possui_multa_atraso' => $contratoOriginal->multas->possui_multa_atraso,
                    'percentual_multa_atraso' => $contratoOriginal->multas->percentual_multa_atraso,
                    'valor_juros_dia' => $contratoOriginal->multas->valor_juros_dia,
                    'dias_tolerancia_atraso' => $contratoOriginal->multas->dias_tolerancia_atraso,
                    'possui_multa_rescisao' => $contratoOriginal->multas->possui_multa_rescisao,
                    'percentual_multa_rescisao' => $contratoOriginal->multas->percentual_multa_rescisao,
                    'base_calculo_rescisao' => $contratoOriginal->multas->base_calculo_rescisao,
                ]);
            }

            $caucaoAcao = $dados['caucao_acao'] ?? null;
            $novoContrato->caucao()->create([
                'possui_caucao' => $caucaoAcao !== null,
                'tipo_caucao' => $contratoOriginal->caucao?->tipo_caucao,
                'valor_caucao' => $contratoOriginal->caucao?->valor_caucao,
            ]);

            if ($caucaoAcao === ContratoRenovacao::CAUCAO_MANTER && $contratoOriginal->caucao) {
                $novoContrato->caucao->update([
                    'saldo_atual' => $contratoOriginal->caucao->saldo_atual,
                    'status_caucao' => $contratoOriginal->caucao->status_caucao,
                    'data_recebimento_caucao' => $contratoOriginal->caucao->data_recebimento_caucao,
                ]);
            }

            if ($caucaoAcao === ContratoRenovacao::CAUCAO_DEVOLVER && $contratoOriginal->caucao && $contratoOriginal->caucao->saldo_atual > 0) {
                $this->movimentacaoCaucaoService->registrar(
                    $contratoOriginal->caucao,
                    MovimentacaoCaucao::TIPO_DEVOLUCAO,
                    (float) $contratoOriginal->caucao->saldo_atual,
                    [
                        'data_movimentacao' => $dados['data_renovacao'],
                        'descricao' => 'Devolução de caução na renovação do contrato.',
                    ],
                    $usuario->id,
                );
            }

            if ((bool) ($dados['gerar_novas_parcelas'] ?? true)) {
                $this->gerarParcelasService->gerar($novoContrato);
            }

            $contratoOriginal->update(['status' => ContratoLocacao::STATUS_ENCERRADO]);

            ContratoRenovacao::create([
                'contrato_original_id' => $contratoOriginal->id,
                'novo_contrato_id' => $novoContrato->id,
                'data_renovacao' => $dados['data_renovacao'],
                'valor_aluguel_anterior' => $contratoOriginal->valor_aluguel,
                'valor_aluguel_novo' => $novoContrato->valor_aluguel,
                'data_inicio_anterior' => $contratoOriginal->data_inicio,
                'data_fim_anterior' => $contratoOriginal->data_fim,
                'nova_data_inicio' => $novoContrato->data_inicio,
                'nova_data_fim' => $novoContrato->data_fim,
                'manter_encargos' => $manterEncargos,
                'manter_regras_multa' => $manterMultas,
                'gerar_novas_parcelas' => (bool) ($dados['gerar_novas_parcelas'] ?? true),
                'caucao_acao' => $caucaoAcao,
                'observacoes' => $dados['observacoes'] ?? null,
                'criado_por' => $usuario->id,
            ]);

            $this->historicoService->registrar(
                $contratoOriginal,
                'renovado_para',
                "Contrato renovado. Nova vigência: {$novoContrato->numero}.",
                [],
                ['novo_contrato_id' => $novoContrato->id],
                $usuario->id,
            );

            $this->historicoService->registrar(
                $novoContrato,
                'criacao_por_renovacao',
                "Contrato criado por renovação de {$contratoOriginal->numero}.",
                [],
                ['contrato_anterior_id' => $contratoOriginal->id],
                $usuario->id,
            );

            return $novoContrato->fresh();
        });
    }
}

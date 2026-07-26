<?php

namespace App\Services\Contratos;

use App\Models\ContratoCaucao;
use App\Models\LancamentoFinanceiro;
use App\Models\MovimentacaoCaucao;
use Illuminate\Support\Facades\DB;

class MovimentacaoCaucaoService
{
    public function __construct(
        private MovimentacaoFinanceiraService $movimentacaoFinanceiraService,
    ) {}

    public function registrar(ContratoCaucao $caucao, string $tipo, float $valor, array $dados, ?string $usuarioId = null): MovimentacaoCaucao
    {
        return DB::transaction(function () use ($caucao, $tipo, $valor, $dados, $usuarioId) {
            $movimentacao = MovimentacaoCaucao::create([
                'caucao_contrato_id' => $caucao->id,
                'tipo_movimentacao' => $tipo,
                'valor' => $valor,
                'data_movimentacao' => $dados['data_movimentacao'],
                'forma_movimentacao' => $dados['forma_movimentacao'] ?? null,
                'descricao' => $dados['descricao'] ?? null,
                'referencia_debito' => $dados['referencia_debito'] ?? null,
                'criado_por' => $usuarioId,
            ]);

            $this->recalcularSaldo($caucao->fresh(), $tipo);

            $contrato = $caucao->contrato;

            if ($tipo === MovimentacaoCaucao::TIPO_RECEBIMENTO) {
                $this->movimentacaoFinanceiraService->registrar(
                    LancamentoFinanceiro::TIPO_ENTRADA,
                    'caucao',
                    $valor,
                    [
                        'origem' => LancamentoFinanceiro::ORIGEM_CAUCAO,
                        'data_movimentacao' => $dados['data_movimentacao'],
                        'forma_pagamento' => $dados['forma_movimentacao'] ?? null,
                        'contrato_id' => $caucao->contrato_id,
                        'caucao_contrato_id' => $caucao->id,
                        'movimentacao_caucao_id' => $movimentacao->id,
                        'imovel_id' => $contrato?->imovel_id,
                        'cliente_id' => $contrato?->inquilino_id,
                        'criado_por' => $usuarioId,
                    ],
                );
            }

            if ($tipo === MovimentacaoCaucao::TIPO_DEVOLUCAO) {
                $this->movimentacaoFinanceiraService->registrar(
                    LancamentoFinanceiro::TIPO_SAIDA,
                    'devolucao_caucao',
                    $valor,
                    [
                        'origem' => LancamentoFinanceiro::ORIGEM_MOVIMENTACAO_CAUCAO,
                        'data_movimentacao' => $dados['data_movimentacao'],
                        'forma_pagamento' => $dados['forma_movimentacao'] ?? null,
                        'contrato_id' => $caucao->contrato_id,
                        'caucao_contrato_id' => $caucao->id,
                        'movimentacao_caucao_id' => $movimentacao->id,
                        'imovel_id' => $contrato?->imovel_id,
                        'cliente_id' => $contrato?->inquilino_id,
                        'criado_por' => $usuarioId,
                    ],
                );
            }

            return $movimentacao;
        });
    }

    private function recalcularSaldo(ContratoCaucao $caucao, string $ultimoTipo): void
    {
        $saldo = 0.0;

        foreach ($caucao->movimentacoes as $movimentacao) {
            $saldo += match ($movimentacao->tipo_movimentacao) {
                MovimentacaoCaucao::TIPO_RECEBIMENTO => (float) $movimentacao->valor,
                MovimentacaoCaucao::TIPO_AJUSTE => (float) $movimentacao->valor,
                default => -1 * (float) $movimentacao->valor,
            };
        }

        $saldo = round($saldo, 2);

        $caucao->update([
            'saldo_atual' => $saldo,
            'status_caucao' => $this->statusParaMovimentacao($ultimoTipo, $saldo),
        ]);
    }

    private function statusParaMovimentacao(string $ultimoTipo, float $saldo): string
    {
        return match ($ultimoTipo) {
            MovimentacaoCaucao::TIPO_RECEBIMENTO => ContratoCaucao::STATUS_RECEBIDA,
            MovimentacaoCaucao::TIPO_DEVOLUCAO => $saldo <= 0
                ? ContratoCaucao::STATUS_DEVOLVIDA
                : ContratoCaucao::STATUS_RETIDA_PARCIALMENTE,
            MovimentacaoCaucao::TIPO_RETENCAO_INTEGRAL => ContratoCaucao::STATUS_RETIDA_TOTALMENTE,
            MovimentacaoCaucao::TIPO_RETENCAO_PARCIAL, MovimentacaoCaucao::TIPO_ABATIMENTO => $saldo <= 0
                ? ContratoCaucao::STATUS_RETIDA_TOTALMENTE
                : ContratoCaucao::STATUS_RETIDA_PARCIALMENTE,
            default => $saldo <= 0 ? ContratoCaucao::STATUS_RETIDA_TOTALMENTE : ContratoCaucao::STATUS_RECEBIDA,
        };
    }
}

<?php

namespace App\Services\Contratos;

use App\Models\LancamentoFinanceiro;
use App\Models\ParcelaAluguel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PagamentoAluguelService
{
    public function __construct(
        private CalcularMultaAtrasoService $calcularMultaAtrasoService,
        private MovimentacaoFinanceiraService $movimentacaoFinanceiraService,
        private RepasseProprietarioService $repasseProprietarioService,
    ) {}

    public function registrar(ParcelaAluguel $parcela, array $dados, ?string $usuarioId = null): ParcelaAluguel
    {
        return DB::transaction(function () use ($parcela, $dados, $usuarioId) {
            $parcela = ParcelaAluguel::query()->lockForUpdate()->findOrFail($parcela->id);

            if ($parcela->status === ParcelaAluguel::STATUS_PAGO) {
                throw ValidationException::withMessages([
                    'status' => 'Esta parcela já está paga.',
                ]);
            }

            $dataPagamento = Carbon::parse($dados['data_pagamento']);
            $calculo = $this->calcularMultaAtrasoService->calcular($parcela, $parcela->contrato->multas, $dataPagamento);

            $valorTotal = round((float) $parcela->valor_aluguel + (float) $parcela->valor_encargos + $calculo['multa'] + $calculo['juros'] - (float) ($dados['valor_desconto'] ?? 0), 2);
            $valorPago = (float) $dados['valor_pago'];

            $parcela->update([
                'data_pagamento' => $dataPagamento->toDateString(),
                'forma_pagamento' => $dados['forma_pagamento'],
                'valor_multa_atraso' => $calculo['multa'],
                'valor_juros_atraso' => $calculo['juros'],
                'valor_desconto' => $dados['valor_desconto'] ?? 0,
                'valor_total' => $valorTotal,
                'valor_pago' => $valorPago,
                'status' => $valorPago >= $valorTotal ? ParcelaAluguel::STATUS_PAGO : ParcelaAluguel::STATUS_PAGO_PARCIAL,
                'observacoes' => $dados['observacoes'] ?? $parcela->observacoes,
            ]);

            $this->movimentacaoFinanceiraService->registrar(
                LancamentoFinanceiro::TIPO_ENTRADA,
                'aluguel',
                $valorPago,
                [
                    'origem' => LancamentoFinanceiro::ORIGEM_PAGAMENTO_ALUGUEL,
                    'data_movimentacao' => $dataPagamento->toDateString(),
                    'forma_pagamento' => $dados['forma_pagamento'],
                    'contrato_id' => $parcela->contrato_id,
                    'parcela_aluguel_id' => $parcela->id,
                    'imovel_id' => $parcela->contrato->imovel_id,
                    'cliente_id' => $parcela->contrato->inquilino_id,
                    'criado_por' => $usuarioId,
                ],
            );

            $repasse = $this->repasseProprietarioService->gerarPendente($parcela);

            $this->movimentacaoFinanceiraService->registrar(
                LancamentoFinanceiro::TIPO_ENTRADA,
                'taxa_administracao',
                (float) $repasse->valor_taxa_administracao,
                [
                    'origem' => LancamentoFinanceiro::ORIGEM_PAGAMENTO_ALUGUEL,
                    'data_movimentacao' => $dataPagamento->toDateString(),
                    'forma_pagamento' => $dados['forma_pagamento'],
                    'contrato_id' => $parcela->contrato_id,
                    'parcela_aluguel_id' => $parcela->id,
                    'imovel_id' => $parcela->contrato->imovel_id,
                    'cliente_id' => $parcela->contrato->inquilino_id,
                    'criado_por' => $usuarioId,
                ],
            );

            return $parcela->fresh();
        });
    }
}

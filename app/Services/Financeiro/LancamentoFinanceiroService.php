<?php

namespace App\Services\Financeiro;

use App\Models\LancamentoFinanceiro;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LancamentoFinanceiroService
{
    public function __construct(
        private HistoricoFinanceiroService $historicoService,
    ) {}

    public function marcarComoPago(LancamentoFinanceiro $lancamento, array $dados, string $usuarioId): LancamentoFinanceiro
    {
        if ($lancamento->status !== LancamentoFinanceiro::STATUS_PENDENTE) {
            throw ValidationException::withMessages([
                'status' => 'Apenas lançamentos pendentes podem ser marcados como pagos.',
            ]);
        }

        return DB::transaction(function () use ($lancamento, $dados, $usuarioId) {
            $dadosAnteriores = $lancamento->only(['status', 'data_pagamento', 'forma_pagamento']);

            $lancamento->update([
                'status' => LancamentoFinanceiro::STATUS_PAGO,
                'data_pagamento' => $dados['data_pagamento'],
                'forma_pagamento' => $dados['forma_pagamento'],
                'pago_por' => $usuarioId,
            ]);

            $this->historicoService->registrar(
                'lancamento_pago',
                $lancamento,
                $dadosAnteriores,
                $lancamento->only(['status', 'data_pagamento', 'forma_pagamento']),
                $usuarioId,
            );

            return $lancamento->fresh();
        });
    }

    public function cancelar(LancamentoFinanceiro $lancamento, string $motivo, string $usuarioId): LancamentoFinanceiro
    {
        if ($lancamento->status !== LancamentoFinanceiro::STATUS_PENDENTE) {
            throw ValidationException::withMessages([
                'status' => 'Apenas lançamentos pendentes podem ser cancelados.',
            ]);
        }

        return DB::transaction(function () use ($lancamento, $motivo, $usuarioId) {
            $dadosAnteriores = $lancamento->only(['status']);

            $lancamento->update([
                'status' => LancamentoFinanceiro::STATUS_CANCELADO,
                'motivo_cancelamento' => $motivo,
                'cancelado_por' => $usuarioId,
            ]);

            $this->historicoService->registrar(
                'lancamento_cancelado',
                $lancamento,
                $dadosAnteriores,
                ['status' => $lancamento->status, 'motivo_cancelamento' => $motivo],
                $usuarioId,
                $motivo,
            );

            return $lancamento->fresh();
        });
    }

    public function estornar(LancamentoFinanceiro $lancamento, string $motivo, string $usuarioId): LancamentoFinanceiro
    {
        if ($lancamento->status !== LancamentoFinanceiro::STATUS_PAGO) {
            throw ValidationException::withMessages([
                'status' => 'Apenas lançamentos pagos podem ser estornados.',
            ]);
        }

        return DB::transaction(function () use ($lancamento, $motivo, $usuarioId) {
            $dadosAnteriores = $lancamento->only(['status']);

            $lancamento->update([
                'status' => LancamentoFinanceiro::STATUS_ESTORNADO,
                'motivo_estorno' => $motivo,
                'estornado_por' => $usuarioId,
            ]);

            $this->historicoService->registrar(
                'lancamento_estornado',
                $lancamento,
                $dadosAnteriores,
                ['status' => $lancamento->status, 'motivo_estorno' => $motivo],
                $usuarioId,
                $motivo,
            );

            return $lancamento->fresh();
        });
    }
}

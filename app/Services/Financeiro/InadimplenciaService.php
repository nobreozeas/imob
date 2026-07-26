<?php

namespace App\Services\Financeiro;

use App\Models\ParcelaAluguel;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InadimplenciaService
{
    public function listar(array $filtros = []): LengthAwarePaginator
    {
        return $this->baseQuery($filtros)
            ->with(['contrato.inquilino', 'contrato.proprietario', 'contrato.imovel'])
            ->orderBy('data_vencimento')
            ->paginate(15)
            ->withQueryString();
    }

    public function indicadores(array $filtros = []): array
    {
        $parcelas = $this->baseQuery($filtros)->get();
        $hoje = Carbon::today();

        return [
            'quantidade_parcelas' => $parcelas->count(),
            'valor_total' => (float) $parcelas->sum(fn (ParcelaAluguel $p) => (float) $p->valor_total - (float) $p->valor_pago),
            'quantidade_contratos' => $parcelas->pluck('contrato_id')->unique()->count(),
            'quantidade_clientes' => $parcelas->pluck('contrato.inquilino_id')->filter()->unique()->count(),
            'maior_atraso_dias' => (int) ($parcelas->max(fn (ParcelaAluguel $p) => abs($hoje->diffInDays($p->data_vencimento))) ?? 0),
        ];
    }

    private function baseQuery(array $filtros = [])
    {
        return ParcelaAluguel::query()
            ->whereIn('status', [ParcelaAluguel::STATUS_PENDENTE, ParcelaAluguel::STATUS_PAGO_PARCIAL])
            ->where('data_vencimento', '<', Carbon::today()->toDateString())
            ->when($filtros['contrato_id'] ?? null, fn ($q, $v) => $q->where('contrato_id', $v))
            ->when($filtros['proprietario_id'] ?? null, fn ($q, $v) => $q->whereHas('contrato', fn ($c) => $c->where('proprietario_id', $v)))
            ->when($filtros['cliente_id'] ?? null, fn ($q, $v) => $q->whereHas('contrato', fn ($c) => $c->where('inquilino_id', $v)));
    }
}

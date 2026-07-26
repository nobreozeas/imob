<?php

namespace App\Services\Financeiro;

use App\Models\LancamentoFinanceiro;
use App\Models\ParcelaAluguel;
use App\Models\RepasseProprietario;
use Carbon\Carbon;

class IndicadoresFinanceirosService
{
    public function resumoMensal(array $filtros = []): array
    {
        $dataInicio = Carbon::parse($filtros['data_inicio'] ?? Carbon::now()->startOfMonth()->toDateString());
        $dataFim = Carbon::parse($filtros['data_fim'] ?? Carbon::now()->endOfMonth()->toDateString());
        $hoje = Carbon::today()->toDateString();

        $lancamentosPagos = LancamentoFinanceiro::query()
            ->where('status', LancamentoFinanceiro::STATUS_PAGO)
            ->whereBetween('data_pagamento', [$dataInicio->toDateString(), $dataFim->toDateString()])
            ->when($filtros['proprietario_id'] ?? null, fn ($q, $v) => $this->filtrarPorProprietario($q, $v))
            ->when($filtros['status_contrato'] ?? null, fn ($q, $v) => $q->whereHas('contrato', fn ($c) => $c->where('status', $v)))
            ->when($filtros['status_imovel'] ?? null, fn ($q, $v) => $q->whereHas('imovel', fn ($i) => $i->where('status', $v)));

        $lancamentosDoResultado = (clone $lancamentosPagos)->whereHas('categoria', fn ($q) => $q->where('impacta_resultado', true));

        $receitas = (clone $lancamentosDoResultado)->where('tipo', LancamentoFinanceiro::TIPO_ENTRADA)->sum('valor');
        $despesas = (clone $lancamentosDoResultado)->where('tipo', LancamentoFinanceiro::TIPO_SAIDA)->sum('valor');
        $caucoesRecebidas = (clone $lancamentosPagos)->where('origem', LancamentoFinanceiro::ORIGEM_CAUCAO)->sum('valor');

        $parcelasQuery = ParcelaAluguel::query()
            ->when($filtros['proprietario_id'] ?? null, fn ($q, $v) => $q->whereHas('contrato', fn ($c) => $c->where('proprietario_id', $v)))
            ->when($filtros['status_contrato'] ?? null, fn ($q, $v) => $q->whereHas('contrato', fn ($c) => $c->where('status', $v)))
            ->when($filtros['status_imovel'] ?? null, fn ($q, $v) => $q->whereHas('contrato.imovel', fn ($i) => $i->where('status', $v)));

        $alugueisRecebidos = (clone $parcelasQuery)
            ->where('status', ParcelaAluguel::STATUS_PAGO)
            ->whereBetween('data_pagamento', [$dataInicio->toDateString(), $dataFim->toDateString()])
            ->sum('valor_pago');

        $alugueisEmAberto = (clone $parcelasQuery)
            ->where('status', ParcelaAluguel::STATUS_PENDENTE)
            ->where('data_vencimento', '>=', $hoje)
            ->sum('valor_total');

        $alugueisVencidos = (clone $parcelasQuery)
            ->whereIn('status', [ParcelaAluguel::STATUS_PENDENTE, ParcelaAluguel::STATUS_PAGO_PARCIAL])
            ->where('data_vencimento', '<', $hoje)
            ->sum('valor_total');

        $repassesQuery = RepasseProprietario::query()
            ->when($filtros['proprietario_id'] ?? null, fn ($q, $v) => $q->where('proprietario_id', $v))
            ->when($filtros['status_contrato'] ?? null, fn ($q, $v) => $q->whereHas('contrato', fn ($c) => $c->where('status', $v)))
            ->when($filtros['status_imovel'] ?? null, fn ($q, $v) => $q->whereHas('imovel', fn ($i) => $i->where('status', $v)));

        $repassesPendentes = (clone $repassesQuery)->where('status', RepasseProprietario::STATUS_PENDENTE)->sum('valor_liquido');

        $repassesPagos = (clone $repassesQuery)
            ->where('status', RepasseProprietario::STATUS_PAGO)
            ->whereBetween('data_pagamento', [$dataInicio->toDateString(), $dataFim->toDateString()])
            ->sum('valor_liquido');

        return [
            'periodo' => ['data_inicio' => $dataInicio->toDateString(), 'data_fim' => $dataFim->toDateString()],
            'receitas' => (float) $receitas,
            'despesas' => (float) $despesas,
            'saldo' => (float) $receitas - (float) $despesas,
            'alugueis_recebidos' => (float) $alugueisRecebidos,
            'alugueis_em_aberto' => (float) $alugueisEmAberto,
            'alugueis_vencidos' => (float) $alugueisVencidos,
            'repasses_pendentes' => (float) $repassesPendentes,
            'repasses_pagos' => (float) $repassesPagos,
            'caucoes_recebidas' => (float) $caucoesRecebidas,
        ];
    }

    private function filtrarPorProprietario($query, string $proprietarioId)
    {
        return $query->where(function ($q) use ($proprietarioId) {
            $q->where('cliente_id', $proprietarioId)
                ->orWhereHas('contrato', fn ($c) => $c->where('proprietario_id', $proprietarioId));
        });
    }
}

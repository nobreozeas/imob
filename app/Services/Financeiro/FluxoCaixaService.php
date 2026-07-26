<?php

namespace App\Services\Financeiro;

use App\Models\LancamentoFinanceiro;
use Illuminate\Support\Collection;

class FluxoCaixaService
{
    public function calcular(string $dataInicio, string $dataFim, string $agrupamento = 'dia'): Collection
    {
        $formato = $agrupamento === 'mes' ? 'Y-m' : 'Y-m-d';

        $previstos = LancamentoFinanceiro::query()
            ->where('status', LancamentoFinanceiro::STATUS_PENDENTE)
            ->whereBetween('data_vencimento', [$dataInicio, $dataFim])
            ->get(['tipo', 'valor', 'data_vencimento']);

        $realizados = LancamentoFinanceiro::query()
            ->where('status', LancamentoFinanceiro::STATUS_PAGO)
            ->whereBetween('data_pagamento', [$dataInicio, $dataFim])
            ->get(['tipo', 'valor', 'data_pagamento']);

        $buckets = [];

        foreach ($previstos as $lancamento) {
            $chave = $lancamento->data_vencimento->format($formato);
            $buckets[$chave] ??= $this->bucketVazio($chave);
            $campo = $lancamento->tipo === LancamentoFinanceiro::TIPO_ENTRADA ? 'entradas_previstas' : 'saidas_previstas';
            $buckets[$chave][$campo] += (float) $lancamento->valor;
        }

        foreach ($realizados as $lancamento) {
            $chave = $lancamento->data_pagamento->format($formato);
            $buckets[$chave] ??= $this->bucketVazio($chave);
            $campo = $lancamento->tipo === LancamentoFinanceiro::TIPO_ENTRADA ? 'entradas_realizadas' : 'saidas_realizadas';
            $buckets[$chave][$campo] += (float) $lancamento->valor;
        }

        return collect($buckets)
            ->map(function (array $bucket) {
                $bucket['saldo_previsto'] = $bucket['entradas_previstas'] - $bucket['saidas_previstas'];
                $bucket['saldo_realizado'] = $bucket['entradas_realizadas'] - $bucket['saidas_realizadas'];

                return $bucket;
            })
            ->sortKeys()
            ->values();
    }

    private function bucketVazio(string $data): array
    {
        return [
            'data' => $data,
            'entradas_previstas' => 0.0,
            'entradas_realizadas' => 0.0,
            'saidas_previstas' => 0.0,
            'saidas_realizadas' => 0.0,
        ];
    }
}

<?php

namespace App\Services\Contratos;

use App\Models\ContratoMultas;
use App\Models\ParcelaAluguel;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class CalcularMultaAtrasoService
{
    public function calcular(ParcelaAluguel $parcela, ?ContratoMultas $regras, CarbonInterface $dataPagamento): array
    {
        $vencimento = Carbon::parse($parcela->data_vencimento);
        $diasAtraso = (int) $vencimento->diffInDays($dataPagamento, false);

        if ($diasAtraso <= 0 || !$regras || !$regras->possui_multa_atraso) {
            return ['multa' => 0.0, 'juros' => 0.0, 'dias_atraso' => max($diasAtraso, 0), 'total' => (float) $parcela->valor_aluguel];
        }

        $tolerancia = (int) ($regras->dias_tolerancia_atraso ?? 0);

        if ($diasAtraso <= $tolerancia) {
            return ['multa' => 0.0, 'juros' => 0.0, 'dias_atraso' => $diasAtraso, 'total' => (float) $parcela->valor_aluguel];
        }

        $valorAluguel = (float) $parcela->valor_aluguel;
        $percentualMulta = (float) ($regras->percentual_multa_atraso ?? 0);
        $percentualJurosDia = (float) ($regras->valor_juros_dia ?? 0);

        $multa = round($valorAluguel * $percentualMulta / 100, 2);
        $juros = round($valorAluguel * ($percentualJurosDia / 100) * $diasAtraso, 2);

        return [
            'multa' => $multa,
            'juros' => $juros,
            'dias_atraso' => $diasAtraso,
            'total' => round($valorAluguel + $multa + $juros, 2),
        ];
    }
}

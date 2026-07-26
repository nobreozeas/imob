<?php

namespace App\Services\Contratos;

use App\Models\ContratoLocacao;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class CalcularMultaRescisaoService
{
    public function calcular(ContratoLocacao $contrato, CarbonInterface $dataRescisao): array
    {
        $regras = $contrato->multas;

        if (!$regras || !$regras->possui_multa_rescisao || !$contrato->data_fim) {
            return ['meses_restantes' => 0, 'multa_cheia' => 0.0, 'multa_proporcional' => 0.0];
        }

        $dataFim = Carbon::parse($contrato->data_fim);

        if ($dataRescisao->greaterThanOrEqualTo($dataFim)) {
            return ['meses_restantes' => 0, 'multa_cheia' => 0.0, 'multa_proporcional' => 0.0];
        }

        $mesesRestantes = (int) ceil($dataRescisao->diffInDays($dataFim) / 30);
        $percentual = (float) $regras->percentual_multa_rescisao / 100;
        $valorAluguel = (float) $contrato->valor_aluguel;

        $base = $regras->base_calculo_rescisao === 'alugueis_restantes'
            ? $valorAluguel * $mesesRestantes
            : $valorAluguel;

        $multa = round($base * $percentual, 2);

        return [
            'meses_restantes' => $mesesRestantes,
            'multa_cheia' => $multa,
            'multa_proporcional' => $multa,
        ];
    }
}

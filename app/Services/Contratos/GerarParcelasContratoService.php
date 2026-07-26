<?php

namespace App\Services\Contratos;

use App\Models\ContratoLocacao;
use App\Models\ParcelaAluguel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GerarParcelasContratoService
{
    public function gerar(ContratoLocacao $contrato): Collection
    {
        $valorEncargos = (float) $contrato->encargos()
            ->where('cobrar_junto_aluguel', true)
            ->sum('valor_estimado');

        $referencias = $this->gerarReferencias($contrato);

        $parcelas = collect();

        foreach ($referencias as [$mes, $ano, $vencimento]) {
            $parcela = ParcelaAluguel::firstOrCreate(
                [
                    'contrato_id' => $contrato->id,
                    'mes_referencia' => $mes,
                    'ano_referencia' => $ano,
                ],
                [
                    'data_vencimento' => $vencimento,
                    'valor_aluguel' => $contrato->valor_aluguel,
                    'valor_encargos' => $valorEncargos,
                    'valor_total' => round((float) $contrato->valor_aluguel + $valorEncargos, 2),
                    'status' => ParcelaAluguel::STATUS_PENDENTE,
                ],
            );

            $parcelas->push($parcela);
        }

        return $parcelas;
    }

    private function gerarReferencias(ContratoLocacao $contrato): array
    {
        $inicio = Carbon::parse($contrato->data_inicio)->startOfMonth();
        $diaVencimento = (int) $contrato->dia_vencimento;

        if ($contrato->data_fim) {
            $quantidade = $inicio->diffInMonths(Carbon::parse($contrato->data_fim)->startOfMonth()) + 1;
        } else {
            $quantidade = (int) ($contrato->quantidade_parcelas ?? 12);
        }

        $referencias = [];

        for ($i = 0; $i < $quantidade; $i++) {
            $mesReferencia = $inicio->copy()->addMonths($i);
            $ultimoDiaDoMes = $mesReferencia->daysInMonth;
            $vencimento = $mesReferencia->copy()->day(min($diaVencimento, $ultimoDiaDoMes));

            $referencias[] = [$mesReferencia->month, $mesReferencia->year, $vencimento->toDateString()];
        }

        return $referencias;
    }
}

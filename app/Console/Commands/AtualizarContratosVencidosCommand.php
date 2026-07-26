<?php

namespace App\Console\Commands;

use App\Models\ContratoLocacao;
use App\Models\ParcelaAluguel;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AtualizarContratosVencidosCommand extends Command
{
    protected $signature = 'contratos:atualizar-vencidos';

    protected $description = 'Marca parcelas pendentes e contratos ativos vencidos com base na data atual';

    public function handle(): int
    {
        $hoje = Carbon::today();

        $parcelas = ParcelaAluguel::query()
            ->where('status', ParcelaAluguel::STATUS_PENDENTE)
            ->where('data_vencimento', '<', $hoje)
            ->update(['status' => ParcelaAluguel::STATUS_VENCIDO]);

        $contratos = ContratoLocacao::query()
            ->where('status', ContratoLocacao::STATUS_ATIVO)
            ->whereNotNull('data_fim')
            ->where('data_fim', '<', $hoje)
            ->update(['status' => ContratoLocacao::STATUS_VENCIDO]);

        $this->info("Parcelas marcadas como vencidas: {$parcelas}");
        $this->info("Contratos marcados como vencidos: {$contratos}");

        return self::SUCCESS;
    }
}

<?php

namespace App\Services\Financeiro;

use App\Models\HistoricoFinanceiro;
use App\Models\LancamentoFinanceiro;

class HistoricoFinanceiroService
{
    public function registrar(
        string $acao,
        LancamentoFinanceiro $lancamento,
        ?array $dadosAnteriores,
        ?array $dadosNovos,
        ?string $usuarioId,
        ?string $descricao = null,
    ): HistoricoFinanceiro {
        return HistoricoFinanceiro::create([
            'lancamento_financeiro_id' => $lancamento->id,
            'entidade_tipo' => LancamentoFinanceiro::class,
            'entidade_id' => $lancamento->id,
            'acao' => $acao,
            'descricao' => $descricao,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos,
            'criado_por' => $usuarioId,
        ]);
    }
}

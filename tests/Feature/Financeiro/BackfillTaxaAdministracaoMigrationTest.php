<?php

namespace Tests\Feature\Financeiro;

use App\Models\CategoriaFinanceira;
use App\Models\LancamentoFinanceiro;
use App\Models\RepasseProprietario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Contratos\ContratoTestHelpers;
use Tests\TestCase;

class BackfillTaxaAdministracaoMigrationTest extends TestCase
{
    use RefreshDatabase, ContratoTestHelpers;

    private function carregarMigration(): object
    {
        return require database_path('migrations/2026_07_06_100003_backfill_lancamentos_taxa_administracao.php');
    }

    public function test_cria_lancamento_de_taxa_para_repasse_antigo_sem_duplicar(): void
    {
        $contrato = $this->criarContrato(['valor_aluguel' => 1000, 'valor_taxa_administracao' => 10]);
        $parcela = $contrato->parcelas()->create([
            'mes_referencia' => 1,
            'ano_referencia' => 2026,
            'data_vencimento' => '2026-01-05',
            'data_pagamento' => '2026-01-05',
            'valor_aluguel' => 1000,
            'valor_total' => 1000,
            'valor_pago' => 1000,
            'status' => 'pago',
        ]);

        $categoriaAluguel = CategoriaFinanceira::where('slug', 'aluguel')->firstOrFail();

        LancamentoFinanceiro::create([
            'codigo' => 'LF-999001',
            'tipo' => LancamentoFinanceiro::TIPO_ENTRADA,
            'categoria_financeira_id' => $categoriaAluguel->id,
            'contrato_id' => $contrato->id,
            'parcela_aluguel_id' => $parcela->id,
            'valor' => 1000,
            'data_pagamento' => '2026-01-05',
            'status' => LancamentoFinanceiro::STATUS_PAGO,
            'origem' => LancamentoFinanceiro::ORIGEM_PAGAMENTO_ALUGUEL,
        ]);

        $repasse = RepasseProprietario::create([
            'contrato_id' => $contrato->id,
            'imovel_id' => $contrato->imovel_id,
            'proprietario_id' => $contrato->proprietario_id,
            'parcela_aluguel_id' => $parcela->id,
            'valor_bruto' => 1000,
            'valor_taxa_administracao' => 100,
            'valor_liquido' => 900,
            'status' => RepasseProprietario::STATUS_PENDENTE,
        ]);

        $migration = $this->carregarMigration();

        $migration->up();

        $this->assertSame(1, LancamentoFinanceiro::where('parcela_aluguel_id', $parcela->id)
            ->whereHas('categoria', fn ($q) => $q->where('slug', 'taxa_administracao'))
            ->count());

        $lancamentoTaxa = LancamentoFinanceiro::where('parcela_aluguel_id', $parcela->id)
            ->whereHas('categoria', fn ($q) => $q->where('slug', 'taxa_administracao'))
            ->first();
        $this->assertSame('100.00', $lancamentoTaxa->valor);

        // idempotente: rodar de novo não duplica
        $migration->up();

        $this->assertSame(1, LancamentoFinanceiro::where('parcela_aluguel_id', $parcela->id)
            ->whereHas('categoria', fn ($q) => $q->where('slug', 'taxa_administracao'))
            ->count());

        $migration->down();

        $this->assertSame(0, LancamentoFinanceiro::where('parcela_aluguel_id', $parcela->id)
            ->whereHas('categoria', fn ($q) => $q->where('slug', 'taxa_administracao'))
            ->count());
    }
}

<?php

namespace Tests\Unit\Services\Financeiro;

use App\Models\CategoriaFinanceira;
use App\Models\LancamentoFinanceiro;
use App\Models\ParcelaAluguel;
use App\Models\RepasseProprietario;
use App\Services\Financeiro\IndicadoresFinanceirosService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Contratos\ContratoTestHelpers;
use Tests\TestCase;

class IndicadoresFinanceirosServiceTest extends TestCase
{
    use RefreshDatabase, ContratoTestHelpers;

    public function test_calcula_indicadores_do_periodo(): void
    {
        $contrato = $this->criarContrato();

        $categoriaAluguel = CategoriaFinanceira::firstOrCreate(['slug' => 'aluguel'], ['nome' => 'Aluguel', 'tipo' => 'entrada', 'ativa' => true, 'impacta_resultado' => false]);
        $categoriaTaxa = CategoriaFinanceira::firstOrCreate(['slug' => 'taxa_administracao'], ['nome' => 'Taxa de administração', 'tipo' => 'entrada', 'ativa' => true, 'impacta_resultado' => true]);
        $categoriaDespesa = CategoriaFinanceira::firstOrCreate(['slug' => 'despesa_operacional'], ['nome' => 'Despesa operacional', 'tipo' => 'saida', 'ativa' => true]);

        LancamentoFinanceiro::create([
            'codigo' => 'LF-000001',
            'tipo' => LancamentoFinanceiro::TIPO_ENTRADA,
            'categoria_financeira_id' => $categoriaAluguel->id,
            'contrato_id' => $contrato->id,
            'valor' => 1000,
            'data_pagamento' => now()->toDateString(),
            'status' => LancamentoFinanceiro::STATUS_PAGO,
            'origem' => LancamentoFinanceiro::ORIGEM_PAGAMENTO_ALUGUEL,
        ]);

        LancamentoFinanceiro::create([
            'codigo' => 'LF-000004',
            'tipo' => LancamentoFinanceiro::TIPO_ENTRADA,
            'categoria_financeira_id' => $categoriaTaxa->id,
            'contrato_id' => $contrato->id,
            'valor' => 100,
            'data_pagamento' => now()->toDateString(),
            'status' => LancamentoFinanceiro::STATUS_PAGO,
            'origem' => LancamentoFinanceiro::ORIGEM_PAGAMENTO_ALUGUEL,
        ]);

        LancamentoFinanceiro::create([
            'codigo' => 'LF-000002',
            'tipo' => LancamentoFinanceiro::TIPO_SAIDA,
            'categoria_financeira_id' => $categoriaDespesa->id,
            'valor' => 400,
            'data_pagamento' => now()->toDateString(),
            'status' => LancamentoFinanceiro::STATUS_PAGO,
            'origem' => LancamentoFinanceiro::ORIGEM_DESPESA,
        ]);

        ParcelaAluguel::create([
            'contrato_id' => $contrato->id,
            'mes_referencia' => now()->month,
            'ano_referencia' => now()->year,
            'data_vencimento' => now()->subDays(5)->toDateString(),
            'valor_aluguel' => 1500,
            'valor_total' => 1500,
            'status' => ParcelaAluguel::STATUS_PENDENTE,
        ]);

        $parcelaComRepasse = ParcelaAluguel::create([
            'contrato_id' => $contrato->id,
            'mes_referencia' => now()->subMonth()->month,
            'ano_referencia' => now()->subMonth()->year,
            'data_vencimento' => now()->subMonth()->toDateString(),
            'valor_aluguel' => 1000,
            'valor_total' => 1000,
            'status' => ParcelaAluguel::STATUS_PAGO,
        ]);

        RepasseProprietario::create([
            'contrato_id' => $contrato->id,
            'imovel_id' => $contrato->imovel_id,
            'proprietario_id' => $contrato->proprietario_id,
            'parcela_aluguel_id' => $parcelaComRepasse->id,
            'valor_bruto' => 1000,
            'valor_taxa_administracao' => 100,
            'valor_liquido' => 900,
            'status' => RepasseProprietario::STATUS_PENDENTE,
        ]);

        $resumo = (new IndicadoresFinanceirosService())->resumoMensal();

        $this->assertSame(100.0, $resumo['receitas']);
        $this->assertSame(400.0, $resumo['despesas']);
        $this->assertSame(-300.0, $resumo['saldo']);
        $this->assertSame(1500.0, $resumo['alugueis_vencidos']);
        $this->assertSame(0.0, $resumo['alugueis_em_aberto']);
        $this->assertSame(900.0, $resumo['repasses_pendentes']);
    }

    public function test_receitas_e_saldo_consideram_apenas_a_taxa_de_administracao(): void
    {
        $categoriaAluguel = CategoriaFinanceira::firstOrCreate(['slug' => 'aluguel'], ['nome' => 'Aluguel', 'tipo' => 'entrada', 'ativa' => true, 'impacta_resultado' => false]);
        $categoriaTaxa = CategoriaFinanceira::firstOrCreate(['slug' => 'taxa_administracao'], ['nome' => 'Taxa de administração', 'tipo' => 'entrada', 'ativa' => true, 'impacta_resultado' => true]);
        $categoriaDespesa = CategoriaFinanceira::firstOrCreate(['slug' => 'despesa_operacional'], ['nome' => 'Despesa operacional', 'tipo' => 'saida', 'ativa' => true]);

        LancamentoFinanceiro::create([
            'codigo' => 'LF-100001',
            'tipo' => LancamentoFinanceiro::TIPO_ENTRADA,
            'categoria_financeira_id' => $categoriaAluguel->id,
            'valor' => 1000,
            'data_pagamento' => now()->toDateString(),
            'status' => LancamentoFinanceiro::STATUS_PAGO,
            'origem' => LancamentoFinanceiro::ORIGEM_PAGAMENTO_ALUGUEL,
        ]);

        LancamentoFinanceiro::create([
            'codigo' => 'LF-100002',
            'tipo' => LancamentoFinanceiro::TIPO_ENTRADA,
            'categoria_financeira_id' => $categoriaTaxa->id,
            'valor' => 100,
            'data_pagamento' => now()->toDateString(),
            'status' => LancamentoFinanceiro::STATUS_PAGO,
            'origem' => LancamentoFinanceiro::ORIGEM_PAGAMENTO_ALUGUEL,
        ]);

        LancamentoFinanceiro::create([
            'codigo' => 'LF-100003',
            'tipo' => LancamentoFinanceiro::TIPO_SAIDA,
            'categoria_financeira_id' => $categoriaDespesa->id,
            'valor' => 50,
            'data_pagamento' => now()->toDateString(),
            'status' => LancamentoFinanceiro::STATUS_PAGO,
            'origem' => LancamentoFinanceiro::ORIGEM_DESPESA,
        ]);

        $resumo = (new IndicadoresFinanceirosService())->resumoMensal();

        $this->assertSame(100.0, $resumo['receitas']);
        $this->assertSame(50.0, $resumo['despesas']);
        $this->assertSame(50.0, $resumo['saldo']);
    }
}

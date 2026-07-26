<?php

namespace Tests\Feature\Financeiro;

use App\Models\CategoriaFinanceira;
use App\Models\LancamentoFinanceiro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Contratos\ContratoTestHelpers;
use Tests\TestCase;

class LancamentoFinanceiroManualTest extends TestCase
{
    use RefreshDatabase, ContratoTestHelpers;

    public function test_cria_receita_pendente(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar', 'financeiro.criar']);
        $categoria = CategoriaFinanceira::firstOrCreate(['slug' => 'receita_diversa'], ['nome' => 'Receita diversa', 'tipo' => 'entrada', 'ativa' => true]);

        $response = $this->actingAs($user)->post(route('financeiro.lancamentos.receitas.store'), [
            'categoria_financeira_id' => $categoria->id,
            'descricao' => 'Taxa de vistoria',
            'valor' => 250,
            'status' => 'pendente',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lancamentos_financeiros', [
            'descricao' => 'Taxa de vistoria',
            'tipo' => LancamentoFinanceiro::TIPO_ENTRADA,
            'status' => LancamentoFinanceiro::STATUS_PENDENTE,
            'origem' => LancamentoFinanceiro::ORIGEM_RECEITA_DIVERSA,
        ]);
    }

    public function test_receita_paga_exige_data_e_forma_de_pagamento(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar', 'financeiro.criar']);
        $categoria = CategoriaFinanceira::firstOrCreate(['slug' => 'receita_diversa'], ['nome' => 'Receita diversa', 'tipo' => 'entrada', 'ativa' => true]);

        $response = $this->actingAs($user)->post(route('financeiro.lancamentos.receitas.store'), [
            'categoria_financeira_id' => $categoria->id,
            'descricao' => 'Taxa de vistoria',
            'valor' => 250,
            'status' => 'pago',
        ]);

        $response->assertSessionHasErrors(['data_pagamento', 'forma_pagamento']);
    }

    public function test_cria_despesa_paga(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar', 'financeiro.criar']);
        $categoria = CategoriaFinanceira::firstOrCreate(['slug' => 'despesa_operacional'], ['nome' => 'Despesa operacional', 'tipo' => 'saida', 'ativa' => true]);

        $response = $this->actingAs($user)->post(route('financeiro.lancamentos.despesas.store'), [
            'categoria_financeira_id' => $categoria->id,
            'descricao' => 'Conta de energia',
            'valor' => 180,
            'status' => 'pago',
            'data_pagamento' => now()->toDateString(),
            'forma_pagamento' => 'pix',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lancamentos_financeiros', [
            'descricao' => 'Conta de energia',
            'tipo' => LancamentoFinanceiro::TIPO_SAIDA,
            'status' => LancamentoFinanceiro::STATUS_PAGO,
            'origem' => LancamentoFinanceiro::ORIGEM_DESPESA,
        ]);
    }

    public function test_lista_lancamentos_com_filtro_de_status(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar']);
        $categoria = CategoriaFinanceira::firstOrCreate(['slug' => 'receita_diversa'], ['nome' => 'Receita diversa', 'tipo' => 'entrada', 'ativa' => true]);

        LancamentoFinanceiro::create([
            'codigo' => 'LF-000001',
            'tipo' => LancamentoFinanceiro::TIPO_ENTRADA,
            'categoria_financeira_id' => $categoria->id,
            'descricao' => 'Pendente',
            'valor' => 100,
            'status' => LancamentoFinanceiro::STATUS_PENDENTE,
            'origem' => LancamentoFinanceiro::ORIGEM_MANUAL,
        ]);

        LancamentoFinanceiro::create([
            'codigo' => 'LF-000002',
            'tipo' => LancamentoFinanceiro::TIPO_ENTRADA,
            'categoria_financeira_id' => $categoria->id,
            'descricao' => 'Pago',
            'valor' => 200,
            'status' => LancamentoFinanceiro::STATUS_PAGO,
            'data_pagamento' => now()->toDateString(),
            'origem' => LancamentoFinanceiro::ORIGEM_MANUAL,
        ]);

        $response = $this->actingAs($user)->get(route('financeiro.lancamentos.index', ['status' => 'pendente']));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Financeiro/Lancamentos/Index')
            ->has('lancamentos.data', 1)
        );
    }
}

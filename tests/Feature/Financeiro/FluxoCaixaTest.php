<?php

namespace Tests\Feature\Financeiro;

use App\Models\CategoriaFinanceira;
use App\Models\LancamentoFinanceiro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Contratos\ContratoTestHelpers;
use Tests\TestCase;

class FluxoCaixaTest extends TestCase
{
    use RefreshDatabase, ContratoTestHelpers;

    public function test_exibe_fluxo_de_caixa_do_periodo(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar']);
        $categoria = CategoriaFinanceira::firstOrCreate(['slug' => 'receita_diversa'], ['nome' => 'Receita diversa', 'tipo' => 'entrada', 'ativa' => true]);

        LancamentoFinanceiro::create([
            'codigo' => 'LF-000001',
            'tipo' => LancamentoFinanceiro::TIPO_ENTRADA,
            'categoria_financeira_id' => $categoria->id,
            'valor' => 800,
            'data_pagamento' => now()->toDateString(),
            'status' => LancamentoFinanceiro::STATUS_PAGO,
            'origem' => LancamentoFinanceiro::ORIGEM_RECEITA_DIVERSA,
        ]);

        $response = $this->actingAs($user)->get(route('financeiro.fluxo-caixa'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Financeiro/FluxoCaixa')
            ->has('fluxo')
        );
    }
}

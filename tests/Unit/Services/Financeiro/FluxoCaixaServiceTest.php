<?php

namespace Tests\Unit\Services\Financeiro;

use App\Models\CategoriaFinanceira;
use App\Models\LancamentoFinanceiro;
use App\Services\Financeiro\FluxoCaixaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FluxoCaixaServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_agrupa_previstos_e_realizados_por_dia(): void
    {
        $categoria = CategoriaFinanceira::firstOrCreate(['slug' => 'aluguel'], ['nome' => 'Aluguel', 'tipo' => 'entrada', 'ativa' => true]);
        $dia = now()->toDateString();

        LancamentoFinanceiro::create([
            'codigo' => 'LF-000001',
            'tipo' => LancamentoFinanceiro::TIPO_ENTRADA,
            'categoria_financeira_id' => $categoria->id,
            'valor' => 500,
            'data_vencimento' => $dia,
            'status' => LancamentoFinanceiro::STATUS_PENDENTE,
            'origem' => LancamentoFinanceiro::ORIGEM_MANUAL,
        ]);

        LancamentoFinanceiro::create([
            'codigo' => 'LF-000002',
            'tipo' => LancamentoFinanceiro::TIPO_ENTRADA,
            'categoria_financeira_id' => $categoria->id,
            'valor' => 300,
            'data_pagamento' => $dia,
            'status' => LancamentoFinanceiro::STATUS_PAGO,
            'origem' => LancamentoFinanceiro::ORIGEM_MANUAL,
        ]);

        $fluxo = (new FluxoCaixaService())->calcular(now()->subDay()->toDateString(), now()->addDay()->toDateString());

        $bucket = $fluxo->firstWhere('data', $dia);

        $this->assertNotNull($bucket);
        $this->assertSame(500.0, $bucket['entradas_previstas']);
        $this->assertSame(300.0, $bucket['entradas_realizadas']);
        $this->assertSame(500.0, $bucket['saldo_previsto']);
        $this->assertSame(300.0, $bucket['saldo_realizado']);
    }
}

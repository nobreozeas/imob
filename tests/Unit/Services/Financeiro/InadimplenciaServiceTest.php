<?php

namespace Tests\Unit\Services\Financeiro;

use App\Models\ParcelaAluguel;
use App\Services\Financeiro\InadimplenciaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Contratos\ContratoTestHelpers;
use Tests\TestCase;

class InadimplenciaServiceTest extends TestCase
{
    use RefreshDatabase, ContratoTestHelpers;

    public function test_lista_e_calcula_indicadores_de_parcelas_vencidas(): void
    {
        $contrato = $this->criarContrato();

        ParcelaAluguel::create([
            'contrato_id' => $contrato->id,
            'mes_referencia' => now()->month,
            'ano_referencia' => now()->year,
            'data_vencimento' => now()->subDays(10)->toDateString(),
            'valor_aluguel' => 1500,
            'valor_total' => 1500,
            'valor_pago' => 0,
            'status' => ParcelaAluguel::STATUS_PENDENTE,
        ]);

        ParcelaAluguel::create([
            'contrato_id' => $contrato->id,
            'mes_referencia' => now()->addMonth()->month,
            'ano_referencia' => now()->addMonth()->year,
            'data_vencimento' => now()->addDays(10)->toDateString(),
            'valor_aluguel' => 1500,
            'valor_total' => 1500,
            'status' => ParcelaAluguel::STATUS_PENDENTE,
        ]);

        $service = new InadimplenciaService();

        $listagem = $service->listar();
        $indicadores = $service->indicadores();

        $this->assertCount(1, $listagem->items());
        $this->assertSame(1, $indicadores['quantidade_parcelas']);
        $this->assertSame(1500.0, $indicadores['valor_total']);
        $this->assertSame(1, $indicadores['quantidade_contratos']);
        $this->assertSame(1, $indicadores['quantidade_clientes']);
        $this->assertSame(10, $indicadores['maior_atraso_dias']);
    }
}

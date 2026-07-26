<?php

namespace Tests\Feature\Financeiro;

use App\Models\ParcelaAluguel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Contratos\ContratoTestHelpers;
use Tests\TestCase;

class InadimplenciaTest extends TestCase
{
    use RefreshDatabase, ContratoTestHelpers;

    public function test_lista_parcelas_inadimplentes(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar']);
        $contrato = $this->criarContrato();

        ParcelaAluguel::create([
            'contrato_id' => $contrato->id,
            'mes_referencia' => now()->month,
            'ano_referencia' => now()->year,
            'data_vencimento' => now()->subDays(3)->toDateString(),
            'valor_aluguel' => 1500,
            'valor_total' => 1500,
            'status' => ParcelaAluguel::STATUS_PENDENTE,
        ]);

        $response = $this->actingAs($user)->get(route('financeiro.inadimplencia'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Financeiro/Inadimplencia')
            ->has('parcelas.data', 1)
            ->where('indicadores.quantidade_parcelas', 1)
        );
    }
}

<?php

namespace Tests\Feature\Contratos;

use App\Models\ContratoLocacao;
use App\Models\ParcelaAluguel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtualizarContratosVencidosCommandTest extends TestCase
{
    use RefreshDatabase;
    use ContratoTestHelpers;

    public function test_marca_parcela_pendente_vencida_como_vencido(): void
    {
        $contrato = $this->criarContrato();

        $parcelaVencida = $contrato->parcelas()->create([
            'mes_referencia' => now()->subMonth()->month,
            'ano_referencia' => now()->subMonth()->year,
            'data_vencimento' => now()->subDays(5),
            'valor_aluguel' => 1500,
            'valor_total' => 1500,
            'status' => ParcelaAluguel::STATUS_PENDENTE,
        ]);

        $parcelaFutura = $contrato->parcelas()->create([
            'mes_referencia' => now()->addMonth()->month,
            'ano_referencia' => now()->addMonth()->year,
            'data_vencimento' => now()->addDays(10),
            'valor_aluguel' => 1500,
            'valor_total' => 1500,
            'status' => ParcelaAluguel::STATUS_PENDENTE,
        ]);

        $this->artisan('contratos:atualizar-vencidos')->assertSuccessful();

        $this->assertSame(ParcelaAluguel::STATUS_VENCIDO, $parcelaVencida->fresh()->status);
        $this->assertSame(ParcelaAluguel::STATUS_PENDENTE, $parcelaFutura->fresh()->status);
    }

    public function test_marca_contrato_ativo_com_data_fim_passada_como_vencido(): void
    {
        $contratoExpirado = $this->criarContrato([
            'status' => ContratoLocacao::STATUS_ATIVO,
            'data_fim' => now()->subDay(),
        ]);

        $contratoVigente = $this->criarContrato([
            'status' => ContratoLocacao::STATUS_ATIVO,
            'data_fim' => now()->addMonths(3),
        ]);

        $this->artisan('contratos:atualizar-vencidos')->assertSuccessful();

        $this->assertSame(ContratoLocacao::STATUS_VENCIDO, $contratoExpirado->fresh()->status);
        $this->assertSame(ContratoLocacao::STATUS_ATIVO, $contratoVigente->fresh()->status);
    }
}

<?php

namespace Tests\Unit\Services\Contratos;

use App\Models\ContratoMultas;
use App\Models\ParcelaAluguel;
use App\Services\Contratos\CalcularMultaAtrasoService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class CalcularMultaAtrasoServiceTest extends TestCase
{
    public function test_calcula_multa_e_juros_proporcionais_conforme_exemplo_do_prd(): void
    {
        $parcela = new ParcelaAluguel([
            'valor_aluguel' => 1500.00,
            'data_vencimento' => '2026-01-05',
        ]);

        $regras = new ContratoMultas([
            'possui_multa_atraso' => true,
            'percentual_multa_atraso' => 2.0,
            'valor_juros_dia' => 0.0333,
            'dias_tolerancia_atraso' => 0,
        ]);

        $resultado = (new CalcularMultaAtrasoService())->calcular($parcela, $regras, Carbon::parse('2026-01-15'));

        $this->assertSame(10, $resultado['dias_atraso']);
        $this->assertEqualsWithDelta(30.0, $resultado['multa'], 0.01);
        $this->assertEqualsWithDelta(4.995, $resultado['juros'], 0.01);
    }

    public function test_nao_aplica_multa_dentro_da_tolerancia(): void
    {
        $parcela = new ParcelaAluguel([
            'valor_aluguel' => 1500.00,
            'data_vencimento' => '2026-01-05',
        ]);

        $regras = new ContratoMultas([
            'possui_multa_atraso' => true,
            'percentual_multa_atraso' => 2.0,
            'valor_juros_dia' => 0.0333,
            'dias_tolerancia_atraso' => 5,
        ]);

        $resultado = (new CalcularMultaAtrasoService())->calcular($parcela, $regras, Carbon::parse('2026-01-08'));

        $this->assertSame(0.0, $resultado['multa']);
        $this->assertSame(0.0, $resultado['juros']);
    }

    public function test_pagamento_em_dia_nao_gera_multa(): void
    {
        $parcela = new ParcelaAluguel([
            'valor_aluguel' => 1500.00,
            'data_vencimento' => '2026-01-05',
        ]);

        $regras = new ContratoMultas([
            'possui_multa_atraso' => true,
            'percentual_multa_atraso' => 2.0,
            'valor_juros_dia' => 0.0333,
        ]);

        $resultado = (new CalcularMultaAtrasoService())->calcular($parcela, $regras, Carbon::parse('2026-01-05'));

        $this->assertSame(0.0, $resultado['multa']);
        $this->assertSame(0.0, $resultado['juros']);
    }

    public function test_sem_regras_de_multa_configuradas_nao_calcula_nada(): void
    {
        $parcela = new ParcelaAluguel([
            'valor_aluguel' => 1500.00,
            'data_vencimento' => '2026-01-05',
        ]);

        $resultado = (new CalcularMultaAtrasoService())->calcular($parcela, null, Carbon::parse('2026-01-20'));

        $this->assertSame(0.0, $resultado['multa']);
        $this->assertSame(0.0, $resultado['juros']);
    }
}

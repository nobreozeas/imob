<?php

namespace Tests\Unit\Services\Contratos;

use App\Models\ContratoLocacao;
use App\Models\ContratoMultas;
use App\Services\Contratos\CalcularMultaRescisaoService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class CalcularMultaRescisaoServiceTest extends TestCase
{
    public function test_calcula_multa_proporcional_conforme_exemplo_do_prd(): void
    {
        $contrato = new ContratoLocacao([
            'valor_aluguel' => 1500.00,
            'data_inicio' => '2026-01-01',
            'data_fim' => '2027-01-01',
        ]);
        $contrato->setRelation('multas', new ContratoMultas([
            'possui_multa_rescisao' => true,
            'percentual_multa_rescisao' => 100.0,
            'base_calculo_rescisao' => 'alugueis_restantes',
        ]));

        $resultado = (new CalcularMultaRescisaoService())->calcular($contrato, Carbon::parse('2026-07-01'));

        $this->assertSame(7, $resultado['meses_restantes']);
        $this->assertEqualsWithDelta(10500.0, $resultado['multa_proporcional'], 1.0);
    }

    public function test_sem_multa_de_rescisao_configurada_retorna_zero(): void
    {
        $contrato = new ContratoLocacao([
            'valor_aluguel' => 1500.00,
            'data_fim' => '2027-01-01',
        ]);
        $contrato->setRelation('multas', new ContratoMultas(['possui_multa_rescisao' => false]));

        $resultado = (new CalcularMultaRescisaoService())->calcular($contrato, Carbon::parse('2026-07-01'));

        $this->assertSame(0.0, $resultado['multa_proporcional']);
    }

    public function test_rescisao_apos_data_fim_nao_gera_multa(): void
    {
        $contrato = new ContratoLocacao([
            'valor_aluguel' => 1500.00,
            'data_fim' => '2026-01-01',
        ]);
        $contrato->setRelation('multas', new ContratoMultas([
            'possui_multa_rescisao' => true,
            'percentual_multa_rescisao' => 10.0,
            'base_calculo_rescisao' => 'valor_fixo',
        ]));

        $resultado = (new CalcularMultaRescisaoService())->calcular($contrato, Carbon::parse('2026-06-01'));

        $this->assertSame(0.0, $resultado['multa_proporcional']);
    }
}

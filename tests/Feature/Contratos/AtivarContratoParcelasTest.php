<?php

namespace Tests\Feature\Contratos;

use App\Models\ContratoLocacao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtivarContratoParcelasTest extends TestCase
{
    use RefreshDatabase;
    use ContratoTestHelpers;

    public function test_ativar_contrato_com_geracao_automatica_cria_uma_parcela_por_mes(): void
    {
        $contrato = $this->criarContrato([
            'status' => ContratoLocacao::STATUS_AGUARDANDO_ASSINATURA,
            'data_inicio' => '2026-01-10',
            'data_fim' => '2026-06-10',
            'dia_vencimento' => 5,
            'gerar_parcelas_automaticamente' => true,
        ]);

        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.ativar']);

        $response = $this->actingAs($user)->post(route('contratos.ativar', $contrato));

        $response->assertRedirect();
        $this->assertSame(ContratoLocacao::STATUS_ATIVO, $contrato->fresh()->status);
        $this->assertSame(6, $contrato->parcelas()->count());
        $this->assertSame(5, (int) $contrato->parcelas()->first()->data_vencimento->day);
    }

    public function test_ativar_contrato_sem_geracao_automatica_nao_cria_parcelas(): void
    {
        $contrato = $this->criarContrato([
            'status' => ContratoLocacao::STATUS_AGUARDANDO_ASSINATURA,
            'gerar_parcelas_automaticamente' => false,
        ]);

        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.ativar']);

        $this->actingAs($user)->post(route('contratos.ativar', $contrato));

        $this->assertSame(0, $contrato->parcelas()->count());
    }

    public function test_ativar_contrato_nao_duplica_parcelas_se_chamado_novamente(): void
    {
        $contrato = $this->criarContrato([
            'status' => ContratoLocacao::STATUS_ATIVO,
            'data_inicio' => '2026-01-10',
            'data_fim' => '2026-03-10',
            'dia_vencimento' => 5,
        ]);

        app(\App\Services\Contratos\GerarParcelasContratoService::class)->gerar($contrato);
        app(\App\Services\Contratos\GerarParcelasContratoService::class)->gerar($contrato);

        $this->assertSame(3, $contrato->parcelas()->count());
    }
}

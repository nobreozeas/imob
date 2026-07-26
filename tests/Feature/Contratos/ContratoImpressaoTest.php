<?php

namespace Tests\Feature\Contratos;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContratoImpressaoTest extends TestCase
{
    use RefreshDatabase;
    use ContratoTestHelpers;

    public function test_usuario_com_permissao_visualiza_impressao_do_contrato(): void
    {
        $contrato = $this->criarContrato();
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view']);

        $response = $this->actingAs($user)->get(route('contratos.imprimir', $contrato->id));

        $response->assertOk();
        $response->assertSee($contrato->numero);
    }

    public function test_usuario_sem_permissao_nao_acessa_impressao(): void
    {
        $contrato = $this->criarContrato();
        $user = $this->usuarioComPermissoes([]);

        $response = $this->actingAs($user)->get(route('contratos.imprimir', $contrato->id));

        $response->assertForbidden();
    }

    public function test_impressao_omite_secao_de_caucao_quando_nao_configurada(): void
    {
        $contrato = $this->criarContrato(['caucao' => ['possui_caucao' => false]]);
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view']);

        $response = $this->actingAs($user)->get(route('contratos.imprimir', $contrato->id));

        $response->assertOk();
        $response->assertDontSee('Da Garantia');
    }

    public function test_impressao_exibe_secao_de_caucao_quando_configurada(): void
    {
        $contrato = $this->criarContrato([
            'caucao' => [
                'possui_caucao' => true,
                'tipo_caucao' => 'dinheiro',
                'valor_caucao' => 1500,
            ],
        ]);
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view']);

        $response = $this->actingAs($user)->get(route('contratos.imprimir', $contrato->id));

        $response->assertOk();
        $response->assertSee('Da Garantia');
        $response->assertSee('1.500,00');
    }
}

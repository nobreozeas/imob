<?php

namespace Tests\Feature\Contratos;

use App\Models\LancamentoFinanceiro;
use App\Models\ParcelaAluguel;
use App\Models\RepasseProprietario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepasseProprietarioTest extends TestCase
{
    use RefreshDatabase;
    use ContratoTestHelpers;

    private function criarRepassePendente(): RepasseProprietario
    {
        $contrato = $this->criarContrato(['valor_aluguel' => 1500, 'valor_taxa_administracao' => 10]);
        $parcela = $contrato->parcelas()->create([
            'mes_referencia' => 1,
            'ano_referencia' => 2026,
            'data_vencimento' => '2026-01-05',
            'valor_aluguel' => 1500,
            'valor_total' => 1500,
            'status' => ParcelaAluguel::STATUS_PAGO,
            'valor_pago' => 1500,
        ]);

        return RepasseProprietario::create([
            'contrato_id' => $contrato->id,
            'imovel_id' => $contrato->imovel_id,
            'proprietario_id' => $contrato->proprietario_id,
            'parcela_aluguel_id' => $parcela->id,
            'valor_bruto' => 1500,
            'valor_taxa_administracao' => 150,
            'valor_liquido' => 1350,
            'status' => RepasseProprietario::STATUS_PENDENTE,
        ]);
    }

    public function test_marcar_repasse_como_pago_cria_saida_financeira(): void
    {
        $repasse = $this->criarRepassePendente();
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'repasses.visualizar', 'repasses.marcar-como-pago']);

        $response = $this->actingAs($user)->post(route('repasses-proprietarios.marcar-como-pago', $repasse), [
            'data_pagamento' => '2026-01-10',
            'forma_pagamento' => 'pix',
        ]);

        $response->assertRedirect();
        $this->assertSame(RepasseProprietario::STATUS_PAGO, $repasse->fresh()->status);
        $this->assertSame(1, LancamentoFinanceiro::where('tipo', 'saida')->whereHas('categoria', fn ($q) => $q->where('slug', 'repasse_proprietario'))->count());
    }

    public function test_cancelar_repasse_sem_motivo_e_rejeitado(): void
    {
        $repasse = $this->criarRepassePendente();
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'repasses.visualizar', 'repasses.marcar-como-pago']);

        $response = $this->actingAs($user)->post(route('repasses-proprietarios.cancelar', $repasse), []);

        $response->assertSessionHasErrors('motivo');
        $this->assertSame(RepasseProprietario::STATUS_PENDENTE, $repasse->fresh()->status);
    }

    public function test_cancelar_repasse_com_motivo_e_aceito(): void
    {
        $repasse = $this->criarRepassePendente();
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'repasses.visualizar', 'repasses.marcar-como-pago']);

        $response = $this->actingAs($user)->post(route('repasses-proprietarios.cancelar', $repasse), [
            'motivo' => 'Pagamento estornado pelo inquilino',
        ]);

        $response->assertRedirect();
        $this->assertSame(RepasseProprietario::STATUS_CANCELADO, $repasse->fresh()->status);
    }
}

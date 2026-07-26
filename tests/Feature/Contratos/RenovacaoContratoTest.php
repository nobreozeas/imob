<?php

namespace Tests\Feature\Contratos;

use App\Models\ContratoLocacao;
use App\Models\ContratoRenovacao;
use App\Models\ParcelaAluguel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RenovacaoContratoTest extends TestCase
{
    use RefreshDatabase;
    use ContratoTestHelpers;

    public function test_renovacao_cria_novo_contrato_vinculado_ao_original_e_encerra_o_original(): void
    {
        $contrato = $this->criarContrato([
            'status' => ContratoLocacao::STATUS_VENCIDO,
            'valor_aluguel' => 1500,
            'data_inicio' => '2026-01-01',
            'data_fim' => '2027-01-01',
        ]);
        $parcelaPaga = $contrato->parcelas()->create([
            'mes_referencia' => 1, 'ano_referencia' => 2026,
            'data_vencimento' => '2026-01-05', 'valor_aluguel' => 1500, 'valor_total' => 1500,
            'status' => ParcelaAluguel::STATUS_PAGO, 'valor_pago' => 1500,
        ]);

        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.renovar']);

        $response = $this->actingAs($user)->post(route('contratos.renovar', $contrato), [
            'data_renovacao' => '2027-01-01',
            'nova_data_inicio' => '2027-01-01',
            'nova_data_fim' => '2028-01-01',
            'valor_aluguel_novo' => '1650',
            'manter_encargos' => true,
            'manter_regras_multa' => true,
            'gerar_novas_parcelas' => true,
        ]);

        $response->assertRedirect();
        $contrato->refresh();
        $this->assertSame(ContratoLocacao::STATUS_ENCERRADO, $contrato->status);

        $renovacao = ContratoRenovacao::where('contrato_original_id', $contrato->id)->firstOrFail();
        $novoContrato = ContratoLocacao::findOrFail($renovacao->novo_contrato_id);

        $this->assertSame($contrato->id, $novoContrato->contrato_anterior_id);
        $this->assertSame('1650.00', $novoContrato->valor_aluguel);
        $this->assertSame(ContratoLocacao::STATUS_ATIVO, $novoContrato->status);

        // Histórico financeiro do contrato original é preservado
        $this->assertTrue($contrato->parcelas()->whereKey($parcelaPaga->id)->exists());
        $this->assertSame(ParcelaAluguel::STATUS_PAGO, $parcelaPaga->fresh()->status);
    }

    public function test_renovacao_copia_encargos_quando_solicitado(): void
    {
        $contrato = $this->criarContrato(['status' => ContratoLocacao::STATUS_ATIVO]);
        $contrato->encargos()->create([
            'tipo_encargo' => 'condominio',
            'responsavel' => 'inquilino',
            'valor_estimado' => 300,
            'cobrar_junto_aluguel' => true,
        ]);

        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.renovar']);

        $response = $this->actingAs($user)->post(route('contratos.renovar', $contrato), [
            'data_renovacao' => '2026-07-01',
            'nova_data_inicio' => '2026-07-01',
            'manter_encargos' => true,
            'gerar_novas_parcelas' => false,
        ]);

        $response->assertRedirect();
        $renovacao = ContratoRenovacao::where('contrato_original_id', $contrato->id)->firstOrFail();
        $novoContrato = ContratoLocacao::findOrFail($renovacao->novo_contrato_id);

        $this->assertSame(1, $novoContrato->encargos()->count());
        $this->assertSame('condominio', $novoContrato->encargos()->first()->tipo_encargo);
    }

    public function test_renovacao_gera_novas_parcelas_quando_solicitado(): void
    {
        $contrato = $this->criarContrato(['status' => ContratoLocacao::STATUS_ATIVO]);
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.renovar']);

        $this->actingAs($user)->post(route('contratos.renovar', $contrato), [
            'data_renovacao' => '2026-07-01',
            'nova_data_inicio' => '2026-07-01',
            'nova_data_fim' => '2026-09-01',
            'gerar_novas_parcelas' => true,
        ]);

        $renovacao = ContratoRenovacao::where('contrato_original_id', $contrato->id)->firstOrFail();
        $novoContrato = ContratoLocacao::findOrFail($renovacao->novo_contrato_id);

        $this->assertSame(3, $novoContrato->parcelas()->count());
    }
}

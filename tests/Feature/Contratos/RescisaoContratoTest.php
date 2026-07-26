<?php

namespace Tests\Feature\Contratos;

use App\Models\ContratoLocacao;
use App\Models\ContratoRescisao;
use App\Models\Imovel;
use App\Models\ParcelaAluguel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RescisaoContratoTest extends TestCase
{
    use RefreshDatabase;
    use ContratoTestHelpers;

    public function test_rescisao_calcula_multa_proporcional(): void
    {
        $contrato = $this->criarContrato([
            'status' => ContratoLocacao::STATUS_ATIVO,
            'valor_aluguel' => 1500,
            'data_inicio' => '2026-01-01',
            'data_fim' => '2027-01-01',
            'multas' => [
                'possui_multa_atraso' => false,
                'possui_multa_rescisao' => true,
                'percentual_multa_rescisao' => 100.0,
                'base_calculo_rescisao' => 'alugueis_restantes',
            ],
        ]);

        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.rescindir']);

        $response = $this->actingAs($user)->post(route('contratos.rescindir', $contrato), [
            'data_rescisao' => '2026-07-01',
            'motivo' => 'Inquilino solicitou saída antecipada',
            'solicitado_por' => 'locatario',
            'destino_imovel' => 'disponivel',
            'acao_parcelas_futuras' => 'manter_parcelas_futuras',
        ]);

        $response->assertRedirect();
        $this->assertSame(ContratoLocacao::STATUS_RESCINDIDO, $contrato->fresh()->status);

        $rescisao = ContratoRescisao::where('contrato_id', $contrato->id)->firstOrFail();
        $this->assertSame(7, $rescisao->meses_restantes);
        $this->assertEqualsWithDelta(10500.0, (float) $rescisao->valor_multa_rescisao, 1.0);
    }

    public function test_rescisao_soma_parcelas_vencidas_em_debitos_em_aberto(): void
    {
        $contrato = $this->criarContrato(['status' => ContratoLocacao::STATUS_ATIVO]);
        $contrato->parcelas()->create([
            'mes_referencia' => 1, 'ano_referencia' => 2026,
            'data_vencimento' => '2026-01-05', 'valor_aluguel' => 1500, 'valor_total' => 1500,
            'status' => ParcelaAluguel::STATUS_VENCIDO,
        ]);
        $contrato->parcelas()->create([
            'mes_referencia' => 2, 'ano_referencia' => 2026,
            'data_vencimento' => '2026-02-05', 'valor_aluguel' => 1500, 'valor_total' => 1500,
            'status' => ParcelaAluguel::STATUS_PAGO, 'valor_pago' => 1500,
        ]);

        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.rescindir']);

        $this->actingAs($user)->post(route('contratos.rescindir', $contrato), [
            'data_rescisao' => '2026-03-01',
            'motivo' => 'Inadimplência',
            'solicitado_por' => 'imobiliaria',
            'destino_imovel' => 'disponivel',
            'acao_parcelas_futuras' => 'manter_parcelas_futuras',
        ]);

        $rescisao = ContratoRescisao::where('contrato_id', $contrato->id)->firstOrFail();
        $this->assertSame('1500.00', $rescisao->debitos_em_aberto);
    }

    public function test_rescisao_permite_abater_debitos_com_caucao(): void
    {
        $contrato = $this->criarContrato([
            'status' => ContratoLocacao::STATUS_ATIVO,
            'caucao' => ['possui_caucao' => true, 'tipo_caucao' => 'dinheiro', 'valor_caucao' => 1500],
        ]);
        $caucao = $contrato->caucao()->first();
        app(\App\Services\Contratos\MovimentacaoCaucaoService::class)->registrar(
            $caucao, 'recebimento', 1500.0, ['data_movimentacao' => '2026-01-01'],
        );

        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.rescindir']);

        $this->actingAs($user)->post(route('contratos.rescindir', $contrato), [
            'data_rescisao' => '2026-06-01',
            'motivo' => 'Débitos em aberto abatidos com a caução',
            'solicitado_por' => 'imobiliaria',
            'destino_imovel' => 'disponivel',
            'acao_parcelas_futuras' => 'manter_parcelas_futuras',
            'valor_caucao_abatida' => '500',
        ]);

        $this->assertSame('1000.00', $caucao->fresh()->saldo_atual);
        $this->assertSame(1, $caucao->movimentacoes()->where('tipo_movimentacao', 'abatimento')->count());
    }

    public function test_rescisao_cancela_parcelas_futuras_quando_solicitado(): void
    {
        $contrato = $this->criarContrato(['status' => ContratoLocacao::STATUS_ATIVO]);
        $parcelaFutura = $contrato->parcelas()->create([
            'mes_referencia' => 12, 'ano_referencia' => 2026,
            'data_vencimento' => '2026-12-05', 'valor_aluguel' => 1500, 'valor_total' => 1500,
            'status' => ParcelaAluguel::STATUS_PENDENTE,
        ]);

        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.rescindir']);

        $this->actingAs($user)->post(route('contratos.rescindir', $contrato), [
            'data_rescisao' => '2026-06-01',
            'motivo' => 'Rescisão amigável',
            'solicitado_por' => 'acordo',
            'destino_imovel' => 'disponivel',
            'acao_parcelas_futuras' => 'cancelar_parcelas_futuras',
        ]);

        $this->assertSame(ParcelaAluguel::STATUS_CANCELADO, $parcelaFutura->fresh()->status);
    }

    public function test_rescisao_muda_imovel_conforme_destino_escolhido(): void
    {
        $contrato = $this->criarContrato(['status' => ContratoLocacao::STATUS_ATIVO]);
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.rescindir']);

        $this->actingAs($user)->post(route('contratos.rescindir', $contrato), [
            'data_rescisao' => '2026-06-01',
            'motivo' => 'Imóvel será reformado',
            'solicitado_por' => 'locador',
            'destino_imovel' => 'inativo',
            'acao_parcelas_futuras' => 'manter_parcelas_futuras',
        ]);

        $this->assertSame(Imovel::STATUS_INATIVO, $contrato->imovel()->first()->fresh()->status);
    }

    public function test_rescisao_exige_motivo(): void
    {
        $contrato = $this->criarContrato(['status' => ContratoLocacao::STATUS_ATIVO]);
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.rescindir']);

        $response = $this->actingAs($user)->post(route('contratos.rescindir', $contrato), [
            'data_rescisao' => '2026-06-01',
            'solicitado_por' => 'locador',
            'destino_imovel' => 'disponivel',
            'acao_parcelas_futuras' => 'manter_parcelas_futuras',
        ]);

        $response->assertSessionHasErrors('motivo');
        $this->assertSame(ContratoLocacao::STATUS_ATIVO, $contrato->fresh()->status);
    }
}

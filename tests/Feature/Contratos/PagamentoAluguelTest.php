<?php

namespace Tests\Feature\Contratos;

use App\Models\LancamentoFinanceiro;
use App\Models\ParcelaAluguel;
use App\Models\RepasseProprietario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagamentoAluguelTest extends TestCase
{
    use RefreshDatabase;
    use ContratoTestHelpers;

    private function criarParcela(array $overrides = []): ParcelaAluguel
    {
        $contrato = $overrides['contrato'] ?? $this->criarContrato([
            'valor_aluguel' => 1500,
            'tipo_taxa_administracao' => 'percentual',
            'valor_taxa_administracao' => 10,
            'multas' => [
                'possui_multa_atraso' => true,
                'percentual_multa_atraso' => 2.0,
                'valor_juros_dia' => 0.0333,
                'dias_tolerancia_atraso' => 0,
            ],
        ]);

        return $contrato->parcelas()->create([
            'mes_referencia' => 1,
            'ano_referencia' => 2026,
            'data_vencimento' => '2026-01-05',
            'valor_aluguel' => 1500,
            'valor_total' => 1500,
            'status' => ParcelaAluguel::STATUS_PENDENTE,
        ]);
    }

    public function test_pagamento_em_dia_nao_gera_multa_nem_juros(): void
    {
        $parcela = $this->criarParcela();
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.registrar-pagamento']);

        $response = $this->actingAs($user)->post(route('contratos.parcelas.pagamento', [$parcela->contrato_id, $parcela->id]), [
            'data_pagamento' => '2026-01-05',
            'forma_pagamento' => 'pix',
            'valor_pago' => '1500',
        ]);

        $response->assertRedirect();
        $parcela->refresh();
        $this->assertSame(ParcelaAluguel::STATUS_PAGO, $parcela->status);
        $this->assertSame('0.00', $parcela->valor_multa_atraso);
        $this->assertSame('0.00', $parcela->valor_juros_atraso);
    }

    public function test_pagamento_em_atraso_calcula_multa_e_juros(): void
    {
        $parcela = $this->criarParcela();
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.registrar-pagamento']);

        $response = $this->actingAs($user)->post(route('contratos.parcelas.pagamento', [$parcela->contrato_id, $parcela->id]), [
            'data_pagamento' => '2026-01-15',
            'forma_pagamento' => 'pix',
            'valor_pago' => '1535',
        ]);

        $response->assertRedirect();
        $parcela->refresh();
        $this->assertSame(ParcelaAluguel::STATUS_PAGO, $parcela->status);
        $this->assertEqualsWithDelta(30.0, (float) $parcela->valor_multa_atraso, 0.01);
        $this->assertEqualsWithDelta(4.995, (float) $parcela->valor_juros_atraso, 0.01);
    }

    public function test_pagamento_parcial_muda_status_para_pago_parcial(): void
    {
        $parcela = $this->criarParcela();
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.registrar-pagamento']);

        $this->actingAs($user)->post(route('contratos.parcelas.pagamento', [$parcela->contrato_id, $parcela->id]), [
            'data_pagamento' => '2026-01-05',
            'forma_pagamento' => 'pix',
            'valor_pago' => '1000',
        ]);

        $this->assertSame(ParcelaAluguel::STATUS_PAGO_PARCIAL, $parcela->fresh()->status);
    }

    public function test_nao_permite_repagar_parcela_ja_paga(): void
    {
        $parcela = $this->criarParcela();
        $parcela->update(['status' => ParcelaAluguel::STATUS_PAGO]);
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.registrar-pagamento']);

        $response = $this->actingAs($user)->post(route('contratos.parcelas.pagamento', [$parcela->contrato_id, $parcela->id]), [
            'data_pagamento' => '2026-01-05',
            'forma_pagamento' => 'pix',
            'valor_pago' => '1500',
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_pagamento_cria_movimentacao_financeira_e_repasse_pendente_sem_encargos_no_bruto(): void
    {
        $contrato = $this->criarContrato([
            'valor_aluguel' => 1500,
            'tipo_taxa_administracao' => 'percentual',
            'valor_taxa_administracao' => 10,
        ]);
        $contrato->encargos()->create([
            'tipo_encargo' => 'condominio',
            'responsavel' => 'inquilino',
            'valor_estimado' => 200,
            'cobrar_junto_aluguel' => true,
        ]);
        $parcela = $contrato->parcelas()->create([
            'mes_referencia' => 1,
            'ano_referencia' => 2026,
            'data_vencimento' => '2026-01-05',
            'valor_aluguel' => 1500,
            'valor_encargos' => 200,
            'valor_total' => 1700,
            'status' => ParcelaAluguel::STATUS_PENDENTE,
        ]);

        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.registrar-pagamento']);

        $this->actingAs($user)->post(route('contratos.parcelas.pagamento', [$contrato->id, $parcela->id]), [
            'data_pagamento' => '2026-01-05',
            'forma_pagamento' => 'pix',
            'valor_pago' => '1700',
        ]);

        $this->assertSame(1, LancamentoFinanceiro::where('tipo', 'entrada')->whereHas('categoria', fn ($q) => $q->where('slug', 'aluguel'))->count());

        $lancamentoTaxa = LancamentoFinanceiro::where('tipo', 'entrada')->whereHas('categoria', fn ($q) => $q->where('slug', 'taxa_administracao'))->first();
        $this->assertNotNull($lancamentoTaxa);
        $this->assertSame('150.00', $lancamentoTaxa->valor);
        $this->assertSame($parcela->id, $lancamentoTaxa->parcela_aluguel_id);

        $repasse = RepasseProprietario::where('parcela_aluguel_id', $parcela->id)->first();
        $this->assertNotNull($repasse);
        $this->assertSame(RepasseProprietario::STATUS_PENDENTE, $repasse->status);
        $this->assertSame('1500.00', $repasse->valor_bruto);
        $this->assertSame('150.00', $repasse->valor_taxa_administracao);
        $this->assertSame('1350.00', $repasse->valor_liquido);
    }
}

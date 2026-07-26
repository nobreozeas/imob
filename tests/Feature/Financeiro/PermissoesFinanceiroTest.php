<?php

namespace Tests\Feature\Financeiro;

use App\Models\CategoriaFinanceira;
use App\Models\LancamentoFinanceiro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Contratos\ContratoTestHelpers;
use Tests\TestCase;

class PermissoesFinanceiroTest extends TestCase
{
    use RefreshDatabase, ContratoTestHelpers;

    public function test_usuario_sem_permissao_recebe_403_em_cada_acao(): void
    {
        $user = $this->usuarioComPermissoes([]);
        $categoria = CategoriaFinanceira::firstOrCreate(['slug' => 'despesa_operacional'], ['nome' => 'Despesa operacional', 'tipo' => 'saida', 'ativa' => true]);
        $lancamento = LancamentoFinanceiro::create([
            'codigo' => 'LF-000001',
            'tipo' => LancamentoFinanceiro::TIPO_SAIDA,
            'categoria_financeira_id' => $categoria->id,
            'valor' => 100,
            'status' => LancamentoFinanceiro::STATUS_PENDENTE,
            'origem' => LancamentoFinanceiro::ORIGEM_MANUAL,
        ]);

        $this->actingAs($user)->get(route('financeiro.dashboard'))->assertForbidden();
        $this->actingAs($user)->get(route('financeiro.lancamentos.index'))->assertForbidden();
        $this->actingAs($user)->post(route('financeiro.lancamentos.despesas.store'), [
            'categoria_financeira_id' => $categoria->id,
            'descricao' => 'x',
            'valor' => 10,
            'status' => 'pendente',
        ])->assertForbidden();
        $this->actingAs($user)->post(route('financeiro.lancamentos.marcar-como-pago', $lancamento), [
            'data_pagamento' => now()->toDateString(),
            'forma_pagamento' => 'pix',
        ])->assertForbidden();
        $this->actingAs($user)->post(route('financeiro.lancamentos.cancelar', $lancamento), ['motivo' => 'x'])->assertForbidden();
        $this->actingAs($user)->post(route('financeiro.lancamentos.estornar', $lancamento), ['motivo' => 'x'])->assertForbidden();
        $this->actingAs($user)->get(route('financeiro.fluxo-caixa'))->assertForbidden();
        $this->actingAs($user)->get(route('financeiro.inadimplencia'))->assertForbidden();
        $this->actingAs($user)->get(route('financeiro.relatorios'))->assertForbidden();
        $this->actingAs($user)->post(route('financeiro.categorias.store'), ['nome' => 'x', 'tipo' => 'entrada'])->assertForbidden();
    }

    public function test_usuario_com_todas_as_permissoes_financeiras_acessa_tudo(): void
    {
        $user = $this->usuarioComPermissoes([
            'financeiro.visualizar',
            'financeiro.criar',
            'financeiro.editar',
            'financeiro.excluir',
            'financeiro.cancelar',
            'financeiro.estornar',
            'financeiro.marcar_como_pago',
            'financeiro.visualizar_relatorios',
        ]);

        $this->actingAs($user)->get(route('financeiro.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('financeiro.lancamentos.index'))->assertOk();
        $this->actingAs($user)->get(route('financeiro.fluxo-caixa'))->assertOk();
        $this->actingAs($user)->get(route('financeiro.inadimplencia'))->assertOk();
        $this->actingAs($user)->get(route('financeiro.relatorios'))->assertOk();
        $this->actingAs($user)->get(route('financeiro.categorias.index'))->assertOk();
    }
}

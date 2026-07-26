<?php

namespace Tests\Feature\Financeiro;

use App\Models\CategoriaFinanceira;
use App\Models\LancamentoFinanceiro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Contratos\ContratoTestHelpers;
use Tests\TestCase;

class AcoesLancamentoFinanceiroTest extends TestCase
{
    use RefreshDatabase, ContratoTestHelpers;

    private function criarLancamento(string $status = LancamentoFinanceiro::STATUS_PENDENTE, string $origem = LancamentoFinanceiro::ORIGEM_MANUAL): LancamentoFinanceiro
    {
        $categoria = CategoriaFinanceira::firstOrCreate(['slug' => 'despesa_operacional'], ['nome' => 'Despesa operacional', 'tipo' => 'saida', 'ativa' => true]);

        return LancamentoFinanceiro::create([
            'codigo' => 'LF-' . uniqid(),
            'tipo' => LancamentoFinanceiro::TIPO_SAIDA,
            'categoria_financeira_id' => $categoria->id,
            'descricao' => 'Despesa teste',
            'valor' => 300,
            'status' => $status,
            'origem' => $origem,
        ]);
    }

    public function test_marca_lancamento_pendente_como_pago(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar', 'financeiro.marcar_como_pago']);
        $lancamento = $this->criarLancamento();

        $response = $this->actingAs($user)->post(route('financeiro.lancamentos.marcar-como-pago', $lancamento), [
            'data_pagamento' => now()->toDateString(),
            'forma_pagamento' => 'pix',
        ]);

        $response->assertRedirect();
        $this->assertSame(LancamentoFinanceiro::STATUS_PAGO, $lancamento->fresh()->status);
    }

    public function test_cancelamento_sem_motivo_e_rejeitado(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar', 'financeiro.cancelar']);
        $lancamento = $this->criarLancamento();

        $response = $this->actingAs($user)->post(route('financeiro.lancamentos.cancelar', $lancamento), []);

        $response->assertSessionHasErrors('motivo');
        $this->assertSame(LancamentoFinanceiro::STATUS_PENDENTE, $lancamento->fresh()->status);
    }

    public function test_cancelamento_com_motivo_e_aceito(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar', 'financeiro.cancelar']);
        $lancamento = $this->criarLancamento();

        $response = $this->actingAs($user)->post(route('financeiro.lancamentos.cancelar', $lancamento), [
            'motivo' => 'Lançamento duplicado',
        ]);

        $response->assertRedirect();
        $lancamento->refresh();
        $this->assertSame(LancamentoFinanceiro::STATUS_CANCELADO, $lancamento->status);
        $this->assertSame('Lançamento duplicado', $lancamento->motivo_cancelamento);
    }

    public function test_estorno_sem_motivo_e_rejeitado(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar', 'financeiro.estornar']);
        $lancamento = $this->criarLancamento(LancamentoFinanceiro::STATUS_PAGO);

        $response = $this->actingAs($user)->post(route('financeiro.lancamentos.estornar', $lancamento), []);

        $response->assertSessionHasErrors('motivo');
        $this->assertSame(LancamentoFinanceiro::STATUS_PAGO, $lancamento->fresh()->status);
    }

    public function test_estorno_com_motivo_e_aceito(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar', 'financeiro.estornar']);
        $lancamento = $this->criarLancamento(LancamentoFinanceiro::STATUS_PAGO);

        $response = $this->actingAs($user)->post(route('financeiro.lancamentos.estornar', $lancamento), [
            'motivo' => 'Pagamento revertido pelo banco',
        ]);

        $response->assertRedirect();
        $this->assertSame(LancamentoFinanceiro::STATUS_ESTORNADO, $lancamento->fresh()->status);
    }

    public function test_lancamento_automatico_nao_pode_ser_editado(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar', 'financeiro.editar']);
        $lancamento = $this->criarLancamento(LancamentoFinanceiro::STATUS_PAGO, LancamentoFinanceiro::ORIGEM_PAGAMENTO_ALUGUEL);

        $response = $this->actingAs($user)->put(route('financeiro.lancamentos.update', $lancamento), [
            'categoria_financeira_id' => $lancamento->categoria_financeira_id,
            'descricao' => 'Tentando editar',
            'valor' => 999,
        ]);

        $response->assertForbidden();
    }
}

<?php

namespace Tests\Feature\Contratos;

use App\Models\ContratoCaucao;
use App\Models\ContratoLocacao;
use App\Models\LancamentoFinanceiro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovimentacaoCaucaoTest extends TestCase
{
    use RefreshDatabase;
    use ContratoTestHelpers;

    private function criarContratoComCaucao(): ContratoLocacao
    {
        return $this->criarContrato([
            'caucao' => [
                'possui_caucao' => true,
                'tipo_caucao' => 'dinheiro',
                'valor_caucao' => 3000,
            ],
        ]);
    }

    public function test_recebimento_de_caucao_atualiza_saldo_e_cria_entrada_financeira(): void
    {
        $contrato = $this->criarContratoComCaucao();
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.gerenciar-caucao']);

        $response = $this->actingAs($user)->post(route('contratos.caucao.movimentacoes', $contrato), [
            'tipo_movimentacao' => 'recebimento',
            'valor' => '3000',
            'data_movimentacao' => '2026-01-01',
            'forma_movimentacao' => 'pix',
        ]);

        $response->assertRedirect();
        $caucao = $contrato->caucao()->first();
        $this->assertSame('3000.00', $caucao->saldo_atual);
        $this->assertSame(ContratoCaucao::STATUS_RECEBIDA, $caucao->status_caucao);
        $this->assertSame(1, LancamentoFinanceiro::where('tipo', 'entrada')->whereHas('categoria', fn ($q) => $q->where('slug', 'caucao'))->count());
    }

    public function test_retencao_parcial_reduz_saldo(): void
    {
        $contrato = $this->criarContratoComCaucao();
        $caucao = $contrato->caucao()->first();
        app(\App\Services\Contratos\MovimentacaoCaucaoService::class)->registrar(
            $caucao, 'recebimento', 3000.0, ['data_movimentacao' => '2026-01-01'],
        );

        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.gerenciar-caucao']);
        $this->actingAs($user)->post(route('contratos.caucao.movimentacoes', $contrato), [
            'tipo_movimentacao' => 'retencao_parcial',
            'valor' => '1000',
            'data_movimentacao' => '2026-06-01',
        ]);

        $caucao->refresh();
        $this->assertSame('2000.00', $caucao->saldo_atual);
        $this->assertSame(ContratoCaucao::STATUS_RETIDA_PARCIALMENTE, $caucao->status_caucao);
    }

    public function test_devolucao_total_zera_saldo_e_cria_saida_financeira(): void
    {
        $contrato = $this->criarContratoComCaucao();
        $caucao = $contrato->caucao()->first();
        app(\App\Services\Contratos\MovimentacaoCaucaoService::class)->registrar(
            $caucao, 'recebimento', 3000.0, ['data_movimentacao' => '2026-01-01'],
        );

        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.gerenciar-caucao']);
        $this->actingAs($user)->post(route('contratos.caucao.movimentacoes', $contrato), [
            'tipo_movimentacao' => 'devolucao',
            'valor' => '3000',
            'data_movimentacao' => '2026-06-01',
        ]);

        $caucao->refresh();
        $this->assertSame('0.00', $caucao->saldo_atual);
        $this->assertSame(ContratoCaucao::STATUS_DEVOLVIDA, $caucao->status_caucao);
        $this->assertSame(1, LancamentoFinanceiro::where('tipo', 'saida')->whereHas('categoria', fn ($q) => $q->where('slug', 'devolucao_caucao'))->count());
    }

    public function test_toda_movimentacao_cria_historico_consultavel(): void
    {
        $contrato = $this->criarContratoComCaucao();
        $caucao = $contrato->caucao()->first();
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.gerenciar-caucao']);

        $this->actingAs($user)->post(route('contratos.caucao.movimentacoes', $contrato), [
            'tipo_movimentacao' => 'recebimento',
            'valor' => '3000',
            'data_movimentacao' => '2026-01-01',
            'descricao' => 'Recebido via PIX na assinatura',
        ]);

        $this->assertSame(1, $caucao->movimentacoes()->count());
        $movimentacao = $caucao->movimentacoes()->first();
        $this->assertSame($user->id, $movimentacao->criado_por);
        $this->assertSame('Recebido via PIX na assinatura', $movimentacao->descricao);
    }
}

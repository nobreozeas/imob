<?php

namespace Tests\Feature\Contratos;

use App\Models\ContratoLocacao;
use App\Models\ParcelaAluguel;
use App\Models\RepasseProprietario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissoesContratoFinanceiroTest extends TestCase
{
    use RefreshDatabase;
    use ContratoTestHelpers;

    private function usuarioSemPermissoes(): User
    {
        return User::factory()->create(['deve_alterar_senha' => false]);
    }

    public function test_usuario_sem_permissao_nao_consegue_registrar_pagamento(): void
    {
        $contrato = $this->criarContrato(['status' => ContratoLocacao::STATUS_ATIVO]);
        $parcela = $contrato->parcelas()->create([
            'mes_referencia' => 1, 'ano_referencia' => 2026,
            'data_vencimento' => '2026-01-05', 'valor_aluguel' => 1500, 'valor_total' => 1500,
            'status' => ParcelaAluguel::STATUS_PENDENTE,
        ]);

        $user = $this->usuarioSemPermissoes();

        $response = $this->actingAs($user)->post(route('contratos.parcelas.pagamento', [$contrato->id, $parcela->id]), [
            'data_pagamento' => '2026-01-05',
            'forma_pagamento' => 'pix',
            'valor_pago' => '1500',
        ]);

        $response->assertForbidden();
        $this->assertSame(ParcelaAluguel::STATUS_PENDENTE, $parcela->fresh()->status);
    }

    public function test_usuario_sem_permissao_nao_consegue_movimentar_caucao(): void
    {
        $contrato = $this->criarContrato([
            'status' => ContratoLocacao::STATUS_ATIVO,
            'caucao' => ['possui_caucao' => true, 'tipo_caucao' => 'dinheiro', 'valor_caucao' => 1500],
        ]);
        $user = $this->usuarioSemPermissoes();

        $response = $this->actingAs($user)->post(route('contratos.caucao.movimentacoes', $contrato), [
            'tipo_movimentacao' => 'recebimento',
            'valor' => '1500',
            'data_movimentacao' => '2026-01-01',
        ]);

        $response->assertForbidden();
    }

    public function test_usuario_sem_permissao_nao_consegue_marcar_repasse_como_pago(): void
    {
        $contrato = $this->criarContrato(['status' => ContratoLocacao::STATUS_ATIVO]);
        $parcela = $contrato->parcelas()->create([
            'mes_referencia' => 1, 'ano_referencia' => 2026,
            'data_vencimento' => '2026-01-05', 'valor_aluguel' => 1500, 'valor_total' => 1500,
            'status' => ParcelaAluguel::STATUS_PAGO, 'valor_pago' => 1500,
        ]);
        $repasse = RepasseProprietario::create([
            'contrato_id' => $contrato->id,
            'imovel_id' => $contrato->imovel_id,
            'proprietario_id' => $contrato->proprietario_id,
            'parcela_aluguel_id' => $parcela->id,
            'valor_bruto' => 1500,
            'valor_taxa_administracao' => 150,
            'valor_liquido' => 1350,
            'status' => RepasseProprietario::STATUS_PENDENTE,
        ]);
        $user = $this->usuarioSemPermissoes();

        $response = $this->actingAs($user)->post(route('repasses-proprietarios.marcar-como-pago', $repasse), [
            'data_pagamento' => '2026-01-10',
        ]);

        $response->assertForbidden();
        $this->assertSame(RepasseProprietario::STATUS_PENDENTE, $repasse->fresh()->status);
    }

    public function test_usuario_sem_permissao_nao_consegue_renovar_contrato(): void
    {
        $contrato = $this->criarContrato(['status' => ContratoLocacao::STATUS_ATIVO]);
        $user = $this->usuarioSemPermissoes();

        $response = $this->actingAs($user)->post(route('contratos.renovar', $contrato), [
            'data_renovacao' => '2026-07-01',
            'nova_data_inicio' => '2026-07-01',
        ]);

        $response->assertForbidden();
        $this->assertSame(ContratoLocacao::STATUS_ATIVO, $contrato->fresh()->status);
    }
}

<?php

namespace Tests\Feature\Financeiro;

use App\Models\CategoriaFinanceira;
use App\Models\LancamentoFinanceiro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Contratos\ContratoTestHelpers;
use Tests\TestCase;

class CategoriaFinanceiraTest extends TestCase
{
    use RefreshDatabase, ContratoTestHelpers;

    public function test_cria_categoria_financeira(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar', 'financeiro.criar']);
        $nome = 'Serviço de vistoria ' . uniqid();

        $response = $this->actingAs($user)->post(route('financeiro.categorias.store'), [
            'nome' => $nome,
            'tipo' => 'entrada',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categorias_financeiras', [
            'nome' => $nome,
            'tipo' => 'entrada',
            'ativa' => true,
        ]);
    }

    public function test_edita_categoria_financeira(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar', 'financeiro.editar']);
        $categoria = CategoriaFinanceira::firstOrCreate(['slug' => 'antiga'], ['nome' => 'Antiga', 'tipo' => 'entrada', 'ativa' => true]);

        $response = $this->actingAs($user)->put(route('financeiro.categorias.update', $categoria), [
            'nome' => 'Nova',
            'tipo' => 'entrada',
            'slug' => 'antiga',
        ]);

        $response->assertRedirect();
        $this->assertSame('Nova', $categoria->fresh()->nome);
    }

    public function test_bloqueia_exclusao_de_categoria_em_uso(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar', 'financeiro.excluir']);
        $categoria = CategoriaFinanceira::firstOrCreate(['slug' => 'em_uso'], ['nome' => 'Em uso', 'tipo' => 'entrada', 'ativa' => true]);

        LancamentoFinanceiro::create([
            'codigo' => 'LF-000001',
            'tipo' => LancamentoFinanceiro::TIPO_ENTRADA,
            'categoria_financeira_id' => $categoria->id,
            'valor' => 100,
            'status' => LancamentoFinanceiro::STATUS_PENDENTE,
            'origem' => LancamentoFinanceiro::ORIGEM_MANUAL,
        ]);

        $response = $this->actingAs($user)->delete(route('financeiro.categorias.destroy', $categoria));

        $response->assertSessionHasErrors('categoria');
        $this->assertNotNull($categoria->fresh());
    }

    public function test_categoria_inativa_nao_pode_ser_usada_em_novo_lancamento(): void
    {
        $user = $this->usuarioComPermissoes(['financeiro.visualizar', 'financeiro.criar']);
        $categoria = CategoriaFinanceira::firstOrCreate(['slug' => 'inativa'], ['nome' => 'Inativa', 'tipo' => 'entrada', 'ativa' => false]);

        $response = $this->actingAs($user)->post(route('financeiro.lancamentos.receitas.store'), [
            'categoria_financeira_id' => $categoria->id,
            'descricao' => 'Receita teste',
            'valor' => 100,
            'status' => 'pendente',
        ]);

        $response->assertSessionHasErrors('categoria_financeira_id');
    }
}

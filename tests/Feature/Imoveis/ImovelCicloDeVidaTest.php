<?php

namespace Tests\Feature\Imoveis;

use App\Models\Cliente;
use App\Models\ClientePapel;
use App\Models\ContratoLocacao;
use App\Models\Imovel;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImovelCicloDeVidaTest extends TestCase
{
    use RefreshDatabase;

    private function usuarioComPermissoes(array $permissoes): User
    {
        $user = User::factory()->create(['deve_alterar_senha' => false]);

        foreach ($permissoes as $permissao) {
            Permission::firstOrCreate(['name' => $permissao, 'guard_name' => 'web']);
        }

        $user->givePermissionTo($permissoes);

        return $user;
    }

    private function criarProprietario(): Cliente
    {
        $cliente = Cliente::create([
            'tipo_pessoa' => 'fisica',
            'nome'        => 'Proprietário Teste',
            'cpf'         => (string) random_int(10000000000, 99999999999),
            'status'      => 'ativo',
        ]);

        ClientePapel::create(['cliente_id' => $cliente->id, 'papel' => 'proprietario']);

        return $cliente;
    }

    private function criarImovel(?Cliente $proprietario = null, string $status = Imovel::STATUS_DISPONIVEL): Imovel
    {
        $proprietario ??= $this->criarProprietario();
        $criador = User::factory()->create(['deve_alterar_senha' => false]);

        return Imovel::create([
            'codigo'          => 'IMO-' . uniqid(),
            'titulo'          => 'Casa Teste',
            'tipo'            => 'casa',
            'finalidade'      => 'aluguel',
            'status'          => $status,
            'proprietario_id' => $proprietario->id,
            'criado_por'      => $criador->id,
        ]);
    }

    private function criarContratoAtivo(Imovel $imovel, Cliente $proprietario): ContratoLocacao
    {
        $inquilino = Cliente::create([
            'tipo_pessoa' => 'fisica',
            'nome'        => 'Inquilino Teste',
            'cpf'         => (string) random_int(10000000000, 99999999999),
            'status'      => 'ativo',
        ]);
        ClientePapel::create(['cliente_id' => $inquilino->id, 'papel' => 'inquilino']);

        $criador = User::factory()->create(['deve_alterar_senha' => false]);

        return ContratoLocacao::create([
            'numero'             => 'CTR-' . uniqid(),
            'status'             => ContratoLocacao::STATUS_ATIVO,
            'tipo_contrato'      => 'residencial',
            'objetivo_contrato'  => 'aluguel',
            'imovel_id'          => $imovel->id,
            'proprietario_id'    => $proprietario->id,
            'inquilino_id'       => $inquilino->id,
            'criado_por'         => $criador->id,
            'data_inicio'        => now(),
            'dia_vencimento'     => 10,
            'valor_aluguel'      => 1500,
        ]);
    }

    public function test_nao_deve_excluir_imovel_com_contrato_ativo(): void
    {
        $proprietario = $this->criarProprietario();
        $imovel = $this->criarImovel($proprietario, Imovel::STATUS_ALUGADO);
        $this->criarContratoAtivo($imovel, $proprietario);

        $user = $this->usuarioComPermissoes(['imoveis.viewAny', 'imoveis.view', 'imoveis.destroy']);

        $response = $this->actingAs($user)->delete(route('imoveis.destroy', $imovel));

        $response->assertSessionHasErrors();
        $this->assertNotSoftDeleted($imovel);
    }

    public function test_deve_excluir_imovel_sem_contrato_ativo_e_registrar_historico(): void
    {
        $imovel = $this->criarImovel();
        $user = $this->usuarioComPermissoes(['imoveis.viewAny', 'imoveis.view', 'imoveis.destroy']);

        $response = $this->actingAs($user)->delete(route('imoveis.destroy', $imovel));

        $response->assertRedirect(route('imoveis.index'));
        $this->assertSoftDeleted($imovel);
        $this->assertDatabaseHas('imovel_historicos', [
            'imovel_id'   => $imovel->id,
            'tipo_evento' => 'exclusao',
        ]);
    }

    public function test_deve_restaurar_imovel_excluido_e_registrar_historico(): void
    {
        $imovel = $this->criarImovel();
        $imovel->delete();

        $user = $this->usuarioComPermissoes(['imoveis.restore']);

        $response = $this->actingAs($user)->patch(route('imoveis.restore', $imovel));

        $response->assertRedirect();
        $this->assertDatabaseHas('imoveis', ['id' => $imovel->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('imovel_historicos', [
            'imovel_id'   => $imovel->id,
            'tipo_evento' => 'restauracao',
        ]);
    }

    public function test_usuario_sem_permissao_nao_consegue_excluir(): void
    {
        $imovel = $this->criarImovel();
        $user = $this->usuarioComPermissoes(['imoveis.viewAny', 'imoveis.view']);

        $response = $this->actingAs($user)->delete(route('imoveis.destroy', $imovel));

        $response->assertForbidden();
        $this->assertNotSoftDeleted($imovel);
    }

    public function test_usuario_sem_permissao_nao_consegue_restaurar(): void
    {
        $imovel = $this->criarImovel();
        $imovel->delete();

        $user = $this->usuarioComPermissoes(['imoveis.viewAny', 'imoveis.view']);

        $response = $this->actingAs($user)->patch(route('imoveis.restore', $imovel));

        $response->assertForbidden();
    }

    public function test_historico_e_criado_ao_cadastrar_editar_e_alterar_status(): void
    {
        $proprietario = $this->criarProprietario();
        $user = $this->usuarioComPermissoes([
            'imoveis.viewAny', 'imoveis.view', 'imoveis.create', 'imoveis.update', 'imoveis.alterar-status',
        ]);

        $storeResponse = $this->actingAs($user)->post(route('imoveis.store'), [
            'titulo'          => 'Apto Teste',
            'tipo'            => 'apartamento',
            'finalidade'      => 'aluguel',
            'status'          => 'disponivel',
            'proprietario_id' => $proprietario->id,
        ]);

        $imovel = Imovel::where('titulo', 'Apto Teste')->firstOrFail();
        $storeResponse->assertRedirect(route('imoveis.show', $imovel));
        $this->assertDatabaseHas('imovel_historicos', ['imovel_id' => $imovel->id, 'tipo_evento' => 'criacao']);

        $updateResponse = $this->actingAs($user)->put(route('imoveis.update', $imovel), [
            'titulo'          => 'Apto Teste Atualizado',
            'tipo'            => 'apartamento',
            'finalidade'      => 'aluguel',
            'status'          => 'disponivel',
            'proprietario_id' => $proprietario->id,
        ]);
        $updateResponse->assertRedirect(route('imoveis.show', $imovel));
        $this->assertDatabaseHas('imovel_historicos', ['imovel_id' => $imovel->id, 'tipo_evento' => 'atualizacao']);

        $statusResponse = $this->actingAs($user)->patch(route('imoveis.alterar-status', $imovel), ['status' => 'reservado']);
        $statusResponse->assertRedirect();
        $this->assertDatabaseHas('imovel_historicos', ['imovel_id' => $imovel->id, 'tipo_evento' => 'alteracao_status']);
    }

    public function test_indicadores_da_listagem_refletem_contagens_e_ignoram_excluidos(): void
    {
        $proprietario = $this->criarProprietario();
        $this->criarImovel($proprietario, Imovel::STATUS_DISPONIVEL);
        $this->criarImovel($proprietario, Imovel::STATUS_ALUGADO);
        $excluido = $this->criarImovel($proprietario, Imovel::STATUS_INATIVO);
        $excluido->delete();

        $user = $this->usuarioComPermissoes(['imoveis.viewAny', 'imoveis.view']);

        $response = $this->actingAs($user)->get(route('imoveis.index'));

        $response->assertInertia(fn ($page) => $page
            ->where('indicadores.total', 2)
            ->where('indicadores.disponivel', 1)
            ->where('indicadores.alugado', 1)
            ->where('indicadores.inativo', 0)
        );
    }
}

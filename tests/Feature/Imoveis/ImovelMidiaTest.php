<?php

namespace Tests\Feature\Imoveis;

use App\Models\Cliente;
use App\Models\ClientePapel;
use App\Models\Imovel;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImovelMidiaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    private function usuarioComPermissoes(array $permissoes): User
    {
        $user = User::factory()->create(['deve_alterar_senha' => false]);

        foreach ($permissoes as $permissao) {
            Permission::firstOrCreate(['name' => $permissao, 'guard_name' => 'web']);
        }

        $user->givePermissionTo($permissoes);

        return $user;
    }

    private function criarImovel(): Imovel
    {
        $proprietario = Cliente::create([
            'tipo_pessoa' => 'fisica',
            'nome'        => 'Proprietário Teste',
            'cpf'         => (string) random_int(10000000000, 99999999999),
            'status'      => 'ativo',
        ]);
        ClientePapel::create(['cliente_id' => $proprietario->id, 'papel' => 'proprietario']);

        $criador = User::factory()->create(['deve_alterar_senha' => false]);

        return Imovel::create([
            'codigo'          => 'IMO-' . uniqid(),
            'titulo'          => 'Casa Teste',
            'tipo'            => 'casa',
            'finalidade'      => 'aluguel',
            'status'          => Imovel::STATUS_DISPONIVEL,
            'proprietario_id' => $proprietario->id,
            'criado_por'      => $criador->id,
        ]);
    }

    public function test_upload_de_foto_atualiza_galeria_e_registra_historico(): void
    {
        $imovel = $this->criarImovel();
        $user = $this->usuarioComPermissoes(['imoveis.viewAny', 'imoveis.view', 'imoveis.gerenciar-fotos']);

        $response = $this->actingAs($user)->post(route('imoveis.fotos.store', $imovel), [
            'fotos' => [UploadedFile::fake()->create('foto.jpg', 10, 'image/jpeg')],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('imovel_fotos', 1);

        $foto = $imovel->fotos()->first();
        $this->assertTrue($foto->is_principal);
        Storage::disk('public')->assertExists($foto->caminho);
        $this->assertDatabaseHas('imovel_historicos', [
            'imovel_id'   => $imovel->id,
            'tipo_evento' => 'foto_adicionada',
        ]);
    }

    public function test_remover_foto_registra_historico(): void
    {
        $imovel = $this->criarImovel();
        $user = $this->usuarioComPermissoes(['imoveis.viewAny', 'imoveis.view', 'imoveis.gerenciar-fotos']);

        $this->actingAs($user)->post(route('imoveis.fotos.store', $imovel), [
            'fotos' => [UploadedFile::fake()->create('foto.jpg', 10, 'image/jpeg')],
        ]);
        $foto = $imovel->fotos()->firstOrFail();

        $response = $this->actingAs($user)->delete(route('imoveis.fotos.destroy', [$imovel, $foto]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('imovel_fotos', ['id' => $foto->id]);
        Storage::disk('public')->assertMissing($foto->caminho);
        $this->assertDatabaseHas('imovel_historicos', [
            'imovel_id'   => $imovel->id,
            'tipo_evento' => 'foto_removida',
        ]);
    }

    public function test_definir_foto_principal_desmarca_anterior_e_primeira_foto_vira_principal_automaticamente(): void
    {
        $imovel = $this->criarImovel();
        $user = $this->usuarioComPermissoes(['imoveis.viewAny', 'imoveis.view', 'imoveis.gerenciar-fotos']);

        $this->actingAs($user)->post(route('imoveis.fotos.store', $imovel), [
            'fotos' => [UploadedFile::fake()->create('a.jpg', 10, 'image/jpeg')],
        ]);
        $primeira = $imovel->fotos()->firstOrFail();
        $this->assertTrue($primeira->fresh()->is_principal);

        $this->actingAs($user)->post(route('imoveis.fotos.store', $imovel), [
            'fotos' => [UploadedFile::fake()->create('b.jpg', 10, 'image/jpeg')],
        ]);
        $segunda = $imovel->fotos()->where('id', '!=', $primeira->id)->firstOrFail();
        $this->assertFalse($segunda->fresh()->is_principal);

        $response = $this->actingAs($user)->patch(route('imoveis.fotos.principal', [$imovel, $segunda]));

        $response->assertRedirect();
        $this->assertFalse($primeira->fresh()->is_principal);
        $this->assertTrue($segunda->fresh()->is_principal);
    }

    public function test_upload_de_documento_registra_historico(): void
    {
        $imovel = $this->criarImovel();
        $user = $this->usuarioComPermissoes(['imoveis.viewAny', 'imoveis.view', 'imoveis.gerenciar-documentos']);

        $response = $this->actingAs($user)->post(route('imoveis.documentos.store', $imovel), [
            'documento' => UploadedFile::fake()->create('contrato.pdf', 100, 'application/pdf'),
            'tipo'      => 'matricula',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('imovel_documentos', [
            'imovel_id' => $imovel->id,
            'tipo'      => 'matricula',
        ]);
        $this->assertDatabaseHas('imovel_historicos', [
            'imovel_id'   => $imovel->id,
            'tipo_evento' => 'documento_adicionado',
        ]);
    }

    public function test_remover_documento_registra_historico(): void
    {
        $imovel = $this->criarImovel();
        $user = $this->usuarioComPermissoes(['imoveis.viewAny', 'imoveis.view', 'imoveis.gerenciar-documentos']);

        $this->actingAs($user)->post(route('imoveis.documentos.store', $imovel), [
            'documento' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
        ]);
        $documento = $imovel->documentos()->firstOrFail();

        $response = $this->actingAs($user)->delete(route('imoveis.documentos.destroy', [$imovel, $documento]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('imovel_documentos', ['id' => $documento->id]);
        $this->assertDatabaseHas('imovel_historicos', [
            'imovel_id'   => $imovel->id,
            'tipo_evento' => 'documento_removido',
        ]);
    }
}

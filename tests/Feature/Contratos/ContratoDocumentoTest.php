<?php

namespace Tests\Feature\Contratos;

use App\Models\ContratoDocumento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContratoDocumentoTest extends TestCase
{
    use RefreshDatabase;
    use ContratoTestHelpers;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_usuario_autorizado_anexa_contrato_assinado(): void
    {
        $contrato = $this->criarContrato();
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.documentos']);

        $response = $this->actingAs($user)->post(route('contratos.documentos.adicionar', $contrato->id), [
            'documento' => UploadedFile::fake()->create('contrato-assinado.pdf', 500, 'application/pdf'),
            'tipo' => 'contrato_assinado',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('contrato_documentos', [
            'contrato_id' => $contrato->id,
            'tipo' => 'contrato_assinado',
            'nome_original' => 'contrato-assinado.pdf',
        ]);
    }

    public function test_usuario_sem_permissao_nao_anexa_documento(): void
    {
        $contrato = $this->criarContrato();
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view']);

        $response = $this->actingAs($user)->post(route('contratos.documentos.adicionar', $contrato->id), [
            'documento' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
        ]);

        $response->assertForbidden();
    }

    public function test_usuario_autorizado_remove_documento(): void
    {
        $contrato = $this->criarContrato();
        $user = $this->usuarioComPermissoes(['contratos.viewAny', 'contratos.view', 'contratos.documentos']);

        $documento = ContratoDocumento::create([
            'contrato_id' => $contrato->id,
            'caminho' => 'contratos/' . $contrato->id . '/arquivo.pdf',
            'nome_original' => 'arquivo.pdf',
            'tipo' => 'outros',
        ]);

        $response = $this->actingAs($user)->delete(route('contratos.documentos.remover', [$contrato->id, $documento->id]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('contrato_documentos', ['id' => $documento->id]);
    }
}

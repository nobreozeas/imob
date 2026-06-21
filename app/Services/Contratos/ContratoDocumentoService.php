<?php

namespace App\Services\Contratos;

use App\Models\ContratoDocumento;
use App\Models\ContratoLocacao;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ContratoDocumentoService
{
    public function salvarDocumentos(
        ContratoLocacao $contrato,
        array $arquivos,
        array $tipos = [],
        ?string $criadoPor = null,
    ): void {
        $pasta = "contratos/{$contrato->id}";

        foreach ($arquivos as $index => $arquivo) {
            if (!($arquivo instanceof UploadedFile)) {
                continue;
            }

            $caminho = $arquivo->store($pasta, 'public');

            $contrato->documentos()->create([
                'caminho'      => $caminho,
                'nome_original' => $arquivo->getClientOriginalName(),
                'tipo'         => $tipos[$index] ?? 'outros',
                'criado_por'   => $criadoPor,
            ]);
        }
    }

    public function removerDocumento(ContratoDocumento $documento): void
    {
        Storage::disk('public')->delete($documento->caminho);
        $documento->delete();
    }
}

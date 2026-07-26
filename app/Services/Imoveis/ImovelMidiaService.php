<?php

namespace App\Services\Imoveis;

use App\Models\Imovel;
use App\Models\ImovelDocumento;
use App\Models\ImovelFoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImovelMidiaService
{
    public function __construct(private ImovelHistoricoService $historico) {}

    public function sincronizarFotos(Imovel $imovel, array $dados, ?string $usuarioId = null): void
    {
        if (!empty($dados['fotos_remover'])) {
            foreach (ImovelFoto::whereIn('id', $dados['fotos_remover'])->where('imovel_id', $imovel->id)->get() as $foto) {
                $this->removerFoto($imovel, $foto, $usuarioId);
            }
        }

        if (!empty($dados['fotos_novas'])) {
            foreach ($dados['fotos_novas'] as $arquivo) {
                if ($arquivo instanceof UploadedFile) {
                    $this->adicionarFoto($imovel, $arquivo, $usuarioId);
                }
            }
        }

        if (!empty($dados['foto_principal_id'])) {
            $foto = $imovel->fotos()->find($dados['foto_principal_id']);
            if ($foto) {
                $this->definirFotoPrincipal($imovel, $foto, $usuarioId);
            }
        } elseif ($imovel->fotos()->where('is_principal', true)->doesntExist()) {
            $primeira = $imovel->fotos()->orderBy('ordem')->first();
            if ($primeira) {
                $primeira->update(['is_principal' => true]);
            }
        }
    }

    public function adicionarFoto(Imovel $imovel, UploadedFile $arquivo, ?string $usuarioId = null): ImovelFoto
    {
        $pasta = "imoveis/{$imovel->id}/fotos";
        $ordem = $imovel->fotos()->max('ordem') ?? 0;

        $foto = $imovel->fotos()->create([
            'caminho'       => $arquivo->store($pasta, 'public'),
            'nome_original' => $arquivo->getClientOriginalName(),
            'is_principal'  => false,
            'ordem'         => $ordem + 1,
        ]);

        if ($imovel->fotos()->where('is_principal', true)->doesntExist()) {
            $foto->update(['is_principal' => true]);
        }

        $this->historico->registrar(
            $imovel,
            'foto_adicionada',
            "Foto \"{$foto->nome_original}\" adicionada.",
            usuarioId: $usuarioId,
        );

        return $foto;
    }

    public function removerFoto(Imovel $imovel, ImovelFoto $foto, ?string $usuarioId = null): void
    {
        Storage::disk('public')->delete($foto->caminho);
        $nome = $foto->nome_original;
        $foto->delete();

        $this->historico->registrar(
            $imovel,
            'foto_removida',
            "Foto \"{$nome}\" removida.",
            usuarioId: $usuarioId,
        );
    }

    public function definirFotoPrincipal(Imovel $imovel, ImovelFoto $foto, ?string $usuarioId = null): void
    {
        $imovel->fotos()->update(['is_principal' => false]);
        $foto->update(['is_principal' => true]);
    }

    public function sincronizarDocumentos(Imovel $imovel, array $dados, ?string $usuarioId = null): void
    {
        if (!empty($dados['documentos_remover'])) {
            foreach (ImovelDocumento::whereIn('id', $dados['documentos_remover'])->where('imovel_id', $imovel->id)->get() as $documento) {
                $this->removerDocumento($imovel, $documento, $usuarioId);
            }
        }

        if (!empty($dados['documentos_novos'])) {
            $tipos = $dados['tipos_documentos'] ?? [];
            foreach ($dados['documentos_novos'] as $index => $arquivo) {
                if ($arquivo instanceof UploadedFile) {
                    $this->adicionarDocumento($imovel, $arquivo, $tipos[$index] ?? null, $usuarioId);
                }
            }
        }
    }

    public function adicionarDocumento(Imovel $imovel, UploadedFile $arquivo, ?string $tipo, ?string $usuarioId = null): ImovelDocumento
    {
        $pasta = "imoveis/{$imovel->id}/documentos";

        $documento = $imovel->documentos()->create([
            'caminho'       => $arquivo->store($pasta, 'public'),
            'nome_original' => $arquivo->getClientOriginalName(),
            'tipo'          => $tipo,
        ]);

        $this->historico->registrar(
            $imovel,
            'documento_adicionado',
            "Documento \"{$documento->nome_original}\" adicionado.",
            usuarioId: $usuarioId,
        );

        return $documento;
    }

    public function removerDocumento(Imovel $imovel, ImovelDocumento $documento, ?string $usuarioId = null): void
    {
        Storage::disk('public')->delete($documento->caminho);
        $nome = $documento->nome_original;
        $documento->delete();

        $this->historico->registrar(
            $imovel,
            'documento_removido',
            "Documento \"{$nome}\" removido.",
            usuarioId: $usuarioId,
        );
    }
}

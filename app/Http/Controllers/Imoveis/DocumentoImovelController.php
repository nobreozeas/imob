<?php

namespace App\Http\Controllers\Imoveis;

use App\Http\Controllers\Controller;
use App\Http\Requests\Imoveis\StoreDocumentoImovelRequest;
use App\Models\Imovel;
use App\Models\ImovelDocumento;
use App\Services\Imoveis\ImovelMidiaService;
use Illuminate\Http\RedirectResponse;

class DocumentoImovelController extends Controller
{
    public function __construct(private ImovelMidiaService $service) {}

    public function store(StoreDocumentoImovelRequest $request, Imovel $imovel): RedirectResponse
    {
        $this->authorize('gerenciarDocumentos', $imovel);

        $this->service->adicionarDocumento(
            $imovel,
            $request->file('documento'),
            $request->input('tipo'),
            $request->user()->id,
        );

        return back()->with('status', 'Documento adicionado com sucesso.');
    }

    public function destroy(Imovel $imovel, ImovelDocumento $documento): RedirectResponse
    {
        $this->authorize('gerenciarDocumentos', $imovel);

        abort_unless($documento->imovel_id === $imovel->id, 404);

        $this->service->removerDocumento($imovel, $documento, request()->user()->id);

        return back()->with('status', 'Documento removido com sucesso.');
    }
}

<?php

namespace App\Http\Controllers\Imoveis;

use App\Http\Controllers\Controller;
use App\Http\Requests\Imoveis\StoreFotoImovelRequest;
use App\Models\Imovel;
use App\Models\ImovelFoto;
use App\Services\Imoveis\ImovelMidiaService;
use Illuminate\Http\RedirectResponse;

class FotoImovelController extends Controller
{
    public function __construct(private ImovelMidiaService $service) {}

    public function store(StoreFotoImovelRequest $request, Imovel $imovel): RedirectResponse
    {
        $this->authorize('gerenciarFotos', $imovel);

        foreach ($request->file('fotos') as $arquivo) {
            $this->service->adicionarFoto($imovel, $arquivo, $request->user()->id);
        }

        return back()->with('status', 'Foto(s) adicionada(s) com sucesso.');
    }

    public function destroy(Imovel $imovel, ImovelFoto $foto): RedirectResponse
    {
        $this->authorize('gerenciarFotos', $imovel);

        abort_unless($foto->imovel_id === $imovel->id, 404);

        $this->service->removerFoto($imovel, $foto, request()->user()->id);

        return back()->with('status', 'Foto removida com sucesso.');
    }

    public function definirPrincipal(Imovel $imovel, ImovelFoto $foto): RedirectResponse
    {
        $this->authorize('gerenciarFotos', $imovel);

        abort_unless($foto->imovel_id === $imovel->id, 404);

        $this->service->definirFotoPrincipal($imovel, $foto, request()->user()->id);

        return back()->with('status', 'Foto principal atualizada com sucesso.');
    }
}

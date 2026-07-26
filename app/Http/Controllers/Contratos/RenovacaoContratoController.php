<?php

namespace App\Http\Controllers\Contratos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contratos\StoreRenovacaoContratoRequest;
use App\Models\ContratoLocacao;
use App\Services\Contratos\RenovacaoContratoService;
use Illuminate\Http\RedirectResponse;

class RenovacaoContratoController extends Controller
{
    public function __construct(
        private RenovacaoContratoService $service,
    ) {}

    public function store(StoreRenovacaoContratoRequest $request, ContratoLocacao $contrato): RedirectResponse
    {
        $this->authorize('renovar', $contrato);

        $novoContrato = $this->service->renovar($contrato, $request->validated(), $request->user());

        return redirect()
            ->route('contratos.show', $novoContrato)
            ->with('status', 'Contrato renovado com sucesso.');
    }
}

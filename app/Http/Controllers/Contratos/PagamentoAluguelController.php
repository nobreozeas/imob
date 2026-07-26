<?php

namespace App\Http\Controllers\Contratos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contratos\RegistrarPagamentoAluguelRequest;
use App\Models\ContratoLocacao;
use App\Models\ParcelaAluguel;
use App\Services\Contratos\PagamentoAluguelService;
use Illuminate\Http\RedirectResponse;

class PagamentoAluguelController extends Controller
{
    public function __construct(
        private PagamentoAluguelService $service,
    ) {}

    public function store(RegistrarPagamentoAluguelRequest $request, ContratoLocacao $contrato, ParcelaAluguel $parcela): RedirectResponse
    {
        $this->authorize('registrarPagamento', $contrato);

        $this->service->registrar($parcela, $request->validated(), $request->user()->id);

        return back()->with('status', 'Pagamento registrado com sucesso.');
    }
}

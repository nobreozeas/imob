<?php

namespace App\Http\Controllers\Contratos;

use App\Http\Controllers\Controller;
use App\Models\RepasseProprietario;
use App\Services\Contratos\RepasseProprietarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class RepasseProprietarioController extends Controller
{
    public function __construct(
        private RepasseProprietarioService $service,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', RepasseProprietario::class);

        $repasses = RepasseProprietario::with(['contrato', 'imovel', 'proprietario', 'parcela'])
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->proprietario_id, fn ($q, $v) => $q->where('proprietario_id', $v))
            ->when($request->contrato_id, fn ($q, $v) => $q->where('contrato_id', $v))
            ->when($request->imovel_id, fn ($q, $v) => $q->where('imovel_id', $v))
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Financeiro/Repasses/Index', [
            'repasses' => $repasses,
            'filtros' => $request->only(['status', 'proprietario_id', 'contrato_id', 'imovel_id']),
        ]);
    }

    public function marcarComoPago(Request $request, RepasseProprietario $repasse): RedirectResponse
    {
        $this->authorize('marcarComoPago', $repasse);

        $dados = $request->validate([
            'data_pagamento' => ['required', 'date'],
            'forma_pagamento' => ['nullable', Rule::in(['pix', 'transferencia', 'dinheiro'])],
        ]);

        $this->service->marcarComoPago($repasse, $dados, $request->user()->id);

        return back()->with('status', 'Repasse marcado como pago.');
    }

    public function cancelar(Request $request, RepasseProprietario $repasse): RedirectResponse
    {
        $this->authorize('marcarComoPago', $repasse);

        $dados = $request->validate([
            'motivo' => ['required', 'string'],
        ]);

        $this->service->cancelar($repasse, $dados['motivo'], $request->user()->id);

        return back()->with('status', 'Repasse cancelado.');
    }
}

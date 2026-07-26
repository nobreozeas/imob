<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\LancamentoFinanceiro;
use App\Services\Financeiro\InadimplenciaService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InadimplenciaController extends Controller
{
    public function __construct(
        private InadimplenciaService $service,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', LancamentoFinanceiro::class);

        $filtros = $request->only(['contrato_id', 'proprietario_id', 'cliente_id']);

        return Inertia::render('Admin/Financeiro/Inadimplencia', [
            'parcelas' => $this->service->listar($filtros),
            'indicadores' => $this->service->indicadores($filtros),
            'filtros' => $filtros,
        ]);
    }
}

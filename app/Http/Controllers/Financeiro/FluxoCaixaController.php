<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\LancamentoFinanceiro;
use App\Services\Financeiro\FluxoCaixaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FluxoCaixaController extends Controller
{
    public function __construct(
        private FluxoCaixaService $service,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', LancamentoFinanceiro::class);

        $agrupamento = $request->input('agrupamento', 'dia');
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->toDateString());
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->toDateString());

        return Inertia::render('Admin/Financeiro/FluxoCaixa', [
            'fluxo' => $this->service->calcular($dataInicio, $dataFim, $agrupamento),
            'filtros' => [
                'agrupamento' => $agrupamento,
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
            ],
        ]);
    }
}

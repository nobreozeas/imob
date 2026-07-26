<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\LancamentoFinanceiro;
use App\Services\Financeiro\IndicadoresFinanceirosService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FinanceiroDashboardController extends Controller
{
    public function __construct(
        private IndicadoresFinanceirosService $service,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', LancamentoFinanceiro::class);

        $filtros = $request->only(['data_inicio', 'data_fim', 'proprietario_id', 'status_contrato', 'status_imovel']);

        return Inertia::render('Admin/Financeiro/Dashboard', [
            'resumo' => $this->service->resumoMensal($filtros),
            'filtros' => $filtros,
            'proprietarios' => Cliente::whereHas('papeis', fn ($q) => $q->where('papel', 'proprietario'))
                ->orderBy('nome')
                ->get(['id', 'nome', 'razao_social', 'tipo_pessoa']),
        ]);
    }
}

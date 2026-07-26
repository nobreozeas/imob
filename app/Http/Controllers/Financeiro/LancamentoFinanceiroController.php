<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financeiro\CancelarLancamentoFinanceiroRequest;
use App\Http\Requests\Financeiro\EstornarLancamentoFinanceiroRequest;
use App\Http\Requests\Financeiro\MarcarLancamentoComoPagoRequest;
use App\Http\Requests\Financeiro\StoreDespesaFinanceiraRequest;
use App\Http\Requests\Financeiro\StoreReceitaFinanceiraRequest;
use App\Http\Requests\Financeiro\UpdateLancamentoFinanceiroRequest;
use App\Models\CategoriaFinanceira;
use App\Models\LancamentoFinanceiro;
use App\Services\Financeiro\DespesaFinanceiraService;
use App\Services\Financeiro\LancamentoFinanceiroService;
use App\Services\Financeiro\ReceitaFinanceiraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class LancamentoFinanceiroController extends Controller
{
    public function __construct(
        private ReceitaFinanceiraService $receitaService,
        private DespesaFinanceiraService $despesaService,
        private LancamentoFinanceiroService $lancamentoService,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', LancamentoFinanceiro::class);

        $query = LancamentoFinanceiro::with(['categoria', 'contrato', 'imovel', 'cliente'])
            ->when($request->busca, fn ($q, $busca) => $q->where(function ($q) use ($busca) {
                $q->where('codigo', 'ilike', "%{$busca}%")->orWhere('descricao', 'ilike', "%{$busca}%");
            }))
            ->when($request->tipo, fn ($q, $v) => $q->where('tipo', $v))
            ->when($request->categoria_financeira_id, fn ($q, $v) => $q->where('categoria_financeira_id', $v))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->forma_pagamento, fn ($q, $v) => $q->where('forma_pagamento', $v))
            ->when($request->origem, fn ($q, $v) => $q->where('origem', $v))
            ->when($request->contrato_id, fn ($q, $v) => $q->where('contrato_id', $v))
            ->when($request->imovel_id, fn ($q, $v) => $q->where('imovel_id', $v))
            ->when($request->cliente_id, fn ($q, $v) => $q->where('cliente_id', $v))
            ->when($request->data_vencimento_de, fn ($q, $v) => $q->whereDate('data_vencimento', '>=', $v))
            ->when($request->data_vencimento_ate, fn ($q, $v) => $q->whereDate('data_vencimento', '<=', $v))
            ->when($request->data_pagamento_de, fn ($q, $v) => $q->whereDate('data_pagamento', '>=', $v))
            ->when($request->data_pagamento_ate, fn ($q, $v) => $q->whereDate('data_pagamento', '<=', $v));

        $lancamentos = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Admin/Financeiro/Lancamentos/Index', [
            'lancamentos' => $lancamentos,
            'categorias' => CategoriaFinanceira::where('ativa', true)->orderBy('nome')->get(),
            'filtros' => $request->only([
                'busca', 'tipo', 'categoria_financeira_id', 'status', 'forma_pagamento', 'origem',
                'contrato_id', 'imovel_id', 'cliente_id',
                'data_vencimento_de', 'data_vencimento_ate', 'data_pagamento_de', 'data_pagamento_ate',
            ]),
        ]);
    }

    public function storeReceita(StoreReceitaFinanceiraRequest $request): RedirectResponse
    {
        $this->authorize('create', LancamentoFinanceiro::class);

        $this->receitaService->criar($request->validated(), $request->user()->id);

        return back()->with('status', 'Receita registrada com sucesso.');
    }

    public function storeDespesa(StoreDespesaFinanceiraRequest $request): RedirectResponse
    {
        $this->authorize('create', LancamentoFinanceiro::class);

        $this->despesaService->criar($request->validated(), $request->user()->id);

        return back()->with('status', 'Despesa registrada com sucesso.');
    }

    public function show(LancamentoFinanceiro $lancamento): Response
    {
        $this->authorize('view', $lancamento);

        $lancamento->load(['categoria', 'contrato', 'imovel', 'cliente', 'historicos.criador']);

        return Inertia::render('Admin/Financeiro/Lancamentos/Show', [
            'lancamento' => $lancamento,
        ]);
    }

    public function update(UpdateLancamentoFinanceiroRequest $request, LancamentoFinanceiro $lancamento): RedirectResponse
    {
        $this->authorize('update', $lancamento);

        if ($lancamento->status !== LancamentoFinanceiro::STATUS_PENDENTE) {
            throw ValidationException::withMessages([
                'status' => 'Apenas lançamentos pendentes podem ser editados.',
            ]);
        }

        $lancamento->update($request->validated());

        return back()->with('status', 'Lançamento atualizado com sucesso.');
    }

    public function destroy(LancamentoFinanceiro $lancamento): RedirectResponse
    {
        $this->authorize('delete', $lancamento);

        $lancamento->delete();

        return back()->with('status', 'Lançamento excluído com sucesso.');
    }

    public function marcarComoPago(MarcarLancamentoComoPagoRequest $request, LancamentoFinanceiro $lancamento): RedirectResponse
    {
        $this->authorize('marcarComoPago', $lancamento);

        $this->lancamentoService->marcarComoPago($lancamento, $request->validated(), $request->user()->id);

        return back()->with('status', 'Lançamento marcado como pago.');
    }

    public function cancelar(CancelarLancamentoFinanceiroRequest $request, LancamentoFinanceiro $lancamento): RedirectResponse
    {
        $this->authorize('cancelar', $lancamento);

        $this->lancamentoService->cancelar($lancamento, $request->validated('motivo'), $request->user()->id);

        return back()->with('status', 'Lançamento cancelado.');
    }

    public function estornar(EstornarLancamentoFinanceiroRequest $request, LancamentoFinanceiro $lancamento): RedirectResponse
    {
        $this->authorize('estornar', $lancamento);

        $this->lancamentoService->estornar($lancamento, $request->validated('motivo'), $request->user()->id);

        return back()->with('status', 'Lançamento estornado.');
    }
}

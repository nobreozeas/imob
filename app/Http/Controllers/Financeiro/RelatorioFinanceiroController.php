<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\LancamentoFinanceiro;
use App\Models\ParcelaAluguel;
use App\Models\RepasseProprietario;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RelatorioFinanceiroController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('ver-relatorios-financeiros');

        $tipo = $request->input('tipo', 'receitas');
        $filtros = $request->only(['data_inicio', 'data_fim', 'status', 'categoria_financeira_id', 'cliente_id', 'contrato_id', 'imovel_id', 'forma_pagamento']);

        return Inertia::render('Admin/Financeiro/Relatorios/Index', [
            'tipo' => $tipo,
            'dados' => $this->gerarRelatorio($tipo, $filtros),
            'filtros' => $filtros,
        ]);
    }

    private function gerarRelatorio(string $tipo, array $filtros)
    {
        return match ($tipo) {
            'despesas' => $this->lancamentos(LancamentoFinanceiro::TIPO_SAIDA, $filtros),
            'alugueis' => $this->alugueis($filtros),
            'repasses' => $this->repasses($filtros),
            'caucoes' => $this->lancamentosPorOrigem([LancamentoFinanceiro::ORIGEM_CAUCAO, LancamentoFinanceiro::ORIGEM_MOVIMENTACAO_CAUCAO], $filtros),
            default => $this->lancamentos(LancamentoFinanceiro::TIPO_ENTRADA, $filtros),
        };
    }

    private function lancamentos(string $tipo, array $filtros)
    {
        return $this->aplicarFiltrosComuns(
            LancamentoFinanceiro::with(['categoria', 'contrato', 'imovel', 'cliente'])
                ->where('tipo', $tipo)
                ->whereHas('categoria', fn ($q) => $q->where('impacta_resultado', true)),
            $filtros,
        )->orderBy('data_pagamento', 'desc')->paginate(20)->withQueryString();
    }

    private function lancamentosPorOrigem(array $origens, array $filtros)
    {
        return $this->aplicarFiltrosComuns(
            LancamentoFinanceiro::with(['categoria', 'contrato', 'imovel', 'cliente'])->whereIn('origem', $origens),
            $filtros,
        )->orderBy('data_pagamento', 'desc')->paginate(20)->withQueryString();
    }

    private function aplicarFiltrosComuns($query, array $filtros)
    {
        return $query
            ->when($filtros['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filtros['categoria_financeira_id'] ?? null, fn ($q, $v) => $q->where('categoria_financeira_id', $v))
            ->when($filtros['cliente_id'] ?? null, fn ($q, $v) => $q->where('cliente_id', $v))
            ->when($filtros['contrato_id'] ?? null, fn ($q, $v) => $q->where('contrato_id', $v))
            ->when($filtros['imovel_id'] ?? null, fn ($q, $v) => $q->where('imovel_id', $v))
            ->when($filtros['forma_pagamento'] ?? null, fn ($q, $v) => $q->where('forma_pagamento', $v))
            ->when($filtros['data_inicio'] ?? null, fn ($q, $v) => $q->whereDate('data_pagamento', '>=', $v))
            ->when($filtros['data_fim'] ?? null, fn ($q, $v) => $q->whereDate('data_pagamento', '<=', $v));
    }

    private function alugueis(array $filtros)
    {
        return ParcelaAluguel::with(['contrato.inquilino', 'contrato.proprietario', 'contrato.imovel', 'repasse'])
            ->when($filtros['contrato_id'] ?? null, fn ($q, $v) => $q->where('contrato_id', $v))
            ->when($filtros['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filtros['data_inicio'] ?? null, fn ($q, $v) => $q->whereDate('data_vencimento', '>=', $v))
            ->when($filtros['data_fim'] ?? null, fn ($q, $v) => $q->whereDate('data_vencimento', '<=', $v))
            ->orderBy('data_vencimento', 'desc')
            ->paginate(20)
            ->withQueryString();
    }

    private function repasses(array $filtros)
    {
        return RepasseProprietario::with(['contrato', 'imovel', 'proprietario', 'parcela'])
            ->when($filtros['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filtros['cliente_id'] ?? null, fn ($q, $v) => $q->where('proprietario_id', $v))
            ->when($filtros['contrato_id'] ?? null, fn ($q, $v) => $q->where('contrato_id', $v))
            ->when($filtros['imovel_id'] ?? null, fn ($q, $v) => $q->where('imovel_id', $v))
            ->when($filtros['data_inicio'] ?? null, fn ($q, $v) => $q->whereDate('data_pagamento', '>=', $v))
            ->when($filtros['data_fim'] ?? null, fn ($q, $v) => $q->whereDate('data_pagamento', '<=', $v))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();
    }
}

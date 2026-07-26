<?php

namespace App\Http\Controllers\Financeiro;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financeiro\StoreCategoriaFinanceiraRequest;
use App\Http\Requests\Financeiro\UpdateCategoriaFinanceiraRequest;
use App\Models\CategoriaFinanceira;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CategoriaFinanceiraController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', CategoriaFinanceira::class);

        return Inertia::render('Admin/Financeiro/Categorias/Index', [
            'categorias' => CategoriaFinanceira::orderBy('tipo')->orderBy('nome')->get(),
        ]);
    }

    public function store(StoreCategoriaFinanceiraRequest $request): RedirectResponse
    {
        $this->authorize('create', CategoriaFinanceira::class);

        $dados = $request->validated();
        $dados['slug'] ??= Str::slug($dados['nome'], '_');
        $dados['ativa'] ??= true;

        CategoriaFinanceira::create($dados);

        return back()->with('status', 'Categoria financeira criada com sucesso.');
    }

    public function update(UpdateCategoriaFinanceiraRequest $request, CategoriaFinanceira $categoria): RedirectResponse
    {
        $this->authorize('update', $categoria);

        $dados = $request->validated();
        $dados['slug'] ??= $categoria->slug;
        $dados['ativa'] ??= $categoria->ativa;

        $categoria->update($dados);

        return back()->with('status', 'Categoria financeira atualizada com sucesso.');
    }

    public function destroy(CategoriaFinanceira $categoria): RedirectResponse
    {
        $this->authorize('delete', $categoria);

        if ($categoria->lancamentos()->exists()) {
            throw ValidationException::withMessages([
                'categoria' => 'Esta categoria possui lançamentos vinculados e não pode ser excluída.',
            ]);
        }

        $categoria->delete();

        return back()->with('status', 'Categoria financeira excluída com sucesso.');
    }
}

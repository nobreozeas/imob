<?php

namespace App\Http\Controllers\Contratos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contratos\StoreMovimentacaoCaucaoRequest;
use App\Models\ContratoLocacao;
use App\Services\Contratos\MovimentacaoCaucaoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class MovimentacaoCaucaoController extends Controller
{
    public function __construct(
        private MovimentacaoCaucaoService $service,
    ) {}

    public function store(StoreMovimentacaoCaucaoRequest $request, ContratoLocacao $contrato): RedirectResponse
    {
        $this->authorize('gerenciarCaucao', $contrato);

        $caucao = $contrato->caucao;

        if (!$caucao || !$caucao->possui_caucao) {
            throw ValidationException::withMessages([
                'caucao' => 'Este contrato não possui caução configurada.',
            ]);
        }

        $dados = $request->validated();

        $this->service->registrar(
            $caucao,
            $dados['tipo_movimentacao'],
            $dados['valor'],
            $dados,
            $request->user()->id,
        );

        return back()->with('status', 'Movimentação de caução registrada com sucesso.');
    }
}

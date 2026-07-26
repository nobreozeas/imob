<?php

namespace App\Http\Controllers\Contratos;

use App\Http\Controllers\Controller;
use App\Models\ContratoLocacao;
use Illuminate\Contracts\View\View;

class ContratoImpressaoController extends Controller
{
    public function imprimir(ContratoLocacao $contrato): View
    {
        $this->authorize('view', $contrato);

        $contrato->load(['imovel', 'proprietario', 'inquilino', 'corretor', 'encargos', 'caucao', 'multas']);

        return view('contratos.imprimir', ['contrato' => $contrato]);
    }
}

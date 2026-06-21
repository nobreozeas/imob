<?php

use App\Http\Controllers\Clientes\ClienteController;
use App\Http\Controllers\Contratos\ContratoLocacaoController;
use App\Http\Controllers\Imoveis\ImovelController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Admin/Dashboard');
})->middleware(['auth', 'must.change.password'])->name('dashboard');

Route::middleware(['auth', 'must.change.password'])->group(function () {
    Route::resource('clientes', ClienteController::class)
        ->except(['destroy'])
        ->names('clientes');

    Route::patch('clientes/{cliente}/status', [ClienteController::class, 'alterarStatus'])
        ->name('clientes.alterar-status');

    Route::resource('imoveis', ImovelController::class)
        ->except(['destroy'])
        ->names('imoveis')
        ->parameters(['imoveis' => 'imovel']);

    Route::patch('imoveis/{imovel}/status', [ImovelController::class, 'alterarStatus'])
        ->name('imoveis.alterar-status');

    Route::resource('contratos', ContratoLocacaoController::class)
        ->except(['destroy'])
        ->names('contratos')
        ->parameters(['contratos' => 'contrato']);

    Route::post('contratos/{contrato}/ativar', [ContratoLocacaoController::class, 'ativar'])
        ->name('contratos.ativar');
    Route::post('contratos/{contrato}/enviar-assinatura', [ContratoLocacaoController::class, 'enviarParaAssinatura'])
        ->name('contratos.enviar-assinatura');
    Route::post('contratos/{contrato}/cancelar', [ContratoLocacaoController::class, 'cancelar'])
        ->name('contratos.cancelar');
    Route::post('contratos/{contrato}/encerrar', [ContratoLocacaoController::class, 'encerrar'])
        ->name('contratos.encerrar');
    Route::post('contratos/{contrato}/rescindir', [ContratoLocacaoController::class, 'rescindir'])
        ->name('contratos.rescindir');
    Route::post('contratos/{contrato}/documentos', [ContratoLocacaoController::class, 'adicionarDocumento'])
        ->name('contratos.documentos.adicionar');
    Route::delete('contratos/{contrato}/documentos/{documento}', [ContratoLocacaoController::class, 'removerDocumento'])
        ->name('contratos.documentos.remover');
});

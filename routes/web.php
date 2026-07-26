<?php

use App\Http\Controllers\Clientes\ClienteController;
use App\Http\Controllers\Contratos\ContratoImpressaoController;
use App\Http\Controllers\Contratos\ContratoLocacaoController;
use App\Http\Controllers\Contratos\MovimentacaoCaucaoController;
use App\Http\Controllers\Contratos\PagamentoAluguelController;
use App\Http\Controllers\Contratos\RenovacaoContratoController;
use App\Http\Controllers\Contratos\RepasseProprietarioController;
use App\Http\Controllers\Financeiro\CategoriaFinanceiraController;
use App\Http\Controllers\Financeiro\FinanceiroDashboardController;
use App\Http\Controllers\Financeiro\FluxoCaixaController;
use App\Http\Controllers\Financeiro\InadimplenciaController;
use App\Http\Controllers\Financeiro\LancamentoFinanceiroController;
use App\Http\Controllers\Financeiro\RelatorioFinanceiroController;
use App\Http\Controllers\Imoveis\DocumentoImovelController;
use App\Http\Controllers\Imoveis\FotoImovelController;
use App\Http\Controllers\Imoveis\ImovelController;
use App\Http\Controllers\Perfis\PerfilController;
use App\Http\Controllers\Usuarios\UsuarioController;
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
        ->names('imoveis')
        ->parameters(['imoveis' => 'imovel']);

    Route::patch('imoveis/{imovel}/status', [ImovelController::class, 'alterarStatus'])
        ->name('imoveis.alterar-status');

    Route::patch('imoveis/{imovel}/restaurar', [ImovelController::class, 'restore'])
        ->withTrashed()
        ->name('imoveis.restore');

    Route::post('imoveis/{imovel}/fotos', [FotoImovelController::class, 'store'])
        ->name('imoveis.fotos.store');
    Route::delete('imoveis/{imovel}/fotos/{foto}', [FotoImovelController::class, 'destroy'])
        ->name('imoveis.fotos.destroy');
    Route::patch('imoveis/{imovel}/fotos/{foto}/principal', [FotoImovelController::class, 'definirPrincipal'])
        ->name('imoveis.fotos.principal');

    Route::post('imoveis/{imovel}/documentos', [DocumentoImovelController::class, 'store'])
        ->name('imoveis.documentos.store');
    Route::delete('imoveis/{imovel}/documentos/{documento}', [DocumentoImovelController::class, 'destroy'])
        ->name('imoveis.documentos.destroy');

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
    Route::get('contratos/{contrato}/imprimir', [ContratoImpressaoController::class, 'imprimir'])
        ->name('contratos.imprimir');

    Route::post('contratos/{contrato}/parcelas/{parcela}/pagamento', [PagamentoAluguelController::class, 'store'])
        ->name('contratos.parcelas.pagamento');
    Route::post('contratos/{contrato}/caucao/movimentacoes', [MovimentacaoCaucaoController::class, 'store'])
        ->name('contratos.caucao.movimentacoes');
    Route::post('contratos/{contrato}/renovar', [RenovacaoContratoController::class, 'store'])
        ->name('contratos.renovar');
    Route::post('repasses-proprietarios/{repasse}/marcar-como-pago', [RepasseProprietarioController::class, 'marcarComoPago'])
        ->name('repasses-proprietarios.marcar-como-pago');
    Route::post('repasses-proprietarios/{repasse}/cancelar', [RepasseProprietarioController::class, 'cancelar'])
        ->name('repasses-proprietarios.cancelar');

    Route::get('financeiro/dashboard', [FinanceiroDashboardController::class, 'index'])
        ->name('financeiro.dashboard');
    Route::get('financeiro/repasses', [RepasseProprietarioController::class, 'index'])
        ->name('financeiro.repasses.index');
    Route::get('financeiro/fluxo-caixa', [FluxoCaixaController::class, 'index'])
        ->name('financeiro.fluxo-caixa');
    Route::get('financeiro/inadimplencia', [InadimplenciaController::class, 'index'])
        ->name('financeiro.inadimplencia');
    Route::get('financeiro/relatorios', [RelatorioFinanceiroController::class, 'index'])
        ->name('financeiro.relatorios');

    Route::resource('financeiro/lancamentos', LancamentoFinanceiroController::class)
        ->except(['store', 'create', 'edit'])
        ->names('financeiro.lancamentos')
        ->parameters(['lancamentos' => 'lancamento']);

    Route::post('financeiro/lancamentos/receitas', [LancamentoFinanceiroController::class, 'storeReceita'])
        ->name('financeiro.lancamentos.receitas.store');
    Route::post('financeiro/lancamentos/despesas', [LancamentoFinanceiroController::class, 'storeDespesa'])
        ->name('financeiro.lancamentos.despesas.store');
    Route::post('financeiro/lancamentos/{lancamento}/marcar-como-pago', [LancamentoFinanceiroController::class, 'marcarComoPago'])
        ->name('financeiro.lancamentos.marcar-como-pago');
    Route::post('financeiro/lancamentos/{lancamento}/cancelar', [LancamentoFinanceiroController::class, 'cancelar'])
        ->name('financeiro.lancamentos.cancelar');
    Route::post('financeiro/lancamentos/{lancamento}/estornar', [LancamentoFinanceiroController::class, 'estornar'])
        ->name('financeiro.lancamentos.estornar');

    Route::resource('financeiro/categorias', CategoriaFinanceiraController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->names('financeiro.categorias')
        ->parameters(['categorias' => 'categoria']);

    Route::resource('usuarios', UsuarioController::class)
        ->except(['destroy', 'show'])
        ->names('usuarios')
        ->parameters(['usuarios' => 'usuario']);

    Route::patch('usuarios/{usuario}/status', [UsuarioController::class, 'alterarStatus'])
        ->name('usuarios.alterar-status');

    Route::post('usuarios/{usuario}/reenviar-acesso', [UsuarioController::class, 'reenviarAcesso'])
        ->name('usuarios.reenviar-acesso');

    Route::resource('perfis', PerfilController::class)
        ->only(['index', 'show'])
        ->names('perfis')
        ->parameters(['perfis' => 'perfil']);
});

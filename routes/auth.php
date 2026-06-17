<?php

use App\Http\Controllers\Auth\AlteracaoSenhaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PrimeiroAcessoController;
use App\Http\Controllers\Auth\RecuperacaoSenhaController;
use App\Http\Controllers\Auth\RedefinirSenhaController;
use Illuminate\Support\Facades\Route;

// Rotas públicas (guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/esqueci-senha', [RecuperacaoSenhaController::class, 'create'])->name('password.request');
    Route::post('/esqueci-senha', [RecuperacaoSenhaController::class, 'store'])->name('password.email');

    Route::get('/redefinir-senha/{token}', [RedefinirSenhaController::class, 'create'])->name('password.reset');
    Route::post('/redefinir-senha', [RedefinirSenhaController::class, 'store'])->name('password.update');
});

// Rota de primeiro acesso (auth, mas sem must.change.password)
Route::middleware('auth')->group(function () {
    Route::get('/primeiro-acesso', [PrimeiroAcessoController::class, 'create'])->name('primeiro-acesso');
    Route::post('/primeiro-acesso', [PrimeiroAcessoController::class, 'store'])->name('primeiro-acesso.store');

    Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout');
});

// Rotas internas protegidas
Route::middleware(['auth', 'must.change.password'])->group(function () {
    Route::put('/minha-conta/senha', [AlteracaoSenhaController::class, 'update'])->name('senha.update');
});

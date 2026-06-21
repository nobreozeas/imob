<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PrimeiroAcessoRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PrimeiroAcessoController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function create(): Response
    {
        return Inertia::render('Auth/PrimeiroAcesso');
    }

    public function store(PrimeiroAcessoRequest $request): RedirectResponse
    {
        $this->authService->trocarSenha(auth()->user(), $request->input('password'));

        return redirect()->route('dashboard');
    }
}

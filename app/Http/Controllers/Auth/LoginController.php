<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Verifica se usuário existe e está inativo antes de tentar login
        $user = User::where('email', $email)->first();
        if ($user && ! $user->estaAtivo()) {
            return back()->withErrors([
                'email' => 'Seu acesso está inativo. Entre em contato com o administrador.',
            ]);
        }

        if (! $this->authService->tentarLogin($email, $password, $remember)) {
            return back()->withErrors([
                'email' => 'Email ou senha inválidos.',
            ]);
        }

        $request->session()->regenerate();

        if (auth()->user()->deve_alterar_senha) {
            return redirect()->route('primeiro-acesso');
        }

        return redirect()->intended(route('dashboard'));
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RecuperacaoSenhaRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

class RecuperacaoSenhaController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/RecuperacaoSenha');
    }

    public function store(RecuperacaoSenhaRequest $request): RedirectResponse
    {
        $email = $request->input('email');

        // Só envia se o usuário existir e estiver ativo (mas mensagem sempre genérica)
        $user = User::where('email', $email)->first();
        if ($user && $user->estaAtivo()) {
            Password::sendResetLink(['email' => $email]);
        }

        return back()->with('status', 'Se este email estiver cadastrado, enviaremos as instruções para redefinição de senha.');
    }
}

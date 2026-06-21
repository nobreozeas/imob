<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RedefinirSenhaRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RedefinirSenhaController extends Controller
{
    public function create(Request $request, string $token): Response
    {
        return Inertia::render('Auth/RedefinirSenha', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function store(RedefinirSenhaRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->input('password')),
                    'remember_token' => Str::random(60),
                    'deve_alterar_senha' => false,
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PasswordReset) {
            return redirect()->route('login')->with('status', 'Senha redefinida com sucesso. Faça login com sua nova senha.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}

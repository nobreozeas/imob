<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Notifications\PrimeiroAcessoNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function tentarLogin(string $email, string $password, bool $remember = false): bool
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return false;
        }

        if (! $user->estaAtivo()) {
            return false;
        }

        Auth::login($user, $remember);
        $this->registrarAcesso($user);

        return true;
    }

    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    public function gerarSenhaTemporaria(): string
    {
        return Str::random(12);
    }

    public function enviarAcessoInicial(User $user): void
    {
        $senhaTemporaria = $this->gerarSenhaTemporaria();

        $user->update([
            'password' => $senhaTemporaria,
            'deve_alterar_senha' => true,
            'convite_enviado_em' => now(),
        ]);

        $urlSistema = rtrim((string) config('app.url'), '/').'/login';

        $user->notify(new PrimeiroAcessoNotification($senhaTemporaria, $urlSistema));
    }

    public function registrarAcesso(User $user): void
    {
        $user->update(['ultimo_acesso_em' => now()]);
    }

    public function trocarSenha(User $user, string $novaSenha): void
    {
        $dados = ['password' => $novaSenha, 'deve_alterar_senha' => false];

        if (is_null($user->primeiro_acesso_em)) {
            $dados['primeiro_acesso_em'] = now();
        }

        $user->update($dados);
    }

    public function usuarioInativo(string $email): bool
    {
        $user = User::where('email', $email)->first();

        return $user && ! $user->estaAtivo();
    }
}

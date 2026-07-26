<?php

namespace App\Services\Usuarios;

use App\Models\User;
use App\Notifications\PrimeiroAcessoNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UsuarioService
{
    public function criar(array $dados): User
    {
        $senhaTemporaria = Str::random(12);

        $user = User::create([
            'name'              => $dados['name'],
            'email'             => $dados['email'],
            'password'          => Hash::make($senhaTemporaria),
            'status'            => $dados['status'],
            'deve_alterar_senha' => true,
            'criado_por'        => $dados['criado_por'],
        ]);

        $user->assignRole($dados['role']);

        $user->notify(new PrimeiroAcessoNotification($senhaTemporaria, config('app.url')));

        return $user;
    }

    public function atualizar(User $user, array $dados): User
    {
        $user->update([
            'name'   => $dados['name'],
            'status' => $dados['status'],
        ]);

        $user->syncRoles([$dados['role']]);

        return $user->fresh();
    }

    public function ativar(User $user): void
    {
        if ($user->status === User::STATUS_ATIVO) {
            throw ValidationException::withMessages([
                'status' => 'O usuário já está ativo.',
            ]);
        }

        $user->update(['status' => User::STATUS_ATIVO]);
    }

    public function inativar(User $user): void
    {
        if ($user->status === User::STATUS_INATIVO) {
            throw ValidationException::withMessages([
                'status' => 'O usuário já está inativo.',
            ]);
        }

        $user->update(['status' => User::STATUS_INATIVO]);
    }

    public function reenviarAcesso(User $user): void
    {
        if (! $user->deve_alterar_senha) {
            throw ValidationException::withMessages([
                'acesso' => 'O primeiro acesso já foi concluído por este usuário.',
            ]);
        }

        if ($user->status !== User::STATUS_ATIVO) {
            throw ValidationException::withMessages([
                'acesso' => 'Não é possível reenviar acesso para um usuário inativo.',
            ]);
        }

        $senhaTemporaria = Str::random(12);
        $user->update(['password' => Hash::make($senhaTemporaria)]);
        $user->notify(new PrimeiroAcessoNotification($senhaTemporaria, config('app.url')));
    }
}

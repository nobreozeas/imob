<?php

namespace App\Policies;

use App\Models\User;

class UsuarioPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('usuarios.viewAny');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('usuarios.view');
    }

    public function create(User $user): bool
    {
        return $user->can('usuarios.create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('usuarios.update');
    }

    public function alterarStatus(User $user, User $model): bool
    {
        return $user->can('usuarios.alterar-status');
    }

    public function reenviarAcesso(User $user, User $model): bool
    {
        return $user->can('usuarios.reenviar-acesso');
    }
}

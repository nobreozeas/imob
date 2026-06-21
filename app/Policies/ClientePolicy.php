<?php

namespace App\Policies;

use App\Models\Cliente;
use App\Models\User;

class ClientePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('clientes.ver');
    }

    public function view(User $user, Cliente $cliente): bool
    {
        return $user->can('clientes.ver');
    }

    public function create(User $user): bool
    {
        return $user->can('clientes.criar');
    }

    public function update(User $user, Cliente $cliente): bool
    {
        return $user->can('clientes.editar');
    }

    public function alterarStatus(User $user, Cliente $cliente): bool
    {
        return $user->can('clientes.ativar-inativar');
    }

    public function delete(User $user, Cliente $cliente): bool
    {
        return false;
    }
}

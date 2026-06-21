<?php

namespace App\Policies;

use App\Models\Imovel;
use App\Models\User;

class ImovelPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('imoveis.viewAny');
    }

    public function view(User $user, Imovel $imovel): bool
    {
        return $user->can('imoveis.view');
    }

    public function create(User $user): bool
    {
        return $user->can('imoveis.create');
    }

    public function update(User $user, Imovel $imovel): bool
    {
        return $user->can('imoveis.update');
    }

    public function alterarStatus(User $user, Imovel $imovel): bool
    {
        return $user->can('imoveis.alterar-status');
    }
}

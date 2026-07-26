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

    public function delete(User $user, Imovel $imovel): bool
    {
        return $user->can('imoveis.destroy');
    }

    public function restore(User $user, Imovel $imovel): bool
    {
        return $user->can('imoveis.restore');
    }

    public function gerenciarFotos(User $user, Imovel $imovel): bool
    {
        return $user->can('imoveis.gerenciar-fotos');
    }

    public function gerenciarDocumentos(User $user, Imovel $imovel): bool
    {
        return $user->can('imoveis.gerenciar-documentos');
    }
}

<?php

namespace App\Policies;

use App\Models\CategoriaFinanceira;
use App\Models\User;

class CategoriaFinanceiraPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('financeiro.visualizar');
    }

    public function view(User $user, CategoriaFinanceira $categoria): bool
    {
        return $user->can('financeiro.visualizar');
    }

    public function create(User $user): bool
    {
        return $user->can('financeiro.criar');
    }

    public function update(User $user, CategoriaFinanceira $categoria): bool
    {
        return $user->can('financeiro.editar');
    }

    public function delete(User $user, CategoriaFinanceira $categoria): bool
    {
        return $user->can('financeiro.excluir');
    }
}

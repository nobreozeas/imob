<?php

namespace App\Policies;

use App\Models\User;

class RelatorioFinanceiroPolicy
{
    public function view(User $user): bool
    {
        return $user->can('financeiro.visualizar_relatorios');
    }
}

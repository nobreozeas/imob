<?php

namespace App\Policies;

use App\Models\RepasseProprietario;
use App\Models\User;

class RepasseProprietarioPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('repasses.visualizar');
    }

    public function marcarComoPago(User $user, RepasseProprietario $repasse): bool
    {
        return $user->can('repasses.marcar-como-pago');
    }
}

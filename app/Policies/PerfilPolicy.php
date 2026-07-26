<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class PerfilPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('perfis.viewAny');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can('perfis.view');
    }
}

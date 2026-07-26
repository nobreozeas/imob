<?php

namespace App\Http\Controllers\Perfis;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Inertia\Inertia;
use Inertia\Response;

class PerfilController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Role::class);

        $perfis = Role::withCount('users')
            ->with('permissions')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Perfis/Index', [
            'perfis' => $perfis,
        ]);
    }

    public function show(Role $perfil): Response
    {
        $this->authorize('view', $perfil);

        $perfil->load('permissions');

        $permissoesPorModulo = $perfil->permissions
            ->groupBy(fn ($p) => explode('.', $p->name)[0])
            ->map(fn ($perms) => $perms->pluck('name')->toArray())
            ->toArray();

        return Inertia::render('Admin/Perfis/Show', [
            'perfil'               => $perfil,
            'permissoesPorModulo'  => $permissoesPorModulo,
        ]);
    }
}

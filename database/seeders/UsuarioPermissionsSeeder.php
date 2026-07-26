<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsuarioPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissoes = [
            'usuarios.viewAny',
            'usuarios.view',
            'usuarios.create',
            'usuarios.update',
            'usuarios.alterar-status',
            'usuarios.reenviar-acesso',
        ];

        foreach ($permissoes as $permissao) {
            Permission::firstOrCreate(
                ['name' => $permissao, 'guard_name' => 'web'],
                ['id' => Str::uuid()->toString()],
            );
        }

        $admin = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['id' => Str::uuid()->toString()],
        );

        $admin->givePermissionTo($permissoes);
    }
}

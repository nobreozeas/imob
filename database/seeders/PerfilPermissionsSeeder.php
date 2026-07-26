<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PerfilPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissoes = [
            'perfis.viewAny',
            'perfis.view',
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

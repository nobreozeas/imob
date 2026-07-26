<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FinanceiroPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissoes = [
            'financeiro.visualizar',
            'financeiro.criar',
            'financeiro.editar',
            'financeiro.excluir',
            'financeiro.cancelar',
            'financeiro.estornar',
            'financeiro.marcar_como_pago',
            'financeiro.visualizar_relatorios',
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

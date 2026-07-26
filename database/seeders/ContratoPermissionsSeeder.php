<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContratoPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissoes = [
            'contratos.viewAny',
            'contratos.view',
            'contratos.create',
            'contratos.update',
            'contratos.ativar',
            'contratos.cancelar',
            'contratos.encerrar',
            'contratos.rescindir',
            'contratos.documentos',
            'contratos.registrar-pagamento',
            'contratos.gerenciar-caucao',
            'contratos.renovar',
            'repasses.visualizar',
            'repasses.marcar-como-pago',
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

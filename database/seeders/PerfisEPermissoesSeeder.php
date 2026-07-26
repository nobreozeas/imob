<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PerfisEPermissoesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'financeiro', 'atendente', 'corretor'];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role, 'guard_name' => 'web'],
                ['id' => Str::uuid()->toString()],
            );
        }

        $admin     = Role::where('name', 'admin')->first();
        $financeiro = Role::where('name', 'financeiro')->first();
        $atendente  = Role::where('name', 'atendente')->first();
        $corretor   = Role::where('name', 'corretor')->first();

        // Administrador: acesso total a tudo
        $todasPermissoes = Permission::all()->pluck('name')->toArray();
        $admin->syncPermissions($todasPermissoes);

        // Financeiro: imoveis (visualizar), clientes (visualizar), contratos (visualizar),
        // usuarios (sem acesso), perfis (sem acesso)
        $financeiro->syncPermissions([
            'imoveis.viewAny',
            'imoveis.view',
            'clientes.ver',
            'contratos.viewAny',
            'contratos.view',
            'contratos.registrar-pagamento',
            'contratos.gerenciar-caucao',
            'repasses.visualizar',
            'repasses.marcar-como-pago',
            'financeiro.visualizar',
            'financeiro.criar',
            'financeiro.editar',
            'financeiro.excluir',
            'financeiro.cancelar',
            'financeiro.estornar',
            'financeiro.marcar_como_pago',
            'financeiro.visualizar_relatorios',
        ]);

        // Atendente: imoveis (criar/editar), clientes (criar/editar), contratos (criar/editar)
        $atendente->syncPermissions([
            'imoveis.viewAny',
            'imoveis.view',
            'imoveis.create',
            'imoveis.update',
            'imoveis.gerenciar-fotos',
            'imoveis.gerenciar-documentos',
            'clientes.ver',
            'clientes.criar',
            'clientes.editar',
            'clientes.ativar-inativar',
            'contratos.viewAny',
            'contratos.view',
            'contratos.create',
            'contratos.update',
            'contratos.documentos',
        ]);

        // Corretor: imoveis (visualizar), clientes (visualizar limitado), contratos (visualizar)
        $corretor->syncPermissions([
            'imoveis.viewAny',
            'imoveis.view',
            'clientes.ver',
            'contratos.viewAny',
            'contratos.view',
        ]);
    }
}

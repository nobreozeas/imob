<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $senhaTemporaria = '12345678'; // Senha temporária padrão

        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@imobgestor.com.br',
            'password' => Hash::make($senhaTemporaria),
            'status' => User::STATUS_ATIVO,
            'deve_alterar_senha' => true,
        ]);

        $user->assignRole('admin');

        $this->command->info("Usuário administrador criado:");
        $this->command->info("Email: {$user->email}");
        $this->command->info("Senha temporária: {$senhaTemporaria}");
        $this->command->warn("Guarde a senha temporária e faça o login para definir uma senha definitiva.");
    }
}

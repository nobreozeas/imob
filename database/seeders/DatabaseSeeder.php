<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ImovelPermissionsSeeder::class);
        $this->call(ContratoPermissionsSeeder::class);
        $this->call(ClientePermissionsSeeder::class);
        $this->call(UsuarioPermissionsSeeder::class);
        $this->call(PerfilPermissionsSeeder::class);
        $this->call(FinanceiroPermissionsSeeder::class);
        $this->call(PerfisEPermissoesSeeder::class);
        $this->call(CategoriaFinanceiraSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(ImovelClienteDemoSeeder::class);
    }
}

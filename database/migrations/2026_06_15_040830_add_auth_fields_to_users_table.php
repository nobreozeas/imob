<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['ativo', 'inativo', 'bloqueado', 'excluido'])->default('ativo')->after('email');
            $table->boolean('deve_alterar_senha')->default(true)->after('status');
            $table->timestamp('ultimo_acesso_em')->nullable()->after('deve_alterar_senha');
            $table->timestamp('primeiro_acesso_em')->nullable()->after('ultimo_acesso_em');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'deve_alterar_senha',
                'ultimo_acesso_em', 
                'primeiro_acesso_em',
            ]);
            $table->dropSoftDeletes();
        });
    }
};

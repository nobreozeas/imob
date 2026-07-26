<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imovel_dados_comerciais', function (Blueprint $table) {
            $table->dropColumn([
                'responsavel_iptu',
                'responsavel_agua',
                'responsavel_energia',
                'responsavel_condominio',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('imovel_dados_comerciais', function (Blueprint $table) {
            $table->enum('responsavel_iptu', ['proprietario', 'inquilino'])->nullable();
            $table->enum('responsavel_agua', ['proprietario', 'inquilino'])->nullable();
            $table->enum('responsavel_energia', ['proprietario', 'inquilino'])->nullable();
            $table->enum('responsavel_condominio', ['proprietario', 'inquilino'])->nullable();
        });
    }
};

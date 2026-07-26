<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos_locacao', function (Blueprint $table) {
            $table->boolean('gerar_parcelas_automaticamente')->default(true)->after('valor_taxa_administracao');
            $table->integer('quantidade_parcelas')->nullable()->after('gerar_parcelas_automaticamente');
        });
    }

    public function down(): void
    {
        Schema::table('contratos_locacao', function (Blueprint $table) {
            $table->dropColumn(['gerar_parcelas_automaticamente', 'quantidade_parcelas']);
        });
    }
};

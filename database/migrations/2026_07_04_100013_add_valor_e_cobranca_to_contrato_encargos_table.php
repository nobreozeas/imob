<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrato_encargos', function (Blueprint $table) {
            $table->decimal('valor_estimado', 12, 2)->nullable()->after('responsavel');
            $table->boolean('cobrar_junto_aluguel')->default(false)->after('valor_estimado');
        });
    }

    public function down(): void
    {
        Schema::table('contrato_encargos', function (Blueprint $table) {
            $table->dropColumn(['valor_estimado', 'cobrar_junto_aluguel']);
        });
    }
};

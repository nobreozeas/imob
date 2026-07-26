<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrato_multas', function (Blueprint $table) {
            $table->integer('dias_tolerancia_atraso')->nullable()->after('valor_juros_dia');
        });
    }

    public function down(): void
    {
        Schema::table('contrato_multas', function (Blueprint $table) {
            $table->dropColumn('dias_tolerancia_atraso');
        });
    }
};

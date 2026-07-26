<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos_locacao', function (Blueprint $table) {
            $table->foreignUuid('contrato_anterior_id')->nullable()->after('corretor_id')->constrained('contratos_locacao');
        });
    }

    public function down(): void
    {
        Schema::table('contratos_locacao', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contrato_anterior_id');
        });
    }
};

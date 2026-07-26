<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias_financeiras', function (Blueprint $table) {
            $table->boolean('impacta_resultado')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('categorias_financeiras', function (Blueprint $table) {
            $table->dropColumn('impacta_resultado');
        });
    }
};

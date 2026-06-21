<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato_multas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contrato_id')->unique()->constrained('contratos_locacao')->cascadeOnDelete();
            $table->boolean('possui_multa_atraso')->default(false);
            $table->decimal('percentual_multa_atraso', 5, 2)->nullable();
            $table->decimal('valor_juros_dia', 5, 4)->nullable();
            $table->boolean('possui_multa_rescisao')->default(false);
            $table->decimal('percentual_multa_rescisao', 5, 2)->nullable();
            $table->enum('base_calculo_rescisao', ['alugueis_restantes', 'valor_fixo'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_multas');
    }
};

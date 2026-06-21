<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato_caucoes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contrato_id')->unique()->constrained('contratos_locacao')->cascadeOnDelete();
            $table->boolean('possui_caucao')->default(false);
            $table->enum('tipo_caucao', ['dinheiro', 'imovel', 'fiador', 'seguro_fianca', 'deposito_bancario'])->nullable();
            $table->decimal('valor_caucao', 12, 2)->nullable();
            $table->date('data_recebimento_caucao')->nullable();
            $table->enum('status_caucao', ['recebida', 'devolvida', 'retida_parcialmente', 'retida_totalmente'])->default('recebida');
            $table->decimal('valor_devolvido', 12, 2)->nullable();
            $table->date('data_devolucao_caucao')->nullable();
            $table->text('observacao_caucao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_caucoes');
    }
};

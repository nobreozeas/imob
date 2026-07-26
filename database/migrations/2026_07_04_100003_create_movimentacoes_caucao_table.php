<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimentacoes_caucao', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('caucao_contrato_id')->constrained('contrato_caucoes')->cascadeOnDelete();
            $table->enum('tipo_movimentacao', ['recebimento', 'devolucao', 'abatimento', 'retencao_parcial', 'retencao_integral', 'ajuste']);
            $table->decimal('valor', 12, 2);
            $table->date('data_movimentacao');
            $table->enum('forma_movimentacao', ['pix', 'dinheiro', 'cartao_credito', 'cartao_debito', 'transferencia', 'boleto', 'outro'])->nullable();
            $table->text('descricao')->nullable();
            $table->string('referencia_debito')->nullable();
            $table->foreignUuid('criado_por')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimentacoes_caucao');
    }
};

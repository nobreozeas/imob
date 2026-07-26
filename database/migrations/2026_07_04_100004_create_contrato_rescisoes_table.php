<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato_rescisoes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contrato_id')->unique()->constrained('contratos_locacao')->cascadeOnDelete();
            $table->date('data_rescisao');
            $table->text('motivo');
            $table->enum('solicitado_por', ['locatario', 'locador', 'imobiliaria', 'acordo']);
            $table->integer('meses_restantes')->nullable();
            $table->decimal('valor_multa_rescisao', 12, 2)->nullable();
            $table->decimal('valor_desconto', 12, 2)->nullable();
            $table->decimal('valor_final_multa', 12, 2)->nullable();
            $table->decimal('debitos_em_aberto', 12, 2)->default(0);
            $table->decimal('valor_caucao_retida', 12, 2)->nullable();
            $table->decimal('valor_caucao_abatida', 12, 2)->nullable();
            $table->decimal('valor_caucao_devolvida', 12, 2)->nullable();
            $table->enum('destino_imovel', ['disponivel', 'inativo']);
            $table->enum('acao_parcelas_futuras', ['cancelar_parcelas_futuras', 'manter_parcelas_futuras']);
            $table->text('observacoes')->nullable();
            $table->foreignUuid('criado_por')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_rescisoes');
    }
};

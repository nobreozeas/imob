<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimentacoes_financeiras', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('tipo', ['entrada', 'saida']);
            $table->enum('categoria', ['aluguel', 'caucao', 'multa', 'juros', 'repasse_proprietario', 'devolucao_caucao']);
            $table->text('descricao')->nullable();
            $table->decimal('valor', 12, 2);
            $table->date('data_movimentacao');
            $table->enum('forma_pagamento', ['pix', 'dinheiro', 'cartao_credito', 'cartao_debito', 'transferencia', 'boleto', 'outro'])->nullable();
            $table->foreignUuid('contrato_id')->nullable()->constrained('contratos_locacao');
            $table->foreignUuid('parcela_aluguel_id')->nullable()->constrained('parcelas_aluguel');
            $table->foreignUuid('repasse_proprietario_id')->nullable()->constrained('repasses_proprietarios');
            $table->foreignUuid('caucao_contrato_id')->nullable()->constrained('contrato_caucoes');
            $table->foreignUuid('criado_por')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimentacoes_financeiras');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcelas_aluguel', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contrato_id')->constrained('contratos_locacao')->cascadeOnDelete();
            $table->integer('mes_referencia');
            $table->integer('ano_referencia');
            $table->date('data_vencimento');
            $table->decimal('valor_aluguel', 12, 2);
            $table->decimal('valor_encargos', 12, 2)->default(0);
            $table->decimal('valor_multa_atraso', 12, 2)->default(0);
            $table->decimal('valor_juros_atraso', 12, 2)->default(0);
            $table->decimal('valor_desconto', 12, 2)->default(0);
            $table->decimal('valor_total', 12, 2);
            $table->decimal('valor_pago', 12, 2)->default(0);
            $table->date('data_pagamento')->nullable();
            $table->enum('forma_pagamento', ['pix', 'dinheiro', 'cartao_credito', 'cartao_debito', 'transferencia', 'boleto', 'outro'])->nullable();
            $table->enum('status', ['pendente', 'pago', 'vencido', 'cancelado', 'pago_parcial'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->unique(['contrato_id', 'mes_referencia', 'ano_referencia']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcelas_aluguel');
    }
};

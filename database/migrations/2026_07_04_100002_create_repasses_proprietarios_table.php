<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repasses_proprietarios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contrato_id')->constrained('contratos_locacao')->cascadeOnDelete();
            $table->foreignUuid('imovel_id')->constrained('imoveis');
            $table->foreignUuid('proprietario_id')->constrained('clientes');
            $table->foreignUuid('parcela_aluguel_id')->unique()->constrained('parcelas_aluguel')->cascadeOnDelete();
            $table->decimal('valor_bruto', 12, 2);
            $table->decimal('valor_taxa_administracao', 12, 2);
            $table->decimal('valor_liquido', 12, 2);
            $table->enum('status', ['pendente', 'pago', 'cancelado'])->default('pendente');
            $table->date('data_pagamento')->nullable();
            $table->enum('forma_pagamento', ['pix', 'transferencia', 'dinheiro'])->nullable();
            $table->text('motivo_cancelamento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repasses_proprietarios');
    }
};

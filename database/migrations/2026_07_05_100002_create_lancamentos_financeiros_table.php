<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lancamentos_financeiros', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo')->unique();
            $table->enum('tipo', ['entrada', 'saida']);
            $table->foreignUuid('categoria_financeira_id')->constrained('categorias_financeiras');
            $table->foreignUuid('contrato_id')->nullable()->constrained('contratos_locacao')->nullOnDelete();
            $table->foreignUuid('parcela_aluguel_id')->nullable()->constrained('parcelas_aluguel')->nullOnDelete();
            $table->foreignUuid('repasse_proprietario_id')->nullable()->constrained('repasses_proprietarios')->nullOnDelete();
            $table->foreignUuid('caucao_contrato_id')->nullable()->constrained('contrato_caucoes')->nullOnDelete();
            $table->foreignUuid('movimentacao_caucao_id')->nullable()->constrained('movimentacoes_caucao')->nullOnDelete();
            $table->foreignUuid('imovel_id')->nullable()->constrained('imoveis')->nullOnDelete();
            $table->foreignUuid('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->text('descricao')->nullable();
            $table->decimal('valor', 12, 2);
            $table->date('data_vencimento')->nullable();
            $table->date('data_pagamento')->nullable();
            $table->enum('forma_pagamento', ['pix', 'dinheiro', 'cartao_credito', 'cartao_debito', 'transferencia', 'boleto', 'cheque', 'outro'])->nullable();
            $table->enum('status', ['pendente', 'pago', 'cancelado', 'estornado']);
            $table->enum('origem', ['manual', 'pagamento_aluguel', 'repasse_proprietario', 'caucao', 'movimentacao_caucao', 'despesa', 'receita_diversa', 'ajuste']);
            $table->text('observacoes')->nullable();
            $table->text('motivo_cancelamento')->nullable();
            $table->text('motivo_estorno')->nullable();
            $table->foreignUuid('criado_por')->nullable()->constrained('users');
            $table->foreignUuid('pago_por')->nullable()->constrained('users');
            $table->foreignUuid('cancelado_por')->nullable()->constrained('users');
            $table->foreignUuid('estornado_por')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('data_pagamento');
            $table->index('data_vencimento');
            $table->index('categoria_financeira_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lancamentos_financeiros');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato_renovacoes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contrato_original_id')->constrained('contratos_locacao');
            $table->foreignUuid('novo_contrato_id')->unique()->constrained('contratos_locacao');
            $table->date('data_renovacao');
            $table->decimal('valor_aluguel_anterior', 12, 2);
            $table->decimal('valor_aluguel_novo', 12, 2);
            $table->date('data_inicio_anterior');
            $table->date('data_fim_anterior')->nullable();
            $table->date('nova_data_inicio');
            $table->date('nova_data_fim')->nullable();
            $table->boolean('manter_encargos')->default(true);
            $table->boolean('manter_regras_multa')->default(true);
            $table->boolean('gerar_novas_parcelas')->default(true);
            $table->enum('caucao_acao', ['manter', 'devolver', 'complementar'])->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignUuid('criado_por')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_renovacoes');
    }
};

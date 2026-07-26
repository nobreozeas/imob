<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historicos_financeiros', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lancamento_financeiro_id')->nullable()->constrained('lancamentos_financeiros')->nullOnDelete();
            $table->string('entidade_tipo');
            $table->uuid('entidade_id');
            $table->string('acao');
            $table->text('descricao')->nullable();
            $table->json('dados_anteriores')->nullable();
            $table->json('dados_novos')->nullable();
            $table->foreignUuid('criado_por')->nullable()->constrained('users');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historicos_financeiros');
    }
};

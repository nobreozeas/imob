<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato_historicos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contrato_id')->constrained('contratos_locacao')->cascadeOnDelete();
            $table->enum('tipo_evento', ['criacao', 'ativacao', 'cancelamento', 'encerramento', 'rescisao', 'alteracao', 'documento_adicionado', 'assinatura_pendente']);
            $table->text('descricao');
            $table->json('dados_anteriores')->nullable();
            $table->json('dados_novos')->nullable();
            $table->foreignUuid('usuario_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_historicos');
    }
};

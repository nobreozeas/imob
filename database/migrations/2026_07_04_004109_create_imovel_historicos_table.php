<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imovel_historicos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('imovel_id')->constrained('imoveis')->cascadeOnDelete();
            $table->enum('tipo_evento', [
                'criacao',
                'atualizacao',
                'alteracao_status',
                'foto_adicionada',
                'foto_removida',
                'documento_adicionado',
                'documento_removido',
                'exclusao',
                'restauracao',
            ]);
            $table->text('descricao');
            $table->json('dados_anteriores')->nullable();
            $table->json('dados_novos')->nullable();
            $table->foreignUuid('usuario_id')->nullable()->constrained('users');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imovel_historicos');
    }
};

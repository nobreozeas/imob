<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente_dados_inquilino', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cliente_id')->unique()->constrained('clientes')->cascadeOnDelete();
            $table->string('profissao')->nullable();
            $table->decimal('renda_mensal', 10, 2)->nullable();
            $table->string('local_trabalho')->nullable();
            $table->string('telefone_comercial')->nullable();
            $table->string('contato_emergencia')->nullable();
            $table->text('observacoes_cadastrais')->nullable();
            $table->text('restricoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_dados_inquilino');
    }
};

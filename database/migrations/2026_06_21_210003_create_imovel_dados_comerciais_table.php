<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imovel_dados_comerciais', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('imovel_id')->unique()->constrained('imoveis')->cascadeOnDelete();
            $table->decimal('valor_aluguel', 12, 2)->nullable();
            $table->decimal('valor_venda', 12, 2)->nullable();
            $table->decimal('valor_condominio', 10, 2)->nullable();
            $table->decimal('valor_iptu', 10, 2)->nullable();
            $table->boolean('condominio_incluso')->default(false);
            $table->enum('responsavel_iptu', ['proprietario', 'inquilino'])->nullable();
            $table->enum('responsavel_agua', ['proprietario', 'inquilino'])->nullable();
            $table->enum('responsavel_energia', ['proprietario', 'inquilino'])->nullable();
            $table->enum('responsavel_condominio', ['proprietario', 'inquilino'])->nullable();
            $table->decimal('valor_caucao_sugerido', 12, 2)->nullable();
            $table->text('observacoes_comerciais')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imovel_dados_comerciais');
    }
};

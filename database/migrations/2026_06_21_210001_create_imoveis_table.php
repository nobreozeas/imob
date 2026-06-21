<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imoveis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo')->unique();
            $table->string('titulo');
            $table->enum('tipo', ['apartamento', 'casa', 'comercial', 'sala', 'galpao', 'terreno', 'rural', 'outro']);
            $table->enum('finalidade', ['aluguel', 'venda', 'aluguel_venda']);
            $table->enum('status', ['disponivel', 'reservado', 'alugado', 'em_manutencao', 'inativo'])->default('disponivel');
            $table->foreignUuid('proprietario_id')->constrained('clientes');
            $table->foreignUuid('corretor_id')->nullable()->constrained('users');
            $table->text('descricao')->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->char('estado', 2)->nullable();
            $table->string('ponto_referencia')->nullable();
            $table->foreignUuid('criado_por')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imoveis');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('tipo_pessoa', ['fisica', 'juridica'])->default('fisica');
            $table->string('nome')->nullable();
            $table->string('razao_social')->nullable();
            $table->string('nome_fantasia')->nullable();
            $table->string('cpf', 14)->unique()->nullable();
            $table->string('cnpj', 18)->unique()->nullable();
            $table->string('rg')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('telefone_principal')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('telefone_secundario')->nullable();
            $table->string('email_principal')->nullable();
            $table->string('email_alternativo')->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();
            $table->string('ponto_referencia')->nullable();
            $table->text('observacoes')->nullable();
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->foreignUuid('criado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

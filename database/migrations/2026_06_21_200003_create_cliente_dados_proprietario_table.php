<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente_dados_proprietario', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cliente_id')->unique()->constrained('clientes')->cascadeOnDelete();
            $table->string('banco')->nullable();
            $table->string('agencia')->nullable();
            $table->string('conta')->nullable();
            $table->string('tipo_conta')->nullable();
            $table->string('chave_pix')->nullable();
            $table->enum('tipo_chave_pix', ['cpf', 'cnpj', 'email', 'telefone', 'aleatoria'])->nullable();
            $table->decimal('percentual_administracao', 5, 2)->nullable();
            $table->boolean('emite_nota_fiscal')->default(false);
            $table->string('preferencia_recebimento')->nullable();
            $table->text('observacoes_repasse')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_dados_proprietario');
    }
};

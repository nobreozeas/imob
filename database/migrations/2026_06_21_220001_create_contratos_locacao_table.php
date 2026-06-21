<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos_locacao', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero')->unique();
            $table->enum('status', ['rascunho', 'aguardando_assinatura', 'ativo', 'encerrado', 'rescindido', 'cancelado'])->default('rascunho');
            $table->enum('tipo_contrato', ['residencial', 'comercial', 'temporada'])->default('residencial');
            $table->enum('objetivo_contrato', ['aluguel', 'temporada'])->default('aluguel');

            // Partes
            $table->foreignUuid('imovel_id')->constrained('imoveis');
            $table->foreignUuid('proprietario_id')->constrained('clientes');
            $table->foreignUuid('inquilino_id')->constrained('clientes');
            $table->foreignUuid('corretor_id')->nullable()->constrained('users');
            $table->foreignUuid('criado_por')->constrained('users');

            // Dados da locação
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->tinyInteger('dia_vencimento');
            $table->integer('duracao_meses')->nullable();

            // Valores
            $table->decimal('valor_aluguel', 12, 2);
            $table->enum('indice_reajuste', ['igpm', 'ipca', 'inpc', 'fixo'])->default('igpm');
            $table->integer('periodicidade_reajuste')->default(12);
            $table->date('data_primeiro_reajuste')->nullable();

            // Repasse
            $table->enum('tipo_taxa_administracao', ['percentual', 'valor_fixo'])->default('percentual');
            $table->decimal('valor_taxa_administracao', 12, 2)->default(0);
            $table->tinyInteger('dia_repasse')->nullable();
            $table->enum('forma_repasse', ['pix', 'transferencia', 'dinheiro'])->default('pix');

            // Dados bancários do proprietário (cópia no momento do contrato)
            $table->string('banco')->nullable();
            $table->string('agencia', 20)->nullable();
            $table->string('conta', 20)->nullable();
            $table->enum('tipo_conta', ['corrente', 'poupanca'])->nullable();
            $table->string('pix_key')->nullable();

            // Encerramento / Rescisão
            $table->date('data_encerramento')->nullable();
            $table->text('motivo_encerramento')->nullable();
            $table->date('data_rescisao')->nullable();
            $table->text('motivo_rescisao')->nullable();
            $table->enum('parte_requerente', ['proprietario', 'inquilino', 'ambos'])->nullable();

            $table->text('observacoes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos_locacao');
    }
};

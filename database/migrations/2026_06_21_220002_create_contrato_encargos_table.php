<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato_encargos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contrato_id')->constrained('contratos_locacao')->cascadeOnDelete();
            $table->enum('tipo_encargo', ['iptu', 'condominio', 'agua', 'energia', 'gas', 'internet', 'outros']);
            $table->enum('responsavel', ['proprietario', 'inquilino', 'nao_se_aplica'])->default('nao_se_aplica');
            $table->string('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_encargos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato_documentos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contrato_id')->constrained('contratos_locacao')->cascadeOnDelete();
            $table->string('caminho');
            $table->string('nome_original');
            $table->enum('tipo', ['contrato_assinado', 'laudo_vistoria', 'comprovante_caucao', 'outros'])->default('outros');
            $table->foreignUuid('criado_por')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_documentos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imovel_documentos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('imovel_id')->constrained('imoveis')->cascadeOnDelete();
            $table->string('caminho');
            $table->string('nome_original');
            $table->string('tipo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imovel_documentos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imovel_fotos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('imovel_id')->constrained('imoveis')->cascadeOnDelete();
            $table->string('caminho');
            $table->string('nome_original');
            $table->boolean('is_principal')->default(false);
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imovel_fotos');
    }
};

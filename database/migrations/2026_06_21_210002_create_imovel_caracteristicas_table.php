<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imovel_caracteristicas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('imovel_id')->unique()->constrained('imoveis')->cascadeOnDelete();
            $table->decimal('area_total', 10, 2)->nullable();
            $table->decimal('area_construida', 10, 2)->nullable();
            $table->unsignedInteger('quartos')->default(0);
            $table->unsignedInteger('suites')->default(0);
            $table->unsignedInteger('banheiros')->default(0);
            $table->unsignedInteger('vagas_garagem')->default(0);
            $table->boolean('mobiliado')->default(false);
            $table->boolean('aceita_pet')->default(false);
            $table->boolean('possui_piscina')->default(false);
            $table->boolean('possui_quintal')->default(false);
            $table->boolean('possui_varanda')->default(false);
            $table->text('outras_caracteristicas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imovel_caracteristicas');
    }
};

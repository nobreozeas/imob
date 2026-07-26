<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE contratos_locacao DROP CONSTRAINT contratos_locacao_status_check');
        DB::statement("ALTER TABLE contratos_locacao ADD CONSTRAINT contratos_locacao_status_check CHECK (status IN ('rascunho', 'aguardando_assinatura', 'ativo', 'vencido', 'encerrado', 'rescindido', 'cancelado'))");

        Schema::table('contratos_locacao', function (Blueprint $table) {
            $table->dropColumn(['data_rescisao', 'motivo_rescisao', 'parte_requerente']);
        });
    }

    public function down(): void
    {
        Schema::table('contratos_locacao', function (Blueprint $table) {
            $table->date('data_rescisao')->nullable();
            $table->text('motivo_rescisao')->nullable();
            $table->enum('parte_requerente', ['proprietario', 'inquilino', 'ambos'])->nullable();
        });

        DB::statement('UPDATE contratos_locacao SET status = ? WHERE status = ?', ['encerrado', 'vencido']);

        DB::statement('ALTER TABLE contratos_locacao DROP CONSTRAINT contratos_locacao_status_check');
        DB::statement("ALTER TABLE contratos_locacao ADD CONSTRAINT contratos_locacao_status_check CHECK (status IN ('rascunho', 'aguardando_assinatura', 'ativo', 'encerrado', 'rescindido', 'cancelado'))");
    }
};

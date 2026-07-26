<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE contrato_historicos DROP CONSTRAINT contrato_historicos_tipo_evento_check');
        DB::statement("ALTER TABLE contrato_historicos ADD CONSTRAINT contrato_historicos_tipo_evento_check CHECK (tipo_evento IN ('criacao', 'ativacao', 'cancelamento', 'encerramento', 'rescisao', 'alteracao', 'documento_adicionado', 'assinatura_pendente', 'renovado_para', 'criacao_por_renovacao', 'repasse_cancelado'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE contrato_historicos DROP CONSTRAINT contrato_historicos_tipo_evento_check');
        DB::statement("ALTER TABLE contrato_historicos ADD CONSTRAINT contrato_historicos_tipo_evento_check CHECK (tipo_evento IN ('criacao', 'ativacao', 'cancelamento', 'encerramento', 'rescisao', 'alteracao', 'documento_adicionado', 'assinatura_pendente'))");
    }
};

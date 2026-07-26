<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $contratosRescindidos = DB::table('contratos_locacao')
            ->where('status', 'rescindido')
            ->whereNotNull('data_rescisao')
            ->get(['id', 'data_rescisao', 'motivo_rescisao', 'parte_requerente']);

        $mapaParteRequerente = [
            'proprietario' => 'locador',
            'inquilino' => 'locatario',
            'ambos' => 'acordo',
        ];

        foreach ($contratosRescindidos as $contrato) {
            DB::table('contrato_rescisoes')->insertOrIgnore([
                'id' => (string) Str::uuid(),
                'contrato_id' => $contrato->id,
                'data_rescisao' => $contrato->data_rescisao,
                'motivo' => $contrato->motivo_rescisao ?? 'Não informado',
                'solicitado_por' => $mapaParteRequerente[$contrato->parte_requerente] ?? 'imobiliaria',
                'debitos_em_aberto' => 0,
                'destino_imovel' => 'disponivel',
                'acao_parcelas_futuras' => 'manter_parcelas_futuras',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Dados legados: não há como reverter com segurança sem duplicar informação.
    }
};

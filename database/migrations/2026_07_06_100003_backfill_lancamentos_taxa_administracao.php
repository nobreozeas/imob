<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    private const MARCADOR = 'Backfill automático (financeiro-separar-receita-taxa-administracao)';

    public function up(): void
    {
        $categoriaAluguelId = DB::table('categorias_financeiras')->where('slug', 'aluguel')->value('id');
        $categoriaTaxaId = DB::table('categorias_financeiras')->where('slug', 'taxa_administracao')->value('id');

        if (!$categoriaAluguelId || !$categoriaTaxaId) {
            return;
        }

        $repasses = DB::table('repasses_proprietarios as r')
            ->join('lancamentos_financeiros as la', function ($join) use ($categoriaAluguelId) {
                $join->on('la.parcela_aluguel_id', '=', 'r.parcela_aluguel_id')
                    ->where('la.categoria_financeira_id', '=', $categoriaAluguelId);
            })
            ->whereNotExists(function ($query) use ($categoriaTaxaId) {
                $query->select(DB::raw(1))
                    ->from('lancamentos_financeiros as lt')
                    ->whereColumn('lt.parcela_aluguel_id', 'r.parcela_aluguel_id')
                    ->where('lt.categoria_financeira_id', '=', $categoriaTaxaId);
            })
            ->select('r.id as repasse_id', 'r.contrato_id', 'r.imovel_id', 'r.proprietario_id', 'r.parcela_aluguel_id', 'r.valor_taxa_administracao', 'la.data_pagamento', 'la.criado_por')
            ->get();

        if ($repasses->isEmpty()) {
            return;
        }

        $ultimoCodigo = DB::table('lancamentos_financeiros')->orderBy('codigo', 'desc')->value('codigo');
        $sequencia = 1;
        if ($ultimoCodigo) {
            $partes = explode('-', $ultimoCodigo);
            $sequencia = ((int) end($partes)) + 1;
        }

        foreach ($repasses as $repasse) {
            DB::table('lancamentos_financeiros')->insert([
                'id' => (string) Str::uuid(),
                'codigo' => 'LF-' . now()->format('Ym') . '-' . str_pad((string) $sequencia++, 4, '0', STR_PAD_LEFT),
                'tipo' => 'entrada',
                'categoria_financeira_id' => $categoriaTaxaId,
                'contrato_id' => $repasse->contrato_id,
                'parcela_aluguel_id' => $repasse->parcela_aluguel_id,
                'imovel_id' => $repasse->imovel_id,
                'cliente_id' => null,
                'descricao' => null,
                'valor' => $repasse->valor_taxa_administracao,
                'data_vencimento' => $repasse->data_pagamento,
                'data_pagamento' => $repasse->data_pagamento,
                'forma_pagamento' => null,
                'status' => 'pago',
                'origem' => 'pagamento_aluguel',
                'observacoes' => self::MARCADOR,
                'criado_por' => $repasse->criado_por,
                'pago_por' => $repasse->criado_por,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('lancamentos_financeiros')
            ->where('observacoes', self::MARCADOR)
            ->delete();
    }
};

<?php

use Database\Seeders\CategoriaFinanceiraSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private const MAPA_ORIGEM = [
        'aluguel' => 'pagamento_aluguel',
        'repasse_proprietario' => 'repasse_proprietario',
        'caucao' => 'caucao',
        'devolucao_caucao' => 'movimentacao_caucao',
    ];

    public function up(): void
    {
        (new CategoriaFinanceiraSeeder())->run();

        DB::transaction(function () {
            $categoriasPorSlug = DB::table('categorias_financeiras')->pluck('id', 'slug');

            $movimentacoes = DB::table('movimentacoes_financeiras')->orderBy('created_at')->get();
            $sequencia = 1;

            foreach ($movimentacoes as $movimentacao) {
                $categoriaId = $categoriasPorSlug[$movimentacao->categoria] ?? null;

                if (!$categoriaId) {
                    continue;
                }

                DB::table('lancamentos_financeiros')->insert([
                    'id' => (string) Str::uuid(),
                    'codigo' => sprintf('LF-%06d', $sequencia++),
                    'tipo' => $movimentacao->tipo,
                    'categoria_financeira_id' => $categoriaId,
                    'contrato_id' => $movimentacao->contrato_id,
                    'parcela_aluguel_id' => $movimentacao->parcela_aluguel_id,
                    'repasse_proprietario_id' => $movimentacao->repasse_proprietario_id,
                    'caucao_contrato_id' => $movimentacao->caucao_contrato_id,
                    'movimentacao_caucao_id' => null,
                    'imovel_id' => null,
                    'cliente_id' => null,
                    'descricao' => $movimentacao->descricao,
                    'valor' => $movimentacao->valor,
                    'data_vencimento' => $movimentacao->data_movimentacao,
                    'data_pagamento' => $movimentacao->data_movimentacao,
                    'forma_pagamento' => $movimentacao->forma_pagamento,
                    'status' => 'pago',
                    'origem' => self::MAPA_ORIGEM[$movimentacao->categoria] ?? 'ajuste',
                    'observacoes' => null,
                    'motivo_cancelamento' => null,
                    'motivo_estorno' => null,
                    'criado_por' => $movimentacao->criado_por,
                    'pago_por' => $movimentacao->criado_por,
                    'cancelado_por' => null,
                    'estornado_por' => null,
                    'created_at' => $movimentacao->created_at,
                    'updated_at' => $movimentacao->updated_at,
                    'deleted_at' => null,
                ]);
            }

            Schema::dropIfExists('movimentacoes_financeiras');
        });
    }

    public function down(): void
    {
        Schema::create('movimentacoes_financeiras', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('tipo', ['entrada', 'saida']);
            $table->enum('categoria', ['aluguel', 'caucao', 'multa', 'juros', 'repasse_proprietario', 'devolucao_caucao']);
            $table->text('descricao')->nullable();
            $table->decimal('valor', 12, 2);
            $table->date('data_movimentacao');
            $table->enum('forma_pagamento', ['pix', 'dinheiro', 'cartao_credito', 'cartao_debito', 'transferencia', 'boleto', 'outro'])->nullable();
            $table->foreignUuid('contrato_id')->nullable()->constrained('contratos_locacao');
            $table->foreignUuid('parcela_aluguel_id')->nullable()->constrained('parcelas_aluguel');
            $table->foreignUuid('repasse_proprietario_id')->nullable()->constrained('repasses_proprietarios');
            $table->foreignUuid('caucao_contrato_id')->nullable()->constrained('contrato_caucoes');
            $table->foreignUuid('criado_por')->nullable()->constrained('users');
            $table->timestamps();
        });

        $mapaInverso = array_flip(self::MAPA_ORIGEM);

        $lancamentos = DB::table('lancamentos_financeiros')
            ->join('categorias_financeiras', 'categorias_financeiras.id', '=', 'lancamentos_financeiros.categoria_financeira_id')
            ->whereIn('lancamentos_financeiros.origem', array_values(self::MAPA_ORIGEM))
            ->select('lancamentos_financeiros.*', 'categorias_financeiras.slug as categoria_slug')
            ->get();

        foreach ($lancamentos as $lancamento) {
            DB::table('movimentacoes_financeiras')->insert([
                'id' => (string) Str::uuid(),
                'tipo' => $lancamento->tipo,
                'categoria' => $lancamento->categoria_slug,
                'descricao' => $lancamento->descricao,
                'valor' => $lancamento->valor,
                'data_movimentacao' => $lancamento->data_pagamento ?? $lancamento->data_vencimento,
                'forma_pagamento' => $lancamento->forma_pagamento,
                'contrato_id' => $lancamento->contrato_id,
                'parcela_aluguel_id' => $lancamento->parcela_aluguel_id,
                'repasse_proprietario_id' => $lancamento->repasse_proprietario_id,
                'caucao_contrato_id' => $lancamento->caucao_contrato_id,
                'criado_por' => $lancamento->criado_por,
                'created_at' => $lancamento->created_at,
                'updated_at' => $lancamento->updated_at,
            ]);
        }

        Schema::dropIfExists('lancamentos_financeiros');
    }
};

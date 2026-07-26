<?php

namespace Database\Seeders;

use App\Models\CategoriaFinanceira;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CategoriaFinanceiraSeeder extends Seeder
{
    private const SLUGS_TRANSITO = ['aluguel', 'caucao', 'devolucao_caucao', 'repasse_proprietario'];

    public function run(): void
    {
        // Esta seeder é chamada tanto pelo DatabaseSeeder quanto de dentro de uma
        // migration anterior à que adiciona `impacta_resultado`; em uma instalação
        // do zero (migrate:fresh), a coluna ainda não existe nesse ponto.
        $temColunaImpactaResultado = Schema::hasColumn('categorias_financeiras', 'impacta_resultado');
        $categorias = [
            ['nome' => 'Aluguel', 'tipo' => CategoriaFinanceira::TIPO_ENTRADA, 'slug' => 'aluguel'],
            ['nome' => 'Receita diversa', 'tipo' => CategoriaFinanceira::TIPO_ENTRADA, 'slug' => 'receita_diversa'],
            ['nome' => 'Taxa de administração', 'tipo' => CategoriaFinanceira::TIPO_ENTRADA, 'slug' => 'taxa_administracao'],
            ['nome' => 'Multa por atraso', 'tipo' => CategoriaFinanceira::TIPO_ENTRADA, 'slug' => 'multa_atraso'],
            ['nome' => 'Juros por atraso', 'tipo' => CategoriaFinanceira::TIPO_ENTRADA, 'slug' => 'juros_atraso'],
            ['nome' => 'Caução', 'tipo' => CategoriaFinanceira::TIPO_ENTRADA, 'slug' => 'caucao'],
            ['nome' => 'Ajuste positivo', 'tipo' => CategoriaFinanceira::TIPO_ENTRADA, 'slug' => 'ajuste_positivo'],

            ['nome' => 'Repasse ao proprietário', 'tipo' => CategoriaFinanceira::TIPO_SAIDA, 'slug' => 'repasse_proprietario'],
            ['nome' => 'Despesa operacional', 'tipo' => CategoriaFinanceira::TIPO_SAIDA, 'slug' => 'despesa_operacional'],
            ['nome' => 'Despesa administrativa', 'tipo' => CategoriaFinanceira::TIPO_SAIDA, 'slug' => 'despesa_administrativa'],
            ['nome' => 'Fornecedor', 'tipo' => CategoriaFinanceira::TIPO_SAIDA, 'slug' => 'fornecedor'],
            ['nome' => 'Devolução de caução', 'tipo' => CategoriaFinanceira::TIPO_SAIDA, 'slug' => 'devolucao_caucao'],
            ['nome' => 'Manutenção de imóvel', 'tipo' => CategoriaFinanceira::TIPO_SAIDA, 'slug' => 'manutencao_imovel'],
            ['nome' => 'Comissão de corretor', 'tipo' => CategoriaFinanceira::TIPO_SAIDA, 'slug' => 'comissao_corretor'],
            ['nome' => 'Ajuste negativo', 'tipo' => CategoriaFinanceira::TIPO_SAIDA, 'slug' => 'ajuste_negativo'],
        ];

        foreach ($categorias as $categoria) {
            $dados = [
                'id' => (string) Str::uuid(),
                'nome' => $categoria['nome'],
                'tipo' => $categoria['tipo'],
                'ativa' => true,
            ];

            if ($temColunaImpactaResultado) {
                $dados['impacta_resultado'] = !in_array($categoria['slug'], self::SLUGS_TRANSITO, true);
            }

            CategoriaFinanceira::firstOrCreate(['slug' => $categoria['slug']], $dados);
        }
    }
}

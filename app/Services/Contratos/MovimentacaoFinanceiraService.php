<?php

namespace App\Services\Contratos;

use App\Models\CategoriaFinanceira;
use App\Models\LancamentoFinanceiro;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MovimentacaoFinanceiraService
{
    public function registrar(string $tipo, string $categoria, float $valor, array $referencias = []): LancamentoFinanceiro
    {
        $categoriaFinanceira = CategoriaFinanceira::where('slug', $categoria)->firstOrFail();
        $dataMovimentacao = $referencias['data_movimentacao'] ?? Carbon::now()->toDateString();
        $criadoPor = $referencias['criado_por'] ?? null;

        return LancamentoFinanceiro::create([
            'codigo' => $this->gerarCodigo(),
            'tipo' => $tipo,
            'categoria_financeira_id' => $categoriaFinanceira->id,
            'descricao' => $referencias['descricao'] ?? null,
            'valor' => $valor,
            'data_vencimento' => $dataMovimentacao,
            'data_pagamento' => $dataMovimentacao,
            'forma_pagamento' => $referencias['forma_pagamento'] ?? null,
            'status' => LancamentoFinanceiro::STATUS_PAGO,
            'origem' => $referencias['origem'],
            'contrato_id' => $referencias['contrato_id'] ?? null,
            'parcela_aluguel_id' => $referencias['parcela_aluguel_id'] ?? null,
            'repasse_proprietario_id' => $referencias['repasse_proprietario_id'] ?? null,
            'caucao_contrato_id' => $referencias['caucao_contrato_id'] ?? null,
            'movimentacao_caucao_id' => $referencias['movimentacao_caucao_id'] ?? null,
            'imovel_id' => $referencias['imovel_id'] ?? null,
            'cliente_id' => $referencias['cliente_id'] ?? null,
            'criado_por' => $criadoPor,
            'pago_por' => $criadoPor,
        ]);
    }

    public function gerarCodigo(): string
    {
        $prefixo = 'LF-' . now()->format('Ym') . '-';

        $ultimo = DB::selectOne(
            'SELECT codigo FROM lancamentos_financeiros WHERE codigo LIKE ? ORDER BY codigo DESC LIMIT 1',
            [$prefixo . '%'],
        );

        $seq = 1;
        if ($ultimo) {
            $partes = explode('-', $ultimo->codigo);
            $seq = ((int) end($partes)) + 1;
        }

        return $prefixo . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}

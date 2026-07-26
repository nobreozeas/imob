<?php

namespace App\Services\Financeiro;

use App\Models\LancamentoFinanceiro;
use App\Services\Contratos\MovimentacaoFinanceiraService;

class DespesaFinanceiraService
{
    public function __construct(
        private MovimentacaoFinanceiraService $movimentacaoFinanceiraService,
    ) {}

    public function criar(array $dados, string $usuarioId): LancamentoFinanceiro
    {
        $status = $dados['status'] ?? LancamentoFinanceiro::STATUS_PENDENTE;

        return LancamentoFinanceiro::create([
            'codigo' => $this->movimentacaoFinanceiraService->gerarCodigo(),
            'tipo' => LancamentoFinanceiro::TIPO_SAIDA,
            'categoria_financeira_id' => $dados['categoria_financeira_id'],
            'contrato_id' => $dados['contrato_id'] ?? null,
            'imovel_id' => $dados['imovel_id'] ?? null,
            'cliente_id' => $dados['cliente_id'] ?? null,
            'descricao' => $dados['descricao'],
            'valor' => $dados['valor'],
            'data_vencimento' => $dados['data_vencimento'] ?? null,
            'data_pagamento' => $status === LancamentoFinanceiro::STATUS_PAGO ? $dados['data_pagamento'] : null,
            'forma_pagamento' => $status === LancamentoFinanceiro::STATUS_PAGO ? $dados['forma_pagamento'] : null,
            'status' => $status,
            'origem' => LancamentoFinanceiro::ORIGEM_DESPESA,
            'observacoes' => $dados['observacoes'] ?? null,
            'criado_por' => $usuarioId,
            'pago_por' => $status === LancamentoFinanceiro::STATUS_PAGO ? $usuarioId : null,
        ]);
    }
}

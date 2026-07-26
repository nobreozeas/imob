<?php

namespace App\Services\Contratos;

use App\Models\LancamentoFinanceiro;
use App\Models\ParcelaAluguel;
use App\Models\RepasseProprietario;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RepasseProprietarioService
{
    public function __construct(
        private MovimentacaoFinanceiraService $movimentacaoFinanceiraService,
        private ContratoHistoricoService $historicoService,
    ) {}

    public function gerarPendente(ParcelaAluguel $parcela): RepasseProprietario
    {
        $contrato = $parcela->contrato;

        $valorBruto = (float) $parcela->valor_aluguel;
        $taxa = $contrato->tipo_taxa_administracao === 'percentual'
            ? round($valorBruto * (float) $contrato->valor_taxa_administracao / 100, 2)
            : (float) $contrato->valor_taxa_administracao;

        return RepasseProprietario::updateOrCreate(
            ['parcela_aluguel_id' => $parcela->id],
            [
                'contrato_id' => $contrato->id,
                'imovel_id' => $contrato->imovel_id,
                'proprietario_id' => $contrato->proprietario_id,
                'valor_bruto' => $valorBruto,
                'valor_taxa_administracao' => $taxa,
                'valor_liquido' => round($valorBruto - $taxa, 2),
                'status' => RepasseProprietario::STATUS_PENDENTE,
            ],
        );
    }

    public function marcarComoPago(RepasseProprietario $repasse, array $dados, ?string $usuarioId = null): RepasseProprietario
    {
        if ($repasse->status !== RepasseProprietario::STATUS_PENDENTE) {
            throw ValidationException::withMessages([
                'status' => 'Apenas repasses pendentes podem ser marcados como pagos.',
            ]);
        }

        return DB::transaction(function () use ($repasse, $dados, $usuarioId) {
            $repasse->update([
                'status' => RepasseProprietario::STATUS_PAGO,
                'data_pagamento' => $dados['data_pagamento'],
                'forma_pagamento' => $dados['forma_pagamento'] ?? null,
            ]);

            $this->movimentacaoFinanceiraService->registrar(
                LancamentoFinanceiro::TIPO_SAIDA,
                'repasse_proprietario',
                (float) $repasse->valor_liquido,
                [
                    'origem' => LancamentoFinanceiro::ORIGEM_REPASSE_PROPRIETARIO,
                    'data_movimentacao' => $dados['data_pagamento'],
                    'forma_pagamento' => $dados['forma_pagamento'] ?? null,
                    'contrato_id' => $repasse->contrato_id,
                    'parcela_aluguel_id' => $repasse->parcela_aluguel_id,
                    'repasse_proprietario_id' => $repasse->id,
                    'imovel_id' => $repasse->imovel_id,
                    'cliente_id' => $repasse->proprietario_id,
                    'criado_por' => $usuarioId,
                ],
            );

            return $repasse->fresh();
        });
    }

    public function cancelar(RepasseProprietario $repasse, string $motivo, ?string $usuarioId = null): RepasseProprietario
    {
        if ($repasse->status !== RepasseProprietario::STATUS_PENDENTE) {
            throw ValidationException::withMessages([
                'status' => 'Apenas repasses pendentes podem ser cancelados.',
            ]);
        }

        $repasse->update([
            'status' => RepasseProprietario::STATUS_CANCELADO,
            'motivo_cancelamento' => $motivo,
        ]);

        $this->historicoService->registrar(
            $repasse->contrato,
            'repasse_cancelado',
            "Repasse cancelado: {$motivo}",
            [],
            ['repasse_id' => $repasse->id],
            $usuarioId,
        );

        return $repasse->fresh();
    }
}

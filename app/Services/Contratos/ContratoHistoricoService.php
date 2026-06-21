<?php

namespace App\Services\Contratos;

use App\Models\ContratoHistorico;
use App\Models\ContratoLocacao;

class ContratoHistoricoService
{
    public function registrar(
        ContratoLocacao $contrato,
        string $tipoEvento,
        string $descricao,
        array $dadosAnteriores = [],
        array $dadosNovos = [],
        ?string $usuarioId = null,
    ): ContratoHistorico {
        return ContratoHistorico::create([
            'contrato_id'      => $contrato->id,
            'tipo_evento'      => $tipoEvento,
            'descricao'        => $descricao,
            'dados_anteriores' => empty($dadosAnteriores) ? null : $dadosAnteriores,
            'dados_novos'      => empty($dadosNovos) ? null : $dadosNovos,
            'usuario_id'       => $usuarioId,
        ]);
    }
}

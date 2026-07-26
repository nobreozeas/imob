<?php

namespace App\Services\Imoveis;

use App\Models\Imovel;
use App\Models\ImovelHistorico;

class ImovelHistoricoService
{
    public function registrar(
        Imovel $imovel,
        string $tipoEvento,
        string $descricao,
        array $dadosAnteriores = [],
        array $dadosNovos = [],
        ?string $usuarioId = null,
    ): ImovelHistorico {
        return ImovelHistorico::create([
            'imovel_id'        => $imovel->id,
            'tipo_evento'      => $tipoEvento,
            'descricao'        => $descricao,
            'dados_anteriores' => empty($dadosAnteriores) ? null : $dadosAnteriores,
            'dados_novos'      => empty($dadosNovos) ? null : $dadosNovos,
            'usuario_id'       => $usuarioId,
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratoRescisao extends Model
{
    use HasUuids;

    protected $table = 'contrato_rescisoes';

    const SOLICITADO_POR_LOCATARIO = 'locatario';
    const SOLICITADO_POR_LOCADOR = 'locador';
    const SOLICITADO_POR_IMOBILIARIA = 'imobiliaria';
    const SOLICITADO_POR_ACORDO = 'acordo';

    const DESTINO_DISPONIVEL = 'disponivel';
    const DESTINO_INATIVO = 'inativo';

    const ACAO_CANCELAR_PARCELAS_FUTURAS = 'cancelar_parcelas_futuras';
    const ACAO_MANTER_PARCELAS_FUTURAS = 'manter_parcelas_futuras';

    protected $fillable = [
        'contrato_id',
        'data_rescisao',
        'motivo',
        'solicitado_por',
        'meses_restantes',
        'valor_multa_rescisao',
        'valor_desconto',
        'valor_final_multa',
        'debitos_em_aberto',
        'valor_caucao_retida',
        'valor_caucao_abatida',
        'valor_caucao_devolvida',
        'destino_imovel',
        'acao_parcelas_futuras',
        'observacoes',
        'criado_por',
    ];

    protected $casts = [
        'data_rescisao' => 'date:Y-m-d',
        'meses_restantes' => 'integer',
        'valor_multa_rescisao' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'valor_final_multa' => 'decimal:2',
        'debitos_em_aberto' => 'decimal:2',
        'valor_caucao_retida' => 'decimal:2',
        'valor_caucao_abatida' => 'decimal:2',
        'valor_caucao_devolvida' => 'decimal:2',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(ContratoLocacao::class, 'contrato_id');
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }
}

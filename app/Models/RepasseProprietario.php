<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepasseProprietario extends Model
{
    use HasUuids;

    protected $table = 'repasses_proprietarios';

    const STATUS_PENDENTE = 'pendente';
    const STATUS_PAGO = 'pago';
    const STATUS_CANCELADO = 'cancelado';

    protected $fillable = [
        'contrato_id',
        'imovel_id',
        'proprietario_id',
        'parcela_aluguel_id',
        'valor_bruto',
        'valor_taxa_administracao',
        'valor_liquido',
        'status',
        'data_pagamento',
        'forma_pagamento',
        'motivo_cancelamento',
    ];

    protected $casts = [
        'valor_bruto' => 'decimal:2',
        'valor_taxa_administracao' => 'decimal:2',
        'valor_liquido' => 'decimal:2',
        'data_pagamento' => 'date:Y-m-d',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(ContratoLocacao::class, 'contrato_id');
    }

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }

    public function proprietario(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'proprietario_id');
    }

    public function parcela(): BelongsTo
    {
        return $this->belongsTo(ParcelaAluguel::class, 'parcela_aluguel_id');
    }
}

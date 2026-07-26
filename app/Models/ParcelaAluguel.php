<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ParcelaAluguel extends Model
{
    use HasUuids;

    protected $table = 'parcelas_aluguel';

    const STATUS_PENDENTE = 'pendente';
    const STATUS_PAGO = 'pago';
    const STATUS_VENCIDO = 'vencido';
    const STATUS_CANCELADO = 'cancelado';
    const STATUS_PAGO_PARCIAL = 'pago_parcial';

    protected $fillable = [
        'contrato_id',
        'mes_referencia',
        'ano_referencia',
        'data_vencimento',
        'valor_aluguel',
        'valor_encargos',
        'valor_multa_atraso',
        'valor_juros_atraso',
        'valor_desconto',
        'valor_total',
        'valor_pago',
        'data_pagamento',
        'forma_pagamento',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'mes_referencia' => 'integer',
        'ano_referencia' => 'integer',
        'data_vencimento' => 'date:Y-m-d',
        'valor_aluguel' => 'decimal:2',
        'valor_encargos' => 'decimal:2',
        'valor_multa_atraso' => 'decimal:2',
        'valor_juros_atraso' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'valor_pago' => 'decimal:2',
        'data_pagamento' => 'date:Y-m-d',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(ContratoLocacao::class, 'contrato_id');
    }

    public function repasse(): HasOne
    {
        return $this->hasOne(RepasseProprietario::class, 'parcela_aluguel_id');
    }

    public static function cancelarFuturas(ContratoLocacao $contrato, \DateTimeInterface $apartirDe): int
    {
        return static::query()
            ->where('contrato_id', $contrato->id)
            ->where('status', self::STATUS_PENDENTE)
            ->where('data_vencimento', '>', $apartirDe)
            ->update(['status' => self::STATUS_CANCELADO]);
    }
}

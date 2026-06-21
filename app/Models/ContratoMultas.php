<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratoMultas extends Model
{
    use HasUuids;

    protected $table = 'contrato_multas';

    protected $fillable = [
        'contrato_id',
        'possui_multa_atraso',
        'percentual_multa_atraso',
        'valor_juros_dia',
        'possui_multa_rescisao',
        'percentual_multa_rescisao',
        'base_calculo_rescisao',
    ];

    protected $casts = [
        'possui_multa_atraso'      => 'boolean',
        'possui_multa_rescisao'    => 'boolean',
        'percentual_multa_atraso'  => 'decimal:2',
        'valor_juros_dia'          => 'decimal:4',
        'percentual_multa_rescisao' => 'decimal:2',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(ContratoLocacao::class, 'contrato_id');
    }
}

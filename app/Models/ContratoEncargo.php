<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratoEncargo extends Model
{
    use HasUuids;

    protected $table = 'contrato_encargos';

    protected $fillable = [
        'contrato_id',
        'tipo_encargo',
        'responsavel',
        'valor_estimado',
        'cobrar_junto_aluguel',
        'observacao',
    ];

    protected $casts = [
        'valor_estimado' => 'decimal:2',
        'cobrar_junto_aluguel' => 'boolean',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(ContratoLocacao::class, 'contrato_id');
    }
}

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
        'observacao',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(ContratoLocacao::class, 'contrato_id');
    }
}

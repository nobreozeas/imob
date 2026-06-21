<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratoCaucao extends Model
{
    use HasUuids;

    protected $table = 'contrato_caucoes';

    protected $fillable = [
        'contrato_id',
        'possui_caucao',
        'tipo_caucao',
        'valor_caucao',
        'data_recebimento_caucao',
        'status_caucao',
        'valor_devolvido',
        'data_devolucao_caucao',
        'observacao_caucao',
    ];

    protected $casts = [
        'possui_caucao'          => 'boolean',
        'valor_caucao'           => 'decimal:2',
        'valor_devolvido'        => 'decimal:2',
        'data_recebimento_caucao' => 'date:Y-m-d',
        'data_devolucao_caucao'  => 'date:Y-m-d',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(ContratoLocacao::class, 'contrato_id');
    }
}

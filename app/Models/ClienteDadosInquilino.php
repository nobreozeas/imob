<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteDadosInquilino extends Model
{
    use HasUuids;

    protected $table = 'cliente_dados_inquilino';

    protected $fillable = [
        'cliente_id',
        'profissao',
        'renda_mensal',
        'local_trabalho',
        'telefone_comercial',
        'contato_emergencia',
        'observacoes_cadastrais',
        'restricoes',
    ];

    protected function casts(): array
    {
        return [
            'renda_mensal' => 'decimal:2',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}

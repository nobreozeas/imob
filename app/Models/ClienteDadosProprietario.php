<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteDadosProprietario extends Model
{
    use HasUuids;

    protected $table = 'cliente_dados_proprietario';

    protected $fillable = [
        'cliente_id',
        'banco',
        'agencia',
        'conta',
        'tipo_conta',
        'chave_pix',
        'tipo_chave_pix',
        'percentual_administracao',
        'emite_nota_fiscal',
        'preferencia_recebimento',
        'observacoes_repasse',
    ];

    protected function casts(): array
    {
        return [
            'emite_nota_fiscal' => 'boolean',
            'percentual_administracao' => 'decimal:2',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}

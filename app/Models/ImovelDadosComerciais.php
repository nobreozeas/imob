<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImovelDadosComerciais extends Model
{
    use HasUuids;

    protected $table = 'imovel_dados_comerciais';

    protected $fillable = [
        'imovel_id',
        'valor_aluguel',
        'valor_venda',
        'valor_condominio',
        'valor_iptu',
        'condominio_incluso',
        'valor_caucao_sugerido',
        'observacoes_comerciais',
    ];

    protected function casts(): array
    {
        return [
            'valor_aluguel'         => 'decimal:2',
            'valor_venda'           => 'decimal:2',
            'valor_condominio'      => 'decimal:2',
            'valor_iptu'            => 'decimal:2',
            'valor_caucao_sugerido' => 'decimal:2',
            'condominio_incluso'    => 'boolean',
        ];
    }

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }
}

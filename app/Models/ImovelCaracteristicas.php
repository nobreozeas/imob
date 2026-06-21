<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImovelCaracteristicas extends Model
{
    use HasUuids;

    protected $table = 'imovel_caracteristicas';

    protected $fillable = [
        'imovel_id',
        'area_total',
        'area_construida',
        'quartos',
        'suites',
        'banheiros',
        'vagas_garagem',
        'mobiliado',
        'aceita_pet',
        'possui_piscina',
        'possui_quintal',
        'possui_varanda',
        'outras_caracteristicas',
    ];

    protected function casts(): array
    {
        return [
            'area_total'       => 'decimal:2',
            'area_construida'  => 'decimal:2',
            'mobiliado'        => 'boolean',
            'aceita_pet'       => 'boolean',
            'possui_piscina'   => 'boolean',
            'possui_quintal'   => 'boolean',
            'possui_varanda'   => 'boolean',
        ];
    }

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }
}

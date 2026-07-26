<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaFinanceira extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'categorias_financeiras';

    const TIPO_ENTRADA = 'entrada';
    const TIPO_SAIDA = 'saida';

    protected $fillable = [
        'nome',
        'tipo',
        'slug',
        'descricao',
        'ativa',
        'impacta_resultado',
    ];

    protected $casts = [
        'ativa' => 'boolean',
        'impacta_resultado' => 'boolean',
    ];

    public function lancamentos(): HasMany
    {
        return $this->hasMany(LancamentoFinanceiro::class, 'categoria_financeira_id');
    }
}

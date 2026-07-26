<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricoFinanceiro extends Model
{
    use HasUuids;

    protected $table = 'historicos_financeiros';

    const UPDATED_AT = null;

    protected $fillable = [
        'lancamento_financeiro_id',
        'entidade_tipo',
        'entidade_id',
        'acao',
        'descricao',
        'dados_anteriores',
        'dados_novos',
        'criado_por',
    ];

    protected $casts = [
        'dados_anteriores' => 'array',
        'dados_novos' => 'array',
    ];

    public function lancamento(): BelongsTo
    {
        return $this->belongsTo(LancamentoFinanceiro::class, 'lancamento_financeiro_id');
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratoRenovacao extends Model
{
    use HasUuids;

    protected $table = 'contrato_renovacoes';

    const CAUCAO_MANTER = 'manter';
    const CAUCAO_DEVOLVER = 'devolver';
    const CAUCAO_COMPLEMENTAR = 'complementar';

    protected $fillable = [
        'contrato_original_id',
        'novo_contrato_id',
        'data_renovacao',
        'valor_aluguel_anterior',
        'valor_aluguel_novo',
        'data_inicio_anterior',
        'data_fim_anterior',
        'nova_data_inicio',
        'nova_data_fim',
        'manter_encargos',
        'manter_regras_multa',
        'gerar_novas_parcelas',
        'caucao_acao',
        'observacoes',
        'criado_por',
    ];

    protected $casts = [
        'data_renovacao' => 'date:Y-m-d',
        'valor_aluguel_anterior' => 'decimal:2',
        'valor_aluguel_novo' => 'decimal:2',
        'data_inicio_anterior' => 'date:Y-m-d',
        'data_fim_anterior' => 'date:Y-m-d',
        'nova_data_inicio' => 'date:Y-m-d',
        'nova_data_fim' => 'date:Y-m-d',
        'manter_encargos' => 'boolean',
        'manter_regras_multa' => 'boolean',
        'gerar_novas_parcelas' => 'boolean',
    ];

    public function contratoOriginal(): BelongsTo
    {
        return $this->belongsTo(ContratoLocacao::class, 'contrato_original_id');
    }

    public function novoContrato(): BelongsTo
    {
        return $this->belongsTo(ContratoLocacao::class, 'novo_contrato_id');
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }
}

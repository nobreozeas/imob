<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimentacaoCaucao extends Model
{
    use HasUuids;

    protected $table = 'movimentacoes_caucao';

    const TIPO_RECEBIMENTO = 'recebimento';
    const TIPO_DEVOLUCAO = 'devolucao';
    const TIPO_ABATIMENTO = 'abatimento';
    const TIPO_RETENCAO_PARCIAL = 'retencao_parcial';
    const TIPO_RETENCAO_INTEGRAL = 'retencao_integral';
    const TIPO_AJUSTE = 'ajuste';

    protected $fillable = [
        'caucao_contrato_id',
        'tipo_movimentacao',
        'valor',
        'data_movimentacao',
        'forma_movimentacao',
        'descricao',
        'referencia_debito',
        'criado_por',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_movimentacao' => 'date:Y-m-d',
    ];

    public function caucao(): BelongsTo
    {
        return $this->belongsTo(ContratoCaucao::class, 'caucao_contrato_id');
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }
}

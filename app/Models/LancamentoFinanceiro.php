<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LancamentoFinanceiro extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'lancamentos_financeiros';

    const TIPO_ENTRADA = 'entrada';
    const TIPO_SAIDA = 'saida';

    const STATUS_PENDENTE = 'pendente';
    const STATUS_PAGO = 'pago';
    const STATUS_CANCELADO = 'cancelado';
    const STATUS_ESTORNADO = 'estornado';

    const ORIGEM_MANUAL = 'manual';
    const ORIGEM_PAGAMENTO_ALUGUEL = 'pagamento_aluguel';
    const ORIGEM_REPASSE_PROPRIETARIO = 'repasse_proprietario';
    const ORIGEM_CAUCAO = 'caucao';
    const ORIGEM_MOVIMENTACAO_CAUCAO = 'movimentacao_caucao';
    const ORIGEM_DESPESA = 'despesa';
    const ORIGEM_RECEITA_DIVERSA = 'receita_diversa';
    const ORIGEM_AJUSTE = 'ajuste';

    protected $fillable = [
        'codigo',
        'tipo',
        'categoria_financeira_id',
        'contrato_id',
        'parcela_aluguel_id',
        'repasse_proprietario_id',
        'caucao_contrato_id',
        'movimentacao_caucao_id',
        'imovel_id',
        'cliente_id',
        'descricao',
        'valor',
        'data_vencimento',
        'data_pagamento',
        'forma_pagamento',
        'status',
        'origem',
        'observacoes',
        'motivo_cancelamento',
        'motivo_estorno',
        'criado_por',
        'pago_por',
        'cancelado_por',
        'estornado_por',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_vencimento' => 'date:Y-m-d',
        'data_pagamento' => 'date:Y-m-d',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaFinanceira::class, 'categoria_financeira_id');
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(ContratoLocacao::class, 'contrato_id');
    }

    public function parcela(): BelongsTo
    {
        return $this->belongsTo(ParcelaAluguel::class, 'parcela_aluguel_id');
    }

    public function repasse(): BelongsTo
    {
        return $this->belongsTo(RepasseProprietario::class, 'repasse_proprietario_id');
    }

    public function caucao(): BelongsTo
    {
        return $this->belongsTo(ContratoCaucao::class, 'caucao_contrato_id');
    }

    public function movimentacaoCaucao(): BelongsTo
    {
        return $this->belongsTo(MovimentacaoCaucao::class, 'movimentacao_caucao_id');
    }

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function pagador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pago_por');
    }

    public function cancelador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelado_por');
    }

    public function estornador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'estornado_por');
    }

    public function historicos(): HasMany
    {
        return $this->hasMany(HistoricoFinanceiro::class, 'lancamento_financeiro_id')->orderBy('created_at', 'desc');
    }
}

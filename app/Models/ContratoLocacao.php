<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContratoLocacao extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'contratos_locacao';

    const STATUS_RASCUNHO              = 'rascunho';
    const STATUS_AGUARDANDO_ASSINATURA = 'aguardando_assinatura';
    const STATUS_ATIVO                 = 'ativo';
    const STATUS_ENCERRADO             = 'encerrado';
    const STATUS_RESCINDIDO            = 'rescindido';
    const STATUS_CANCELADO             = 'cancelado';

    const TIPO_RESIDENCIAL = 'residencial';
    const TIPO_COMERCIAL   = 'comercial';
    const TIPO_TEMPORADA   = 'temporada';

    const INDICE_IGPM  = 'igpm';
    const INDICE_IPCA  = 'ipca';
    const INDICE_INPC  = 'inpc';
    const INDICE_FIXO  = 'fixo';

    const REPASSE_PIX          = 'pix';
    const REPASSE_TRANSFERENCIA = 'transferencia';
    const REPASSE_DINHEIRO     = 'dinheiro';

    const TAXA_PERCENTUAL   = 'percentual';
    const TAXA_VALOR_FIXO   = 'valor_fixo';

    protected $fillable = [
        'numero',
        'status',
        'tipo_contrato',
        'objetivo_contrato',
        'imovel_id',
        'proprietario_id',
        'inquilino_id',
        'corretor_id',
        'criado_por',
        'data_inicio',
        'data_fim',
        'dia_vencimento',
        'duracao_meses',
        'valor_aluguel',
        'indice_reajuste',
        'periodicidade_reajuste',
        'data_primeiro_reajuste',
        'tipo_taxa_administracao',
        'valor_taxa_administracao',
        'dia_repasse',
        'forma_repasse',
        'banco',
        'agencia',
        'conta',
        'tipo_conta',
        'pix_key',
        'data_encerramento',
        'motivo_encerramento',
        'data_rescisao',
        'motivo_rescisao',
        'parte_requerente',
        'observacoes',
    ];

    protected $appends = ['permite_edicao_completa'];

    protected $casts = [
        'data_inicio'            => 'date:Y-m-d',
        'data_fim'               => 'date:Y-m-d',
        'data_primeiro_reajuste' => 'date:Y-m-d',
        'data_encerramento'      => 'date:Y-m-d',
        'data_rescisao'          => 'date:Y-m-d',
        'valor_aluguel'          => 'decimal:2',
        'valor_taxa_administracao' => 'decimal:2',
        'dia_vencimento'         => 'integer',
        'dia_repasse'            => 'integer',
        'duracao_meses'          => 'integer',
        'periodicidade_reajuste' => 'integer',
    ];

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }

    public function proprietario(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'proprietario_id');
    }

    public function inquilino(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'inquilino_id');
    }

    public function corretor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corretor_id');
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function encargos(): HasMany
    {
        return $this->hasMany(ContratoEncargo::class, 'contrato_id');
    }

    public function caucao(): HasOne
    {
        return $this->hasOne(ContratoCaucao::class, 'contrato_id');
    }

    public function multas(): HasOne
    {
        return $this->hasOne(ContratoMultas::class, 'contrato_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(ContratoDocumento::class, 'contrato_id')->orderBy('created_at', 'desc');
    }

    public function historicos(): HasMany
    {
        return $this->hasMany(ContratoHistorico::class, 'contrato_id')->orderBy('created_at', 'desc');
    }

    public function getPermiteEdicaoCompletaAttribute(): bool
    {
        return $this->status === self::STATUS_RASCUNHO;
    }
}

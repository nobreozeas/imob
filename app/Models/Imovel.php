<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Imovel extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'imoveis';

    const STATUS_DISPONIVEL   = 'disponivel';
    const STATUS_RESERVADO    = 'reservado';
    const STATUS_ALUGADO      = 'alugado';
    const STATUS_MANUTENCAO   = 'em_manutencao';
    const STATUS_INATIVO      = 'inativo';

    const TIPO_APARTAMENTO = 'apartamento';
    const TIPO_CASA        = 'casa';
    const TIPO_COMERCIAL   = 'comercial';
    const TIPO_SALA        = 'sala';
    const TIPO_GALPAO      = 'galpao';
    const TIPO_TERRENO     = 'terreno';
    const TIPO_RURAL       = 'rural';
    const TIPO_OUTRO       = 'outro';

    const FINALIDADE_ALUGUEL      = 'aluguel';
    const FINALIDADE_VENDA        = 'venda';
    const FINALIDADE_ALUGUEL_VENDA = 'aluguel_venda';

    protected $fillable = [
        'codigo',
        'titulo',
        'tipo',
        'finalidade',
        'status',
        'proprietario_id',
        'corretor_id',
        'descricao',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'ponto_referencia',
        'criado_por',
    ];

    public function caracteristicas(): HasOne
    {
        return $this->hasOne(ImovelCaracteristicas::class);
    }

    public function dadosComerciais(): HasOne
    {
        return $this->hasOne(ImovelDadosComerciais::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(ImovelFoto::class)->orderBy('ordem');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(ImovelDocumento::class);
    }

    public function proprietario(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'proprietario_id');
    }

    public function corretor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corretor_id');
    }

    public function scopeDisponivel($query)
    {
        return $query->where('status', self::STATUS_DISPONIVEL);
    }

    public function scopePorProprietario($query, string $proprietarioId)
    {
        return $query->where('proprietario_id', $proprietarioId);
    }

    public function getFotoPrincipalAttribute(): ?ImovelFoto
    {
        return $this->fotos->firstWhere('is_principal', true)
            ?? $this->fotos->first();
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(ContratoLocacao::class);
    }

    public function temContratoAtivo(): bool
    {
        return $this->contratos()->where('status', ContratoLocacao::STATUS_ATIVO)->exists();
    }
}

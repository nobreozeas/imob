<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    const STATUS_ATIVO = 'ativo';
    const STATUS_INATIVO = 'inativo';

    const TIPO_FISICA = 'fisica';
    const TIPO_JURIDICA = 'juridica';

    protected $fillable = [
        'tipo_pessoa',
        'nome',
        'razao_social',
        'nome_fantasia',
        'cpf',
        'cnpj',
        'rg',
        'data_nascimento',
        'telefone_principal',
        'whatsapp',
        'telefone_secundario',
        'email_principal',
        'email_alternativo',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'ponto_referencia',
        'observacoes',
        'status',
        'criado_por',
    ];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date:Y-m-d',
        ];
    }

    public function papeis(): HasMany
    {
        return $this->hasMany(ClientePapel::class);
    }

    public function dadosProprietario(): HasOne
    {
        return $this->hasOne(ClienteDadosProprietario::class);
    }

    public function dadosInquilino(): HasOne
    {
        return $this->hasOne(ClienteDadosInquilino::class);
    }

    public function scopeAtivo($query)
    {
        return $query->where('status', self::STATUS_ATIVO);
    }

    public function scopePorPapel($query, string $papel)
    {
        return $query->whereHas('papeis', fn ($q) => $q->where('papel', $papel));
    }

    public function temPapel(string $papel): bool
    {
        return $this->papeis->contains('papel', $papel);
    }

    public function estaAtivo(): bool
    {
        return $this->status === self::STATUS_ATIVO;
    }

    public function getNomeExibicaoAttribute(): string
    {
        return $this->tipo_pessoa === self::TIPO_JURIDICA
            ? ($this->razao_social ?? '')
            : ($this->nome ?? '');
    }

    public function getDocumentoAttribute(): ?string
    {
        return $this->tipo_pessoa === self::TIPO_JURIDICA ? $this->cnpj : $this->cpf;
    }
}

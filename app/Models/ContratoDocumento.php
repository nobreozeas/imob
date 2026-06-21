<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ContratoDocumento extends Model
{
    use HasUuids;

    protected $table = 'contrato_documentos';

    protected $appends = ['url'];

    protected $fillable = [
        'contrato_id',
        'caminho',
        'nome_original',
        'tipo',
        'criado_por',
    ];

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->caminho);
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(ContratoLocacao::class, 'contrato_id');
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }
}

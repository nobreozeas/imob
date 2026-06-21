<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ImovelFoto extends Model
{
    use HasUuids;

    protected $table = 'imovel_fotos';

    protected $fillable = [
        'imovel_id',
        'caminho',
        'nome_original',
        'is_principal',
        'ordem',
    ];

    protected $appends = ['url'];

    protected function casts(): array
    {
        return [
            'is_principal' => 'boolean',
        ];
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->caminho);
    }

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }
}

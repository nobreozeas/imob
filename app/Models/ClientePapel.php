<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientePapel extends Model
{
    use HasUuids;

    const PROPRIETARIO = 'proprietario';
    const INQUILINO = 'inquilino';

    protected $table = 'cliente_papeis';

    protected $fillable = [
        'cliente_id',
        'papel',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}

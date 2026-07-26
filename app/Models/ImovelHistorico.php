<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImovelHistorico extends Model
{
    use HasUuids;

    protected $table = 'imovel_historicos';

    public $timestamps = false;

    protected $fillable = [
        'imovel_id',
        'tipo_evento',
        'descricao',
        'dados_anteriores',
        'dados_novos',
        'usuario_id',
    ];

    protected $casts = [
        'dados_anteriores' => 'array',
        'dados_novos'      => 'array',
        'created_at'       => 'datetime',
    ];

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class, 'imovel_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}

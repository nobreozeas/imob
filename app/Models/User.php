<?php

namespace App\Models;

use App\Notifications\RecuperacaoSenhaNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    const STATUS_ATIVO = 'ativo';
    const STATUS_INATIVO = 'inativo';
    const STATUS_BLOQUEADO = 'bloqueado';
    const STATUS_EXCLUIDO = 'excluido';

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'deve_alterar_senha',
        'ultimo_acesso_em',
        'primeiro_acesso_em',
        'convite_enviado_em',
        'criado_por',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deve_alterar_senha' => 'boolean',
            'ultimo_acesso_em' => 'datetime',
            'primeiro_acesso_em' => 'datetime',
            'convite_enviado_em' => 'datetime',
        ];
    }

    public function scopeAtivo($query)
    {
        return $query->where('status', self::STATUS_ATIVO);
    }

    public function estaAtivo(): bool
    {
        return $this->status === self::STATUS_ATIVO;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new RecuperacaoSenhaNotification($token));
    }
}

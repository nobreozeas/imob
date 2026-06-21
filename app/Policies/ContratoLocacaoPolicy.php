<?php

namespace App\Policies;

use App\Models\ContratoLocacao;
use App\Models\User;

class ContratoLocacaoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('contratos.viewAny');
    }

    public function view(User $user, ContratoLocacao $contrato): bool
    {
        return $user->can('contratos.view');
    }

    public function create(User $user): bool
    {
        return $user->can('contratos.create');
    }

    public function update(User $user, ContratoLocacao $contrato): bool
    {
        return $user->can('contratos.update');
    }

    public function ativar(User $user, ContratoLocacao $contrato): bool
    {
        return $user->can('contratos.ativar');
    }

    public function cancelar(User $user, ContratoLocacao $contrato): bool
    {
        return $user->can('contratos.cancelar');
    }

    public function encerrar(User $user, ContratoLocacao $contrato): bool
    {
        return $user->can('contratos.encerrar');
    }

    public function rescindir(User $user, ContratoLocacao $contrato): bool
    {
        return $user->can('contratos.rescindir');
    }

    public function documentos(User $user, ContratoLocacao $contrato): bool
    {
        return $user->can('contratos.documentos');
    }
}

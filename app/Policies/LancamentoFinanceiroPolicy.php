<?php

namespace App\Policies;

use App\Models\LancamentoFinanceiro;
use App\Models\User;

class LancamentoFinanceiroPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('financeiro.visualizar');
    }

    public function view(User $user, LancamentoFinanceiro $lancamento): bool
    {
        return $user->can('financeiro.visualizar');
    }

    public function create(User $user): bool
    {
        return $user->can('financeiro.criar');
    }

    public function update(User $user, LancamentoFinanceiro $lancamento): bool
    {
        return $user->can('financeiro.editar') && $lancamento->origem === LancamentoFinanceiro::ORIGEM_MANUAL;
    }

    public function delete(User $user, LancamentoFinanceiro $lancamento): bool
    {
        return $user->can('financeiro.excluir')
            && $lancamento->origem === LancamentoFinanceiro::ORIGEM_MANUAL
            && $lancamento->status === LancamentoFinanceiro::STATUS_PENDENTE;
    }

    public function marcarComoPago(User $user, LancamentoFinanceiro $lancamento): bool
    {
        return $user->can('financeiro.marcar_como_pago');
    }

    public function cancelar(User $user, LancamentoFinanceiro $lancamento): bool
    {
        return $user->can('financeiro.cancelar');
    }

    public function estornar(User $user, LancamentoFinanceiro $lancamento): bool
    {
        return $user->can('financeiro.estornar');
    }
}

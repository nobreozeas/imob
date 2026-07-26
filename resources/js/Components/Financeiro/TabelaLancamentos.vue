<script setup lang="ts">
import { useForm, usePage, Link } from '@inertiajs/vue3';
import Swal, { swalClass } from '@/lib/swal';
import StatusLancamentoBadge from '@/Components/Financeiro/StatusLancamentoBadge.vue';
import type { LancamentoFinanceiro } from '@/types/lancamentoFinanceiro';

const props = defineProps<{ lancamentos: LancamentoFinanceiro[] }>();

const page = usePage();
const auth = (page.props as any).auth;

const formAcao = useForm({});

function tem(permissao: string): boolean {
    return auth?.permissions?.includes(permissao);
}

function moeda(val: string | null): string {
    if (!val) return '—';
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(val));
}

function nomeCliente(c: { nome: string | null; razao_social: string | null } | undefined): string {
    if (!c) return '—';
    return c.nome ?? c.razao_social ?? '—';
}

function marcarComoPago(lancamento: LancamentoFinanceiro) {
    Swal.fire({
        title: 'Marcar lançamento como pago?',
        html: '<input id="swal-data" type="date" class="input input-bordered w-full mb-2" value="' + new Date().toISOString().substring(0, 10) + '">'
            + '<select id="swal-forma" class="select select-bordered w-full">'
            + '<option value="pix">PIX</option><option value="dinheiro">Dinheiro</option>'
            + '<option value="transferencia">Transferência</option><option value="boleto">Boleto</option>'
            + '<option value="cartao_credito">Cartão de Crédito</option><option value="cartao_debito">Cartão de Débito</option>'
            + '<option value="cheque">Cheque</option><option value="outro">Outro</option></select>',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => ({
            data_pagamento: (document.getElementById('swal-data') as HTMLInputElement).value,
            forma_pagamento: (document.getElementById('swal-forma') as HTMLSelectElement).value,
        }),
    }).then(result => {
        if (result.isConfirmed) {
            formAcao.transform(() => result.value).post(route('financeiro.lancamentos.marcar-como-pago', lancamento.id));
        }
    });
}

function cancelar(lancamento: LancamentoFinanceiro) {
    Swal.fire({
        title: 'Cancelar lançamento?',
        input: 'text',
        inputLabel: 'Motivo do cancelamento',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Voltar',
        customClass: swalClass('error'),
        inputValidator: (value) => (!value ? 'Informe o motivo' : undefined),
    }).then(result => {
        if (result.isConfirmed) {
            formAcao.transform(() => ({ motivo: result.value })).post(route('financeiro.lancamentos.cancelar', lancamento.id));
        }
    });
}

function estornar(lancamento: LancamentoFinanceiro) {
    Swal.fire({
        title: 'Estornar lançamento?',
        input: 'text',
        inputLabel: 'Motivo do estorno',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Voltar',
        customClass: swalClass('error'),
        inputValidator: (value) => (!value ? 'Informe o motivo' : undefined),
    }).then(result => {
        if (result.isConfirmed) {
            formAcao.transform(() => ({ motivo: result.value })).post(route('financeiro.lancamentos.estornar', lancamento.id));
        }
    });
}

function excluir(lancamento: LancamentoFinanceiro) {
    Swal.fire({
        title: 'Excluir lançamento?',
        text: 'Esta ação não pode ser desfeita.',
        showCancelButton: true,
        confirmButtonText: 'Excluir',
        cancelButtonText: 'Voltar',
        customClass: swalClass('error'),
    }).then(result => {
        if (result.isConfirmed) {
            formAcao.delete(route('financeiro.lancamentos.destroy', lancamento.id));
        }
    });
}
</script>

<template>
    <div class="overflow-x-auto">
        <table class="table table-sm">
            <thead>
                <tr class="bg-base-200/50">
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Categoria</th>
                    <th>Descrição</th>
                    <th>Cliente</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Pagamento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="lancamento in props.lancamentos" :key="lancamento.id" class="hover">
                    <td class="font-mono text-xs">{{ lancamento.codigo }}</td>
                    <td>
                        <span class="badge badge-sm" :class="lancamento.tipo === 'entrada' ? 'badge-success' : 'badge-error'">
                            {{ lancamento.tipo === 'entrada' ? 'Entrada' : 'Saída' }}
                        </span>
                    </td>
                    <td class="text-sm">{{ lancamento.categoria?.nome ?? '—' }}</td>
                    <td class="text-sm">{{ lancamento.descricao ?? '—' }}</td>
                    <td class="text-sm">{{ nomeCliente(lancamento.cliente) }}</td>
                    <td class="text-sm font-medium">{{ moeda(lancamento.valor) }}</td>
                    <td class="text-sm">{{ lancamento.data_vencimento ?? '—' }}</td>
                    <td class="text-sm">{{ lancamento.data_pagamento ?? '—' }}</td>
                    <td><StatusLancamentoBadge :status="lancamento.status" /></td>
                    <td>
                        <div class="flex flex-wrap gap-1">
                            <Link :href="route('financeiro.lancamentos.show', lancamento.id)" class="btn btn-ghost btn-xs">Ver</Link>
                            <button
                                v-if="lancamento.status === 'pendente' && tem('financeiro.marcar_como_pago')"
                                class="btn btn-success btn-xs"
                                @click="marcarComoPago(lancamento)"
                            >Marcar Pago</button>
                            <button
                                v-if="lancamento.status === 'pendente' && tem('financeiro.cancelar')"
                                class="btn btn-error btn-outline btn-xs"
                                @click="cancelar(lancamento)"
                            >Cancelar</button>
                            <button
                                v-if="lancamento.status === 'pago' && tem('financeiro.estornar')"
                                class="btn btn-error btn-outline btn-xs"
                                @click="estornar(lancamento)"
                            >Estornar</button>
                            <button
                                v-if="lancamento.status === 'pendente' && lancamento.origem === 'manual' && tem('financeiro.excluir')"
                                class="btn btn-ghost btn-xs text-error"
                                @click="excluir(lancamento)"
                            >Excluir</button>
                        </div>
                    </td>
                </tr>
                <tr v-if="props.lancamentos.length === 0">
                    <td colspan="10" class="text-center py-8 text-base-content/40">Nenhum lançamento encontrado.</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

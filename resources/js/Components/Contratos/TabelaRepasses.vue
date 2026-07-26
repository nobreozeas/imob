<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import Swal, { swalClass } from '@/lib/swal';
import type { RepasseProprietario, StatusRepasseProprietario } from '@/types/repasse';

const props = defineProps<{ repasses: RepasseProprietario[] }>();

const page = usePage();
const auth = (page.props as any).auth;

const formAcao = useForm({});

function podeMarcarComoPago(): boolean {
    return auth?.permissions?.includes('repasses.marcar-como-pago');
}

function moeda(val: string | null): string {
    if (!val) return '—';
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(val));
}

function corStatus(status: StatusRepasseProprietario): string {
    const cores: Record<StatusRepasseProprietario, string> = {
        pendente: 'badge-warning',
        pago: 'badge-success',
        cancelado: 'badge-error',
    };
    return cores[status] ?? 'badge-ghost';
}

function labelStatus(status: StatusRepasseProprietario): string {
    const labels: Record<StatusRepasseProprietario, string> = {
        pendente: 'Pendente',
        pago: 'Pago',
        cancelado: 'Cancelado',
    };
    return labels[status] ?? status;
}

function marcarComoPago(repasse: RepasseProprietario) {
    Swal.fire({
        title: 'Marcar repasse como pago?',
        input: 'date',
        inputLabel: 'Data do pagamento',
        inputValue: new Date().toISOString().substring(0, 10),
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
    }).then(result => {
        if (result.isConfirmed) {
            formAcao.transform(() => ({ data_pagamento: result.value })).post(
                route('repasses-proprietarios.marcar-como-pago', repasse.id),
            );
        }
    });
}

function cancelar(repasse: RepasseProprietario) {
    Swal.fire({
        title: 'Cancelar repasse?',
        input: 'text',
        inputLabel: 'Motivo do cancelamento',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Voltar',
        customClass: swalClass('error'),
        inputValidator: (value) => (!value ? 'Informe o motivo' : undefined),
    }).then(result => {
        if (result.isConfirmed) {
            formAcao.transform(() => ({ motivo: result.value })).post(
                route('repasses-proprietarios.cancelar', repasse.id),
            );
        }
    });
}
</script>

<template>
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Repasses ao Proprietário</h3>
            <p v-if="!repasses.length" class="text-sm text-base-content/40">Nenhum repasse gerado ainda.</p>
            <div v-else class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Referência</th>
                            <th>Bruto</th>
                            <th>Taxa</th>
                            <th>Líquido</th>
                            <th>Status</th>
                            <th v-if="podeMarcarComoPago()">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="repasse in repasses" :key="repasse.id">
                            <td>{{ repasse.parcela ? `${String(repasse.parcela.mes_referencia).padStart(2, '0')}/${repasse.parcela.ano_referencia}` : '—' }}</td>
                            <td>{{ moeda(repasse.valor_bruto) }}</td>
                            <td>{{ moeda(repasse.valor_taxa_administracao) }}</td>
                            <td>{{ moeda(repasse.valor_liquido) }}</td>
                            <td><span class="badge badge-sm" :class="corStatus(repasse.status)">{{ labelStatus(repasse.status) }}</span></td>
                            <td v-if="podeMarcarComoPago() && repasse.status === 'pendente'">
                                <div class="flex gap-1">
                                    <button class="btn btn-success btn-xs" @click="marcarComoPago(repasse)">Marcar Pago</button>
                                    <button class="btn btn-error btn-outline btn-xs" @click="cancelar(repasse)">Cancelar</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import Swal, { swalClass } from '@/lib/swal';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import type { RepasseProprietario, StatusRepasseProprietario } from '@/types/repasse';

defineOptions({ layout: AdminLayout });

interface RepassePaginado {
    data: (RepasseProprietario & { contrato?: { numero: string }; imovel?: { titulo: string }; proprietario?: { nome: string | null; razao_social: string | null } })[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{
    repasses: RepassePaginado;
    filtros: { status?: string };
}>();

const filtros = ref({ status: props.filtros.status ?? '' });
const formAcao = useForm({});

let debounceTimer: ReturnType<typeof setTimeout>;
watch(filtros, (val) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('financeiro.repasses.index'), val, { preserveState: true, replace: true });
    }, 300);
}, { deep: true });

function moeda(val: string | null): string {
    if (!val) return '—';
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(val));
}

function nome(c: { nome: string | null; razao_social: string | null } | undefined): string {
    if (!c) return '—';
    return c.nome ?? c.razao_social ?? '—';
}

function corStatus(status: StatusRepasseProprietario): string {
    const cores: Record<StatusRepasseProprietario, string> = { pendente: 'badge-warning', pago: 'badge-success', cancelado: 'badge-error' };
    return cores[status] ?? 'badge-ghost';
}

function marcarComoPago(id: string) {
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
            formAcao.transform(() => ({ data_pagamento: result.value })).post(route('repasses-proprietarios.marcar-como-pago', id));
        }
    });
}

function cancelar(id: string) {
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
            formAcao.transform(() => ({ motivo: result.value })).post(route('repasses-proprietarios.cancelar', id));
        }
    });
}
</script>

<template>
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-bold text-base-content">Repasses aos Proprietários</h1>
            <AppBreadcrumb />
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-4">
                <select v-model="filtros.status" class="select select-bordered select-sm w-full md:w-64">
                    <option value="">Todos os status</option>
                    <option value="pendente">Pendente</option>
                    <option value="pago">Pago</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200 overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr class="bg-base-200/50">
                        <th>Proprietário</th>
                        <th>Imóvel</th>
                        <th>Contrato</th>
                        <th>Bruto</th>
                        <th>Taxa</th>
                        <th>Líquido</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="repasse in repasses.data" :key="repasse.id" class="hover">
                        <td class="text-sm">{{ nome(repasse.proprietario) }}</td>
                        <td class="text-sm">{{ repasse.imovel?.titulo ?? '—' }}</td>
                        <td class="text-sm">
                            <Link :href="route('contratos.show', { contrato: repasse.contrato_id, tab: 'repasses' })" class="link">{{ repasse.contrato?.numero ?? '—' }}</Link>
                        </td>
                        <td class="text-sm">{{ moeda(repasse.valor_bruto) }}</td>
                        <td class="text-sm">{{ moeda(repasse.valor_taxa_administracao) }}</td>
                        <td class="text-sm font-medium">{{ moeda(repasse.valor_liquido) }}</td>
                        <td><span class="badge badge-sm" :class="corStatus(repasse.status)">{{ repasse.status }}</span></td>
                        <td v-if="repasse.status === 'pendente'">
                            <div class="flex gap-1">
                                <button class="btn btn-success btn-xs" @click="marcarComoPago(repasse.id)">Marcar Pago</button>
                                <button class="btn btn-error btn-outline btn-xs" @click="cancelar(repasse.id)">Cancelar</button>
                            </div>
                        </td>
                        <td v-else />
                    </tr>
                    <tr v-if="repasses.data.length === 0">
                        <td colspan="8" class="text-center py-8 text-base-content/40">Nenhum repasse encontrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="repasses.last_page > 1" class="flex justify-center gap-1">
            <template v-for="link in repasses.links" :key="link.label">
                <button
                    v-if="link.url"
                    class="btn btn-sm"
                    :class="link.active ? 'btn-primary' : 'btn-ghost'"
                    v-html="link.label"
                    @click="router.get(link.url!, {}, { preserveState: true })"
                />
                <span v-else class="btn btn-sm btn-disabled" v-html="link.label" />
            </template>
        </div>
    </div>
</template>

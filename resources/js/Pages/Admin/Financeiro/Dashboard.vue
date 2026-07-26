<script setup lang="ts">
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import FinanceiroResumoCards, { type ResumoFinanceiro } from '@/Components/Financeiro/FinanceiroResumoCards.vue';

defineOptions({ layout: AdminLayout });

interface Proprietario {
    id: string;
    nome: string | null;
    razao_social: string | null;
    tipo_pessoa: string;
}

interface Filtros {
    data_inicio?: string;
    data_fim?: string;
    proprietario_id?: string;
    status_contrato?: string;
    status_imovel?: string;
}

const props = defineProps<{
    resumo: ResumoFinanceiro;
    filtros: Filtros;
    proprietarios: Proprietario[];
}>();

const filtros = ref<Filtros>({ ...props.filtros });

let debounceTimer: ReturnType<typeof setTimeout>;
watch(filtros, (val) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('financeiro.dashboard'), val as Record<string, string>, { preserveState: true, replace: true });
    }, 400);
}, { deep: true });

function nomeProprietario(p: Proprietario): string {
    return p.tipo_pessoa === 'juridica' ? (p.razao_social ?? '—') : (p.nome ?? '—');
}
</script>

<template>
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-bold text-base-content">Financeiro</h1>
            <AppBreadcrumb />
        </div>

        <!-- Filtros -->
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input v-model="filtros.data_inicio" type="date" class="input input-bordered input-sm w-full" />
                    <input v-model="filtros.data_fim" type="date" class="input input-bordered input-sm w-full" />
                    <select v-model="filtros.proprietario_id" class="select select-bordered select-sm w-full">
                        <option value="">Todos os proprietários</option>
                        <option v-for="p in proprietarios" :key="p.id" :value="p.id">{{ nomeProprietario(p) }}</option>
                    </select>
                    <select v-model="filtros.status_imovel" class="select select-bordered select-sm w-full">
                        <option value="">Todos os imóveis</option>
                        <option value="disponivel">Disponível</option>
                        <option value="alugado">Alugado</option>
                        <option value="reservado">Reservado</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
            </div>
        </div>

        <FinanceiroResumoCards :resumo="resumo" />

        <!-- Atalhos rápidos -->
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body">
                <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Atalhos rápidos</h3>
                <div class="flex flex-wrap gap-2">
                    <Link :href="route('financeiro.lancamentos.index')" class="btn btn-sm btn-outline">Ver lançamentos</Link>
                    <Link :href="route('financeiro.repasses.index')" class="btn btn-sm btn-outline">Repasses pendentes</Link>
                    <Link :href="route('financeiro.inadimplencia')" class="btn btn-sm btn-outline">Ver inadimplência</Link>
                    <Link :href="route('financeiro.fluxo-caixa')" class="btn btn-sm btn-outline">Ver fluxo de caixa</Link>
                </div>
            </div>
        </div>
    </div>
</template>

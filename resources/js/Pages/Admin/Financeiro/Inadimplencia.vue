<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import TabelaInadimplencia from '@/Components/Financeiro/TabelaInadimplencia.vue';

defineOptions({ layout: AdminLayout });

interface Indicadores {
    quantidade_parcelas: number;
    valor_total: number;
    quantidade_contratos: number;
    quantidade_clientes: number;
    maior_atraso_dias: number;
}

const props = defineProps<{
    parcelas: { data: any[]; last_page: number; links: { url: string | null; label: string; active: boolean }[]; total: number; from: number | null; to: number | null };
    indicadores: Indicadores;
    filtros: Record<string, string>;
}>();

function moeda(val: number): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val);
}
</script>

<template>
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-bold text-base-content">Inadimplência</h1>
            <AppBreadcrumb />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body py-4">
                    <span class="text-xs uppercase text-base-content/50">Parcelas vencidas</span>
                    <span class="text-xl font-bold">{{ indicadores.quantidade_parcelas }}</span>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body py-4">
                    <span class="text-xs uppercase text-base-content/50">Valor total vencido</span>
                    <span class="text-xl font-bold text-error">{{ moeda(indicadores.valor_total) }}</span>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body py-4">
                    <span class="text-xs uppercase text-base-content/50">Contratos inadimplentes</span>
                    <span class="text-xl font-bold">{{ indicadores.quantidade_contratos }}</span>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body py-4">
                    <span class="text-xs uppercase text-base-content/50">Inquilinos inadimplentes</span>
                    <span class="text-xl font-bold">{{ indicadores.quantidade_clientes }}</span>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body py-4">
                    <span class="text-xs uppercase text-base-content/50">Maior atraso</span>
                    <span class="text-xl font-bold">{{ indicadores.maior_atraso_dias }} dias</span>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <TabelaInadimplencia :parcelas="parcelas.data" />
        </div>

        <div v-if="parcelas.last_page > 1" class="flex justify-center gap-1">
            <template v-for="link in parcelas.links" :key="link.label">
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

        <p v-if="parcelas.total > 0" class="text-xs text-base-content/50 text-center">
            Exibindo {{ parcelas.from }}–{{ parcelas.to }} de {{ parcelas.total }} parcelas
        </p>
    </div>
</template>

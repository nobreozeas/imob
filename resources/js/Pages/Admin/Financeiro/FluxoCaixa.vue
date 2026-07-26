<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';

defineOptions({ layout: AdminLayout });

interface Bucket {
    data: string;
    entradas_previstas: number;
    entradas_realizadas: number;
    saidas_previstas: number;
    saidas_realizadas: number;
    saldo_previsto: number;
    saldo_realizado: number;
}

interface Filtros {
    agrupamento: string;
    data_inicio: string;
    data_fim: string;
}

const props = defineProps<{
    fluxo: Bucket[];
    filtros: Filtros;
}>();

const filtros = ref<Filtros>({ ...props.filtros });

let debounceTimer: ReturnType<typeof setTimeout>;
watch(filtros, (val) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('financeiro.fluxo-caixa'), val as Record<string, string>, { preserveState: true, replace: true });
    }, 400);
}, { deep: true });

function moeda(val: number): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val);
}

const totalRealizado = () => props.fluxo.reduce((acc, b) => acc + b.saldo_realizado, 0);
const totalPrevisto = () => props.fluxo.reduce((acc, b) => acc + b.saldo_previsto, 0);
</script>

<template>
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-bold text-base-content">Fluxo de Caixa</h1>
            <AppBreadcrumb />
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <select v-model="filtros.agrupamento" class="select select-bordered select-sm w-full">
                        <option value="dia">Diário</option>
                        <option value="mes">Mensal</option>
                    </select>
                    <input v-model="filtros.data_inicio" type="date" class="input input-bordered input-sm w-full" />
                    <input v-model="filtros.data_fim" type="date" class="input input-bordered input-sm w-full" />
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body py-4">
                    <span class="text-xs uppercase text-base-content/50">Saldo previsto no período</span>
                    <span class="text-xl font-bold">{{ moeda(totalPrevisto()) }}</span>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body py-4">
                    <span class="text-xs uppercase text-base-content/50">Saldo realizado no período</span>
                    <span class="text-xl font-bold" :class="totalRealizado() >= 0 ? 'text-success' : 'text-error'">{{ moeda(totalRealizado()) }}</span>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200 overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr class="bg-base-200/50">
                        <th>Data</th>
                        <th>Entradas Previstas</th>
                        <th>Entradas Realizadas</th>
                        <th>Saídas Previstas</th>
                        <th>Saídas Realizadas</th>
                        <th>Saldo Previsto</th>
                        <th>Saldo Realizado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="bucket in fluxo" :key="bucket.data" class="hover">
                        <td class="text-sm">{{ bucket.data }}</td>
                        <td class="text-sm">{{ moeda(bucket.entradas_previstas) }}</td>
                        <td class="text-sm">{{ moeda(bucket.entradas_realizadas) }}</td>
                        <td class="text-sm">{{ moeda(bucket.saidas_previstas) }}</td>
                        <td class="text-sm">{{ moeda(bucket.saidas_realizadas) }}</td>
                        <td class="text-sm font-medium">{{ moeda(bucket.saldo_previsto) }}</td>
                        <td class="text-sm font-medium" :class="bucket.saldo_realizado >= 0 ? 'text-success' : 'text-error'">{{ moeda(bucket.saldo_realizado) }}</td>
                    </tr>
                    <tr v-if="fluxo.length === 0">
                        <td colspan="7" class="text-center py-8 text-base-content/40">Nenhuma movimentação no período.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

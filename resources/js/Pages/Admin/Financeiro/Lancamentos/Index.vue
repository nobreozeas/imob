<script setup lang="ts">
import { ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import TabelaLancamentos from '@/Components/Financeiro/TabelaLancamentos.vue';
import ModalReceita from '@/Components/Financeiro/ModalReceita.vue';
import ModalDespesa from '@/Components/Financeiro/ModalDespesa.vue';
import type { CategoriaFinanceira } from '@/types/categoriaFinanceira';
import type { LancamentoFinanceiroFiltros, LancamentoFinanceiroPaginado } from '@/types/lancamentoFinanceiro';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    lancamentos: LancamentoFinanceiroPaginado;
    categorias: CategoriaFinanceira[];
    filtros: LancamentoFinanceiroFiltros;
}>();

const page = usePage();
const auth = (page.props as any).auth;

function podeCriar(): boolean {
    return auth?.permissions?.includes('financeiro.criar');
}

const filtros = ref<LancamentoFinanceiroFiltros>({ ...props.filtros });
const modalReceitaAberto = ref(false);
const modalDespesaAberto = ref(false);

let debounceTimer: ReturnType<typeof setTimeout>;
watch(filtros, (val) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('financeiro.lancamentos.index'), val as Record<string, string>, { preserveState: true, replace: true });
    }, 400);
}, { deep: true });

const categoriasEntrada = props.categorias.filter(c => c.tipo === 'entrada');
const categoriasSaida = props.categorias.filter(c => c.tipo === 'saida');
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Lançamentos Financeiros</h1>
                <AppBreadcrumb />
            </div>
            <div v-if="podeCriar()" class="flex gap-2">
                <button class="btn btn-success btn-sm" @click="modalReceitaAberto = true">+ Receita</button>
                <button class="btn btn-error btn-sm" @click="modalDespesaAberto = true">+ Despesa</button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input v-model="filtros.busca" type="text" class="input input-bordered input-sm w-full" placeholder="Buscar por código ou descrição..." />
                    <select v-model="filtros.tipo" class="select select-bordered select-sm w-full">
                        <option value="">Todos os tipos</option>
                        <option value="entrada">Entrada</option>
                        <option value="saida">Saída</option>
                    </select>
                    <select v-model="filtros.status" class="select select-bordered select-sm w-full">
                        <option value="">Todos os status</option>
                        <option value="pendente">Pendente</option>
                        <option value="pago">Pago</option>
                        <option value="cancelado">Cancelado</option>
                        <option value="estornado">Estornado</option>
                    </select>
                    <select v-model="filtros.categoria_financeira_id" class="select select-bordered select-sm w-full">
                        <option value="">Todas as categorias</option>
                        <optgroup label="Entradas">
                            <option v-for="c in categoriasEntrada" :key="c.id" :value="c.id">{{ c.nome }}</option>
                        </optgroup>
                        <optgroup label="Saídas">
                            <option v-for="c in categoriasSaida" :key="c.id" :value="c.id">{{ c.nome }}</option>
                        </optgroup>
                    </select>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <TabelaLancamentos :lancamentos="lancamentos.data" />
        </div>

        <!-- Paginação -->
        <div v-if="lancamentos.last_page > 1" class="flex justify-center gap-1">
            <template v-for="link in lancamentos.links" :key="link.label">
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

        <p v-if="lancamentos.total > 0" class="text-xs text-base-content/50 text-center">
            Exibindo {{ lancamentos.from }}–{{ lancamentos.to }} de {{ lancamentos.total }} lançamentos
        </p>

        <ModalReceita v-if="modalReceitaAberto" :categorias="categoriasEntrada" @fechado="modalReceitaAberto = false" />
        <ModalDespesa v-if="modalDespesaAberto" :categorias="categoriasSaida" @fechado="modalDespesaAberto = false" />
    </div>
</template>

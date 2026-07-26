<script setup lang="ts">
import { ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BadgeStatus from '@/Components/Contratos/BadgeStatus.vue';
import type { ContratoPaginado, ContratoFiltros } from '@/types/contrato';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    contratos: ContratoPaginado;
    filtros: ContratoFiltros;
}>();

const page = usePage();
const auth = (page.props as any).auth;

function podeRenovar(): boolean {
    return auth?.permissions?.includes('contratos.renovar');
}

const filtros = ref<ContratoFiltros>({ ...props.filtros });

let debounceTimer: ReturnType<typeof setTimeout>;
watch(filtros, (val) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('contratos.index'), val as Record<string, string>, { preserveState: true, replace: true });
    }, 400);
}, { deep: true });

function moeda(val: string): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(val));
}

function nomeCliente(c: { nome: string | null; razao_social: string | null; tipo_pessoa: string } | undefined): string {
    if (!c) return '—';
    return c.tipo_pessoa === 'juridica' ? (c.razao_social ?? '—') : (c.nome ?? '—');
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Contratos de Locação</h1>
            </div>
            <Link :href="route('contratos.create')" class="btn btn-primary btn-sm">+ Novo Contrato</Link>
        </div>

        <!-- Filtros -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body py-4">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    <input
                        v-model="filtros.busca"
                        type="text"
                        class="input input-bordered input-sm w-full"
                        placeholder="Buscar por número..."
                    />
                    <select v-model="filtros.status" class="select select-bordered select-sm w-full">
                        <option value="">Todos os status</option>
                        <option value="rascunho">Rascunho</option>
                        <option value="aguardando_assinatura">Ag. Assinatura</option>
                        <option value="ativo">Ativo</option>
                        <option value="vencido">Vencido</option>
                        <option value="encerrado">Encerrado</option>
                        <option value="rescindido">Rescindido</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                    <input
                        v-model="filtros.data_inicio_de"
                        type="date"
                        class="input input-bordered input-sm w-full"
                        placeholder="Início a partir de"
                    />
                    <input
                        v-model="filtros.data_inicio_ate"
                        type="date"
                        class="input input-bordered input-sm w-full"
                        placeholder="Início até"
                    />
                </div>
            </div>
        </div>

        <!-- Tabela -->
        <div class="card bg-base-100 shadow-sm border border-base-300 overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr class="bg-base-200/50">
                        <th>Número</th>
                        <th>Imóvel</th>
                        <th>Proprietário</th>
                        <th>Inquilino</th>
                        <th>Vigência</th>
                        <th>Aluguel</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="contrato in contratos.data" :key="contrato.id" class="hover">
                        <td class="font-mono text-xs">{{ contrato.numero }}</td>
                        <td>
                            <div v-if="contrato.imovel" class="text-sm">
                                <div class="font-medium">{{ contrato.imovel.titulo }}</div>
                                <div class="text-base-content/50 text-xs">{{ contrato.imovel.codigo }}</div>
                            </div>
                            <span v-else>—</span>
                        </td>
                        <td class="text-sm">{{ nomeCliente(contrato.proprietario) }}</td>
                        <td class="text-sm">{{ nomeCliente(contrato.inquilino) }}</td>
                        <td class="text-sm whitespace-nowrap">
                            {{ contrato.data_inicio }}
                            <span v-if="contrato.data_fim" class="text-base-content/50"> → {{ contrato.data_fim }}</span>
                        </td>
                        <td class="text-sm font-medium">{{ moeda(contrato.valor_aluguel) }}</td>
                        <td><BadgeStatus :status="contrato.status" /></td>
                        <td>
                            <div class="flex gap-1">
                                <Link :href="route('contratos.show', contrato.id)" class="btn btn-ghost btn-xs">Ver</Link>
                                <Link
                                    v-if="contrato.status === 'rascunho'"
                                    :href="route('contratos.edit', contrato.id)"
                                    class="btn btn-ghost btn-xs"
                                >Editar</Link>
                                <Link
                                    v-if="['ativo', 'vencido'].includes(contrato.status) && podeRenovar()"
                                    :href="route('contratos.show', contrato.id)"
                                    class="btn btn-ghost btn-xs"
                                >Renovar</Link>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="contratos.data.length === 0">
                        <td colspan="8" class="text-center py-8 text-base-content/40">Nenhum contrato encontrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div v-if="contratos.last_page > 1" class="flex justify-center gap-1">
            <template v-for="link in contratos.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="btn btn-sm"
                    :class="link.active ? 'btn-primary' : 'btn-ghost'"
                    v-html="link.label"
                />
                <span v-else class="btn btn-sm btn-disabled" v-html="link.label" />
            </template>
        </div>

        <p v-if="contratos.total > 0" class="text-xs text-base-content/50 text-center">
            Exibindo {{ contratos.from }}–{{ contratos.to }} de {{ contratos.total }} contratos
        </p>
    </div>
</template>

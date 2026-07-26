<script setup lang="ts">
import { ref, watch } from 'vue';
import { ImageOff } from 'lucide-vue-next';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import BadgeStatus from '@/Components/Imoveis/BadgeStatus.vue';
import BadgeTipo from '@/Components/Imoveis/BadgeTipo.vue';
import BadgeFinalidade from '@/Components/Imoveis/BadgeFinalidade.vue';
import Swal, { swalClass } from '@/lib/swal';
import type { ImovelFiltros, ImovelIndicadores, ImovelPaginado, ProprietarioOpcao, StatusImovel } from '@/types/imovel';
import { useImovelStatus } from '@/composables/useImovelStatus';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    imoveis: ImovelPaginado;
    filtros: ImovelFiltros;
    indicadores: ImovelIndicadores;
    proprietarios: ProprietarioOpcao[];
}>();

const page = usePage();
const auth = (page.props as any).auth;
const { labelStatus } = useImovelStatus();
const filtros = ref<ImovelFiltros>({ ...props.filtros });

function podeRestaurar(): boolean {
    return auth?.permissions?.includes('imoveis.restore');
}

function podeExcluir(): boolean {
    return auth?.permissions?.includes('imoveis.destroy');
}

let debounceTimer: ReturnType<typeof setTimeout>;
watch(filtros, (val) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('imoveis.index'), val as Record<string, string>, { preserveState: true, replace: true });
    }, 400);
}, { deep: true });

function nomeProprietario(p: ProprietarioOpcao): string {
    return p.tipo_pessoa === 'juridica' ? (p.razao_social ?? '') : (p.nome ?? '');
}

async function alterarStatus(imovelId: string, statusAtual: StatusImovel) {
    const opcoesStatus = [
        { value: 'disponivel', label: 'Disponível' },
        { value: 'reservado', label: 'Reservado' },
        { value: 'alugado', label: 'Alugado' },
        { value: 'em_manutencao', label: 'Em Manutenção' },
        { value: 'inativo', label: 'Inativo' },
    ].filter(o => o.value !== statusAtual);

    const inputOptions: Record<string, string> = {};
    opcoesStatus.forEach(o => { inputOptions[o.value] = o.label; });

    const result = await Swal.fire({
        title: 'Alterar Status',
        input: 'select',
        inputOptions,
        inputPlaceholder: 'Selecione o novo status',
        showCancelButton: true,
        confirmButtonText: 'Alterar',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed && result.value) {
        router.patch(route('imoveis.alterar-status', imovelId), { status: result.value });
    }
}

async function excluir(imovelId: string) {
    const result = await Swal.fire({
        title: 'Excluir imóvel?',
        text: 'O imóvel será excluído e poderá ser restaurado depois.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Excluir',
        cancelButtonText: 'Cancelar',
        customClass: swalClass('error'),
    });

    if (result.isConfirmed) {
        router.delete(route('imoveis.destroy', imovelId));
    }
}

async function restaurar(imovelId: string) {
    const result = await Swal.fire({
        title: 'Restaurar imóvel?',
        showCancelButton: true,
        confirmButtonText: 'Restaurar',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        router.patch(route('imoveis.restore', imovelId));
    }
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Imóveis</h1>
                <AppBreadcrumb />
            </div>
            <Link :href="route('imoveis.create')" class="btn btn-primary btn-sm">+ Novo Imóvel</Link>
        </div>

        <!-- Indicadores -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body py-3 px-4">
                    <span class="text-xs text-base-content/50">Total</span>
                    <span class="text-xl font-bold">{{ indicadores.total }}</span>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body py-3 px-4">
                    <span class="text-xs text-base-content/50">Disponíveis</span>
                    <span class="text-xl font-bold">{{ indicadores.disponivel }}</span>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body py-3 px-4">
                    <span class="text-xs text-base-content/50">Alugados</span>
                    <span class="text-xl font-bold">{{ indicadores.alugado }}</span>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body py-3 px-4">
                    <span class="text-xs text-base-content/50">Reservados</span>
                    <span class="text-xl font-bold">{{ indicadores.reservado }}</span>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body py-3 px-4">
                    <span class="text-xs text-base-content/50">Em Manutenção</span>
                    <span class="text-xl font-bold">{{ indicadores.em_manutencao }}</span>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body py-3 px-4">
                    <span class="text-xs text-base-content/50">Inativos</span>
                    <span class="text-xl font-bold">{{ indicadores.inativo }}</span>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-4">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    <input type="text" class="input input-bordered input-sm w-full"
                           placeholder="Buscar por título ou código..."
                           v-model="filtros.busca" />
                    <select class="select select-bordered select-sm w-full" v-model="filtros.tipo">
                        <option value="">Todos os tipos</option>
                        <option value="apartamento">Apartamento</option>
                        <option value="casa">Casa</option>
                        <option value="comercial">Comercial</option>
                        <option value="sala">Sala</option>
                        <option value="galpao">Galpão</option>
                        <option value="terreno">Terreno</option>
                        <option value="rural">Rural</option>
                        <option value="outro">Outro</option>
                    </select>
                    <select class="select select-bordered select-sm w-full" v-model="filtros.finalidade">
                        <option value="">Todas as finalidades</option>
                        <option value="aluguel">Aluguel</option>
                        <option value="venda">Venda</option>
                        <option value="aluguel_venda">Aluguel / Venda</option>
                    </select>
                    <select class="select select-bordered select-sm w-full" v-model="filtros.status">
                        <option value="">Todos os status</option>
                        <option value="disponivel">Disponível</option>
                        <option value="reservado">Reservado</option>
                        <option value="alugado">Alugado</option>
                        <option value="em_manutencao">Em Manutenção</option>
                        <option value="inativo">Inativo</option>
                    </select>
                    <input type="text" class="input input-bordered input-sm w-full"
                           placeholder="Filtrar por cidade..."
                           v-model="filtros.cidade" />
                </div>
                <label v-if="podeRestaurar()" class="label cursor-pointer justify-start gap-2 mt-2">
                    <input type="checkbox" class="checkbox checkbox-sm" v-model="filtros.excluidos" />
                    <span class="label-text">Exibir apenas imóveis excluídos</span>
                </label>
            </div>
        </div>

        <!-- Tabela -->
        <div class="card bg-base-100 shadow-sm border border-base-200 overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr class="bg-base-200/50">
                        <th>Foto</th>
                        <th>Código</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Finalidade</th>
                        <th>Proprietário</th>
                        <th>Cidade/Bairro</th>
                        <th>Aluguel</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="imovel in imoveis.data" :key="imovel.id" class="hover">
                        <td class="w-16">
                            <img v-if="imovel.foto_principal"
                                 :src="imovel.foto_principal.url"
                                 :alt="imovel.titulo"
                                 class="w-12 h-10 object-cover rounded-md" />
                            <div v-else class="w-12 h-10 bg-base-200 rounded-md flex items-center justify-center">
                                <ImageOff class="w-5 h-5 text-base-content/30" />
                            </div>
                        </td>
                        <td class="font-mono text-xs">{{ imovel.codigo }}</td>
                        <td class="font-medium max-w-48 truncate">{{ imovel.titulo }}</td>
                        <td><BadgeTipo :tipo="imovel.tipo" /></td>
                        <td><BadgeFinalidade :finalidade="imovel.finalidade" /></td>
                        <td class="text-sm max-w-32 truncate">
                            {{ imovel.proprietario
                                ? (imovel.proprietario.tipo_pessoa === 'juridica'
                                    ? imovel.proprietario.razao_social
                                    : imovel.proprietario.nome)
                                : '—' }}
                        </td>
                        <td class="text-sm">
                            <span>{{ imovel.cidade ?? '—' }}</span>
                            <span v-if="imovel.bairro" class="text-base-content/50"> / {{ imovel.bairro }}</span>
                        </td>
                        <td class="text-sm font-medium">
                            {{ imovel.dados_comerciais?.valor_aluguel
                                ? parseFloat(imovel.dados_comerciais.valor_aluguel).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
                                : '—' }}
                        </td>
                        <td><BadgeStatus :status="imovel.status" /></td>
                        <td>
                            <div class="flex gap-1">
                                <template v-if="!imovel.deleted_at">
                                    <Link :href="route('imoveis.show', imovel.id)" class="btn btn-ghost btn-xs">Ver</Link>
                                    <Link :href="route('imoveis.edit', imovel.id)" class="btn btn-ghost btn-xs">Editar</Link>
                                    <button class="btn btn-ghost btn-xs" @click="alterarStatus(imovel.id, imovel.status)">Status</button>
                                    <button v-if="podeExcluir()" class="btn btn-ghost btn-xs text-error" @click="excluir(imovel.id)">Excluir</button>
                                </template>
                                <button v-else-if="podeRestaurar()" class="btn btn-ghost btn-xs text-success" @click="restaurar(imovel.id)">Restaurar</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="imoveis.data.length === 0">
                        <td colspan="10" class="text-center py-8 text-base-content/40">Nenhum imóvel encontrado.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div v-if="imoveis.last_page > 1" class="flex justify-center gap-1">
            <template v-for="link in imoveis.links" :key="link.label">
                <Link v-if="link.url"
                      :href="link.url"
                      class="btn btn-sm"
                      :class="link.active ? 'btn-primary' : 'btn-ghost'"
                      v-html="link.label" />
                <span v-else class="btn btn-sm btn-disabled" v-html="link.label" />
            </template>
        </div>

        <!-- Info -->
        <p v-if="imoveis.total > 0" class="text-xs text-base-content/50 text-center">
            Exibindo {{ imoveis.from }}–{{ imoveis.to }} de {{ imoveis.total }} imóveis
        </p>
    </div>
</template>

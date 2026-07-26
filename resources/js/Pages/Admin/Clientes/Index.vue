<script setup lang="ts">
import { ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import BadgePapel from '@/Components/Clientes/BadgePapel.vue';
import BadgeStatus from '@/Components/Clientes/BadgeStatus.vue';
import Swal, { swalClass } from '@/lib/swal';
import type { Cliente, ClienteFiltros, ClientePaginado } from '@/types/cliente';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    clientes: ClientePaginado;
    filtros: ClienteFiltros;
}>();

const page = usePage();
const filtros = ref<ClienteFiltros>({ ...props.filtros });

let debounceTimer: ReturnType<typeof setTimeout>;
watch(filtros, (val) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('clientes.index'), val as Record<string, string>, { preserveState: true, replace: true });
    }, 300);
}, { deep: true });

function ordenarPor(campo: string) {
    const direcaoAtual = filtros.value.ordenar_por === campo && filtros.value.direcao === 'asc' ? 'desc' : 'asc';
    filtros.value = { ...filtros.value, ordenar_por: campo, direcao: direcaoAtual };
}

async function confirmarAlterarStatus(cliente: Cliente) {
    const acao = cliente.status === 'ativo' ? 'inativar' : 'ativar';
    const novoStatus = cliente.status === 'ativo' ? 'inativo' : 'ativo';

    const result = await Swal.fire({
        title: `Deseja ${acao} este cliente?`,
        text: `${cliente.nome_exibicao} será ${acao === 'ativar' ? 'ativado' : 'inativado'}.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Sim, ${acao}`,
        cancelButtonText: 'Cancelar',
        customClass: swalClass(acao === 'ativar' ? 'success' : 'error'),
    });

    if (result.isConfirmed) {
        router.patch(route('clientes.alterar-status', cliente.id), { status: novoStatus });
    }
}

function nomeExibicao(cliente: Cliente): string {
    return cliente.nome_exibicao || cliente.nome || cliente.razao_social || '—';
}

function formatarData(data: string): string {
    return new Date(data).toLocaleDateString('pt-BR');
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Clientes</h1>
                <AppBreadcrumb />
            </div>
            <Link :href="route('clientes.create')" class="btn btn-primary">
                Novo Cliente
            </Link>
        </div>

        <!-- Filtros -->
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-3">
                <div class="flex flex-wrap gap-3">
                    <input type="text" class="input input-bordered input-sm flex-1 min-w-48"
                        placeholder="Buscar por nome, CPF ou CNPJ..."
                        v-model="filtros.busca" />

                    <select class="select select-bordered select-sm" v-model="filtros.tipo_pessoa">
                        <option value="">Tipo de Pessoa</option>
                        <option value="fisica">Pessoa Física</option>
                        <option value="juridica">Pessoa Jurídica</option>
                    </select>

                    <select class="select select-bordered select-sm" v-model="filtros.papel">
                        <option value="">Papel</option>
                        <option value="proprietario">Proprietário</option>
                        <option value="inquilino">Inquilino</option>
                    </select>

                    <select class="select select-bordered select-sm" v-model="filtros.status">
                        <option value="">Status</option>
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>

                    <input type="text" class="input input-bordered input-sm w-40"
                        placeholder="Cidade..."
                        v-model="filtros.cidade" />
                </div>
            </div>
        </div>

        <!-- Tabela -->
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th class="cursor-pointer" @click="ordenarPor('nome')">
                                Nome / Razão Social
                                <span v-if="filtros.ordenar_por === 'nome'">{{ filtros.direcao === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th>CPF / CNPJ</th>
                            <th>Tipo</th>
                            <th>Telefone</th>
                            <th>E-mail</th>
                            <th>Papéis</th>
                            <th>Cidade</th>
                            <th class="cursor-pointer" @click="ordenarPor('status')">
                                Status
                                <span v-if="filtros.ordenar_por === 'status'">{{ filtros.direcao === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th class="cursor-pointer" @click="ordenarPor('created_at')">
                                Cadastro
                                <span v-if="filtros.ordenar_por === 'created_at'">{{ filtros.direcao === 'asc' ? '↑' : '↓' }}</span>
                            </th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="clientes.data.length === 0">
                            <td colspan="10" class="text-center text-base-content/60 py-8">
                                Nenhum cliente encontrado.
                            </td>
                        </tr>
                        <tr v-for="cliente in clientes.data" :key="cliente.id" class="hover">
                            <td class="font-medium">
                                <Link :href="route('clientes.show', cliente.id)" class="link link-hover">
                                    {{ nomeExibicao(cliente) }}
                                </Link>
                            </td>
                            <td class="text-sm text-base-content/70">{{ cliente.documento || '—' }}</td>
                            <td class="text-sm">{{ cliente.tipo_pessoa === 'fisica' ? 'Física' : 'Jurídica' }}</td>
                            <td class="text-sm">{{ cliente.telefone_principal || '—' }}</td>
                            <td class="text-sm">{{ cliente.email_principal || '—' }}</td>
                            <td>
                                <div class="flex gap-1 flex-wrap">
                                    <BadgePapel v-for="p in cliente.papeis" :key="p.papel" :papel="p.papel" />
                                </div>
                            </td>
                            <td class="text-sm">{{ cliente.cidade || '—' }}</td>
                            <td><BadgeStatus :status="cliente.status" /></td>
                            <td class="text-sm text-base-content/60">{{ formatarData(cliente.created_at) }}</td>
                            <td>
                                <div class="flex gap-1">
                                    <Link :href="route('clientes.show', cliente.id)" class="btn btn-ghost btn-xs">Ver</Link>
                                    <Link :href="route('clientes.edit', cliente.id)" class="btn btn-ghost btn-xs">Editar</Link>
                                    <button class="btn btn-ghost btn-xs"
                                        :class="cliente.status === 'ativo' ? 'text-error' : 'text-success'"
                                        @click="confirmarAlterarStatus(cliente)">
                                        {{ cliente.status === 'ativo' ? 'Inativar' : 'Ativar' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div v-if="clientes.last_page > 1" class="card-body pt-2 flex items-center justify-between">
                <span class="text-sm text-base-content/60">
                    Exibindo {{ clientes.from }}–{{ clientes.to }} de {{ clientes.total }} registros
                </span>
                <div class="join">
                    <template v-for="link in clientes.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="join-item btn btn-sm"
                            :class="{ 'btn-active': link.active }"
                            v-html="link.label"
                        />
                        <button v-else class="join-item btn btn-sm btn-disabled" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Flash -->
        <div v-if="(page.props as any).flash?.status" class="alert alert-success">
            {{ (page.props as any).flash.status }}
        </div>
    </div>
</template>

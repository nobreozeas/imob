<script setup lang="ts">
import { ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import BadgeStatus from '@/Components/Usuarios/BadgeStatus.vue';
import BadgePrimeiroAcesso from '@/Components/Usuarios/BadgePrimeiroAcesso.vue';
import Swal, { swalClass } from '@/lib/swal';
import type { Perfil, Usuario, UsuarioFiltros, UsuarioPaginado } from '@/types/usuario';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    usuarios: UsuarioPaginado;
    filtros: UsuarioFiltros;
    roles: Perfil[];
}>();

const page = usePage();
const auth = (page.props as any).auth;

const filtros = ref<UsuarioFiltros>({ ...props.filtros });

let debounceTimer: ReturnType<typeof setTimeout>;
watch(filtros, (val) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('usuarios.index'), val as Record<string, string>, { preserveState: true, replace: true });
    }, 300);
}, { deep: true });

function podeEditar(): boolean {
    return auth?.permissions?.includes('usuarios.update');
}

function podeAlterarStatus(): boolean {
    return auth?.permissions?.includes('usuarios.alterar-status');
}

function podeReenviar(): boolean {
    return auth?.permissions?.includes('usuarios.reenviar-acesso');
}

function podeCriar(): boolean {
    return auth?.permissions?.includes('usuarios.create');
}

async function confirmarAlterarStatus(usuario: Usuario) {
    const acao = usuario.status === 'ativo' ? 'inativar' : 'ativar';
    const novoStatus = usuario.status === 'ativo' ? 'inativo' : 'ativo';

    const result = await Swal.fire({
        title: `Deseja ${acao} este usuário?`,
        text: `${usuario.name} será ${acao === 'ativar' ? 'ativado' : 'inativado'}.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Sim, ${acao}`,
        cancelButtonText: 'Cancelar',
        customClass: swalClass(acao === 'ativar' ? 'success' : 'error'),
    });

    if (result.isConfirmed) {
        router.patch(route('usuarios.alterar-status', usuario.id), { status: novoStatus });
    }
}

async function confirmarReenvio(usuario: Usuario) {
    const result = await Swal.fire({
        title: 'Reenviar acesso inicial?',
        text: `Uma nova senha temporária será gerada e enviada para ${usuario.email}.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, reenviar',
        cancelButtonText: 'Cancelar',
    });

    if (result.isConfirmed) {
        router.post(route('usuarios.reenviar-acesso', usuario.id));
    }
}

function formatarData(data: string | null): string {
    if (!data) return '—';
    return new Date(data).toLocaleDateString('pt-BR');
}

function roleName(usuario: Usuario): string {
    return usuario.roles?.[0]?.name ?? '—';
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Usuários</h1>
                <AppBreadcrumb />
            </div>
            <Link v-if="podeCriar()" :href="route('usuarios.create')" class="btn btn-primary">
                Novo Usuário
            </Link>
        </div>

        <!-- Filtros -->
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-3">
                <div class="flex flex-wrap gap-3">
                    <input type="text" class="input input-bordered input-sm flex-1 min-w-48"
                        placeholder="Buscar por nome ou e-mail..."
                        v-model="filtros.busca" />

                    <select class="select select-bordered select-sm" v-model="filtros.role">
                        <option value="">Todos os perfis</option>
                        <option v-for="role in roles" :key="role.id" :value="role.name">
                            {{ role.name }}
                        </option>
                    </select>

                    <select class="select select-bordered select-sm" v-model="filtros.status">
                        <option value="">Status</option>
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>

                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="checkbox" class="checkbox checkbox-sm"
                            v-model="filtros.primeiro_acesso_pendente" />
                        Primeiro acesso pendente
                    </label>
                </div>
            </div>
        </div>

        <!-- Tabela -->
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Perfil</th>
                            <th>Status</th>
                            <th>Primeiro Acesso</th>
                            <th>Último Acesso</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="usuarios.data.length === 0">
                            <td colspan="7" class="text-center text-base-content/60 py-8">
                                Nenhum usuário encontrado.
                            </td>
                        </tr>
                        <tr v-for="usuario in usuarios.data" :key="usuario.id" class="hover">
                            <td class="font-medium">{{ usuario.name }}</td>
                            <td class="text-sm text-base-content/70">{{ usuario.email }}</td>
                            <td class="text-sm capitalize">{{ roleName(usuario) }}</td>
                            <td><BadgeStatus :status="usuario.status" /></td>
                            <td><BadgePrimeiroAcesso :pendente="usuario.deve_alterar_senha" /></td>
                            <td class="text-sm text-base-content/60">{{ formatarData(usuario.ultimo_acesso_em) }}</td>
                            <td>
                                <div class="flex gap-1">
                                    <Link v-if="podeEditar()"
                                        :href="route('usuarios.edit', usuario.id)"
                                        class="btn btn-ghost btn-xs">
                                        Editar
                                    </Link>
                                    <button v-if="podeAlterarStatus()"
                                        class="btn btn-ghost btn-xs"
                                        :class="usuario.status === 'ativo' ? 'text-error' : 'text-success'"
                                        @click="confirmarAlterarStatus(usuario)">
                                        {{ usuario.status === 'ativo' ? 'Inativar' : 'Ativar' }}
                                    </button>
                                    <button v-if="podeReenviar() && usuario.deve_alterar_senha"
                                        class="btn btn-ghost btn-xs text-warning"
                                        @click="confirmarReenvio(usuario)">
                                        Reenviar acesso
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div v-if="usuarios.last_page > 1" class="card-body pt-2 flex items-center justify-between">
                <span class="text-sm text-base-content/60">
                    Exibindo {{ usuarios.from }}–{{ usuarios.to }} de {{ usuarios.total }} registros
                </span>
                <div class="join">
                    <template v-for="link in usuarios.links" :key="link.label">
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

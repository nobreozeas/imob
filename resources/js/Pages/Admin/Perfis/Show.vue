<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import type { Perfil } from '@/types/perfil';

defineOptions({ layout: AdminLayout });

defineProps<{
    perfil: Perfil;
    permissoesPorModulo: Record<string, string[]>;
}>();

function labelPermissao(perm: string): string {
    const acao = perm.split('.').slice(1).join('.');
    const labels: Record<string, string> = {
        viewAny: 'Listar',
        view: 'Visualizar',
        create: 'Criar',
        update: 'Editar',
        'alterar-status': 'Alterar status',
        'reenviar-acesso': 'Reenviar acesso',
        ativar: 'Ativar',
        cancelar: 'Cancelar',
        encerrar: 'Encerrar',
        rescindir: 'Rescindir',
        documentos: 'Documentos',
        ver: 'Visualizar',
        criar: 'Criar',
        editar: 'Editar',
        'ativar-inativar': 'Ativar/Inativar',
    };
    return labels[acao] ?? acao;
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-base-content capitalize">Perfil: {{ perfil.name }}</h1>
                <AppBreadcrumb />
            </div>
            <Link :href="route('perfis.index')" class="btn btn-ghost btn-sm">Voltar</Link>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-lg mb-4">Permissões por módulo</h2>

                <div v-if="Object.keys(permissoesPorModulo).length === 0" class="text-base-content/60">
                    Este perfil não possui permissões atribuídas.
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Módulo</th>
                                <th>Permissões</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(perms, modulo) in permissoesPorModulo" :key="modulo" class="hover">
                                <td class="font-medium capitalize w-48">{{ modulo }}</td>
                                <td>
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="perm in perms" :key="perm"
                                            class="badge badge-sm badge-outline">
                                            {{ labelPermissao(perm) }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

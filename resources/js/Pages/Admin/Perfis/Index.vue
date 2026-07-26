<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import type { Perfil } from '@/types/perfil';

defineOptions({ layout: AdminLayout });

defineProps<{ perfis: Perfil[] }>();

const page = usePage();
</script>

<template>
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-bold text-base-content">Perfis</h1>
            <AppBreadcrumb />
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Total de usuários</th>
                            <th>Permissões</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="perfis.length === 0">
                            <td colspan="4" class="text-center text-base-content/60 py-8">
                                Nenhum perfil cadastrado.
                            </td>
                        </tr>
                        <tr v-for="perfil in perfis" :key="perfil.id" class="hover">
                            <td class="font-medium capitalize">{{ perfil.name }}</td>
                            <td>{{ perfil.users_count ?? 0 }}</td>
                            <td class="text-sm text-base-content/60">{{ perfil.permissions.length }} permissões</td>
                            <td>
                                <Link :href="route('perfis.show', perfil.id)" class="btn btn-ghost btn-xs">
                                    Visualizar
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="(page.props as any).flash?.status" class="alert alert-success">
            {{ (page.props as any).flash.status }}
        </div>
    </div>
</template>

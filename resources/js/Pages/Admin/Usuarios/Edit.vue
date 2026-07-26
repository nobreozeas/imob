<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import type { Perfil, Usuario } from '@/types/usuario';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    usuario: Usuario;
    roles: Perfil[];
}>();

const form = useForm({
    name: props.usuario.name,
    role: props.usuario.roles?.[0]?.name ?? '',
    status: props.usuario.status,
});

function submit() {
    form.put(route('usuarios.update', props.usuario.id));
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Editar Usuário</h1>
                <AppBreadcrumb />
            </div>
            <Link :href="route('usuarios.index')" class="btn btn-ghost btn-sm">Voltar</Link>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body max-w-lg">
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Nome *</span></label>
                        <input type="text" class="input input-bordered" :class="{ 'input-error': form.errors.name }"
                            v-model="form.name" />
                        <p v-if="form.errors.name" class="text-error text-sm mt-1">{{ form.errors.name }}</p>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">E-mail</span></label>
                        <input type="email" class="input input-bordered input-disabled" :value="usuario.email" disabled />
                        <p class="text-base-content/50 text-xs mt-1">O e-mail não pode ser alterado após o cadastro.</p>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Perfil *</span></label>
                        <select class="select select-bordered" :class="{ 'select-error': form.errors.role }"
                            v-model="form.role">
                            <option value="">Selecione um perfil</option>
                            <option v-for="role in roles" :key="role.id" :value="role.name">
                                {{ role.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.role" class="text-error text-sm mt-1">{{ form.errors.role }}</p>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Status *</span></label>
                        <select class="select select-bordered" :class="{ 'select-error': form.errors.status }"
                            v-model="form.status">
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                        <p v-if="form.errors.status" class="text-error text-sm mt-1">{{ form.errors.status }}</p>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            <span v-if="form.processing" class="loading loading-spinner loading-sm" />
                            Salvar alterações
                        </button>
                        <Link :href="route('usuarios.index')" class="btn btn-ghost">Cancelar</Link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

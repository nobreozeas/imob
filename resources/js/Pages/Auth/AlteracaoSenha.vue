<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputSenha from '@/Components/Auth/InputSenha.vue';
import type { AlteracaoSenhaForm } from '@/types/auth';

const form = useForm<AlteracaoSenhaForm>({
    current_password: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.put(route('senha.update'), {
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <div class="max-w-md">
        <h3 class="text-lg font-semibold mb-4">Alterar senha</h3>

        <div v-if="$page.props.flash?.status" class="alert alert-success mb-4">
            <span>{{ $page.props.flash.status }}</span>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <InputSenha
                v-model="form.current_password"
                label="Senha atual"
                placeholder="Sua senha atual"
                :error="form.errors.current_password"
            />

            <InputSenha
                v-model="form.password"
                label="Nova senha"
                placeholder="Crie uma nova senha"
                :error="form.errors.password"
            />

            <InputSenha
                v-model="form.password_confirmation"
                label="Confirmar nova senha"
                placeholder="Repita a nova senha"
            />

            <button
                type="submit"
                class="btn btn-primary"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                <span v-else>Alterar senha</span>
            </button>
        </form>
    </div>
</template>

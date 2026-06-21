<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputSenha from '@/Components/Auth/InputSenha.vue';
import { KeyRound } from 'lucide-vue-next';
import type { RedefinirSenhaForm } from '@/types/auth';

defineOptions({ layout: AuthLayout });

const props = defineProps<{
    token: string;
    email?: string;
}>();

const form = useForm<RedefinirSenhaForm>({
    token: props.token,
    email: props.email ?? '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <div>
        <div class="mb-8">
            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4">
                <KeyRound class="w-6 h-6 text-primary" />
            </div>
            <h2 class="text-2xl font-bold text-base-content">Redefinir senha</h2>
            <p class="text-base-content/60 mt-1">Crie uma nova senha para sua conta.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text font-medium">E-mail</span>
                </label>
                <input
                    v-model="form.email"
                    type="email"
                    :class="['input input-bordered w-full', form.errors.email ? 'input-error' : '']"
                    readonly
                />
                <label v-if="form.errors.email" class="label">
                    <span class="label-text-alt text-error">{{ form.errors.email }}</span>
                </label>
            </div>

            <InputSenha
                v-model="form.password"
                label="Nova senha"
                placeholder="Crie uma senha segura"
                :error="form.errors.password"
            />

            <InputSenha
                v-model="form.password_confirmation"
                label="Confirmar nova senha"
                placeholder="Repita a senha"
            />

            <button
                type="submit"
                class="btn btn-primary w-full"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                <span v-else>Redefinir senha</span>
            </button>
        </form>
    </div>
</template>

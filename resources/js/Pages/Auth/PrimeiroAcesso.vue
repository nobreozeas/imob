<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputSenha from '@/Components/Auth/InputSenha.vue';
import { KeyRound } from 'lucide-vue-next';
import type { PrimeiroAcessoForm } from '@/types/auth';

defineOptions({ layout: AuthLayout });

const form = useForm<PrimeiroAcessoForm>({
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('primeiro-acesso.store'), {
        onFinish: () => form.reset(),
    });
}
</script>

<template>
    <div>
        <div class="mb-8">
            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4">
                <KeyRound class="w-6 h-6 text-primary" />
            </div>
            <h2 class="text-2xl font-bold text-base-content">Defina sua senha</h2>
            <p class="text-base-content/60 mt-1">
                Este é seu primeiro acesso. Crie uma senha definitiva para continuar.
            </p>
        </div>

        <div class="alert alert-info mb-6">
            <span class="text-sm">A senha deve ter pelo menos 8 caracteres, com letras e números.</span>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
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
                :error="form.errors.password_confirmation"
            />

            <button
                type="submit"
                class="btn btn-primary w-full mt-2"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                <span v-else>Definir senha e entrar</span>
            </button>
        </form>
    </div>
</template>

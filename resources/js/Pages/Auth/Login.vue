<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputSenha from '@/Components/Auth/InputSenha.vue';
import type { LoginForm } from '@/types/auth';

defineOptions({ layout: AuthLayout });

const form = useForm<LoginForm>({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post(route('login.store'), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <div>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-base-content">Bem-vindo de volta!</h2>
            <p class="text-base-content/60 mt-1">Faça login para acessar sua conta</p>
        </div>

        <!-- Mensagem de sucesso (ex: após redefinir senha) -->
        <div v-if="$page.props.flash?.status" class="alert alert-success mb-4">
            <span>{{ $page.props.flash.status }}</span>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Email -->
            <div class="form-control w-full">
                <label class="label">
                    <span class="label-text font-medium">E-mail</span>
                </label>
                <input
                    v-model="form.email"
                    type="email"
                    placeholder="seu@email.com"
                    :class="['input input-bordered w-full', form.errors.email ? 'input-error' : '']"
                    autocomplete="email"
                    autofocus
                />
                <label v-if="form.errors.email" class="label">
                    <span class="label-text-alt text-error">{{ form.errors.email }}</span>
                </label>
            </div>

            <!-- Senha -->
            <InputSenha
                v-model="form.password"
                label="Senha"
                placeholder="Sua senha"
                :error="form.errors.password"
            />

            <!-- Lembrar acesso -->
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input
                        v-model="form.remember"
                        type="checkbox"
                        class="checkbox checkbox-primary checkbox-sm"
                    />
                    <span class="text-sm text-base-content/70">Lembrar acesso</span>
                </label>
                <a :href="route('password.request')" class="text-sm text-primary hover:underline">
                    Esqueci minha senha
                </a>
            </div>

            <!-- Botão -->
            <button
                type="submit"
                class="btn btn-primary w-full"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                <span v-else>Entrar</span>
            </button>
        </form>
    </div>
</template>

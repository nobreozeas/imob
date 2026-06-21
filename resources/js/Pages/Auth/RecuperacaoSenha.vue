<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { Mail } from 'lucide-vue-next';
import type { RecuperacaoSenhaForm } from '@/types/auth';

defineOptions({ layout: AuthLayout });

const form = useForm<RecuperacaoSenhaForm>({
    email: '',
});

function submit() {
    form.post(route('password.email'));
}
</script>

<template>
    <div>
        <div class="mb-8">
            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-4">
                <Mail class="w-6 h-6 text-primary" />
            </div>
            <h2 class="text-2xl font-bold text-base-content">Esqueceu sua senha?</h2>
            <p class="text-base-content/60 mt-1">
                Informe seu email e enviaremos as instruções para redefinição.
            </p>
        </div>

        <!-- Status após envio -->
        <div v-if="$page.props.flash?.status" class="alert alert-success mb-6">
            <span>{{ $page.props.flash.status }}</span>
        </div>

        <form v-else @submit.prevent="submit" class="space-y-4">
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

            <button
                type="submit"
                class="btn btn-primary w-full"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                <span v-else>Enviar instruções</span>
            </button>
        </form>

        <div class="mt-6 text-center">
            <a :href="route('login')" class="text-sm text-primary hover:underline">
                ← Voltar para o login
            </a>
        </div>
    </div>
</template>

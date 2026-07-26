<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, Building2, Users, FileText, DollarSign } from 'lucide-vue-next';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const page = usePage();

const permissions = computed<string[]>(() => (page.props as any).auth?.permissions ?? []);
const primeiroNome = computed(() => {
    const nome = (page.props as any).auth?.user?.name as string | undefined;
    return nome?.split(' ')[0] ?? '';
});

const hoje = computed(() =>
    new Intl.DateTimeFormat('pt-BR', { weekday: 'long', day: 'numeric', month: 'long' }).format(new Date()),
);

const ATALHOS = [
    { label: 'Imóveis', description: 'Cadastro e status da carteira', icon: Building2, href: '/imoveis' },
    { label: 'Clientes', description: 'Locatários e proprietários', icon: Users, href: '/clientes' },
    { label: 'Contratos', description: 'Locações ativas e encerradas', icon: FileText, href: '/contratos' },
    {
        label: 'Financeiro',
        description: 'Recebimentos e repasses',
        icon: DollarSign,
        href: '/financeiro/dashboard',
        permission: 'financeiro.visualizar',
    },
];

const atalhos = computed(() => ATALHOS.filter((a) => !a.permission || permissions.value.includes(a.permission)));
</script>

<template>
    <div class="mx-auto max-w-5xl">
        <div class="bg-blueprint relative overflow-hidden rounded-box border border-base-300 bg-base-100 px-8 py-10">
            <p class="font-mono text-xs uppercase tracking-[0.14em] text-base-content/40">{{ hoje }}</p>
            <h1 class="mt-2 font-display text-3xl font-semibold text-base-content sm:text-4xl">
                Bom dia<span v-if="primeiroNome">, {{ primeiroNome }}</span>.
            </h1>
            <p class="mt-3 max-w-lg text-base-content/60">
                O painel geral com indicadores ainda está em construção. Enquanto isso, os módulos abaixo já estão
                prontos para uso.
            </p>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
            <Link
                v-for="atalho in atalhos"
                :key="atalho.href"
                :href="atalho.href"
                class="group flex items-center gap-4 rounded-box border border-base-300 bg-base-100 px-5 py-4 transition-colors hover:border-primary/40 hover:bg-primary/5"
            >
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-field bg-primary/10 text-primary">
                    <component :is="atalho.icon" class="h-5 w-5" />
                </span>
                <span class="flex-1">
                    <span class="block font-medium text-base-content">{{ atalho.label }}</span>
                    <span class="block text-sm text-base-content/50">{{ atalho.description }}</span>
                </span>
                <ArrowRight
                    class="h-4 w-4 shrink-0 text-base-content/30 transition-transform group-hover:translate-x-0.5 group-hover:text-primary"
                />
            </Link>
        </div>
    </div>
</template>

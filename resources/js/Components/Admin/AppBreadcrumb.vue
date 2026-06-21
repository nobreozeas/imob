<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const LABELS: Record<string, string> = {
    dashboard: 'Dashboard',
    imoveis: 'Imóveis',
    clientes: 'Clientes',
    contratos: 'Contratos',
    recebimentos: 'Recebimentos',
    configuracoes: 'Configurações',
    'minha-conta': 'Minha Conta',
    novo: 'Novo',
    editar: 'Editar',
};

const page = usePage();

const crumbs = computed(() => {
    const segments = page.url
        .split('?')[0]
        .split('/')
        .filter(Boolean);

    return segments.map((segment, index) => ({
        label: LABELS[segment] ?? segment,
        href: '/' + segments.slice(0, index + 1).join('/'),
        isLast: index === segments.length - 1,
    }));
});
</script>

<template>
    <nav class="breadcrumbs text-sm">
        <ul>
            <li>
                <Link :href="route('dashboard')">Início</Link>
            </li>
            <li v-for="crumb in crumbs" :key="crumb.href">
                <Link v-if="!crumb.isLast" :href="crumb.href">{{ crumb.label }}</Link>
                <span v-else class="text-base-content/60">{{ crumb.label }}</span>
            </li>
        </ul>
    </nav>
</template>

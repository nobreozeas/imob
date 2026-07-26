<script setup lang="ts">
import type { StatusUsuario } from '@/types/usuario';

defineProps<{ status: StatusUsuario }>();

function label(status: StatusUsuario): string {
    const labels: Record<StatusUsuario, string> = {
        ativo: 'Ativo',
        inativo: 'Inativo',
        bloqueado: 'Bloqueado',
    };
    return labels[status] ?? status;
}

function cor(status: StatusUsuario): string {
    const cores: Record<StatusUsuario, string> = {
        ativo: 'badge-success',
        inativo: 'badge-error',
        bloqueado: 'badge-warning',
    };
    return cores[status] ?? 'badge-ghost';
}
</script>

<template>
    <span class="badge badge-sm font-medium" :class="cor(status)">
        {{ label(status) }}
    </span>
</template>

<script setup lang="ts">
import type { StatusLancamentoFinanceiro } from '@/types/lancamentoFinanceiro';

defineProps<{ status: StatusLancamentoFinanceiro }>();

const cores: Record<StatusLancamentoFinanceiro, string> = {
    pendente: 'badge-warning',
    pago: 'badge-success',
    cancelado: 'badge-error',
    estornado: 'badge-neutral',
};

const labels: Record<StatusLancamentoFinanceiro, string> = {
    pendente: 'Pendente',
    pago: 'Pago',
    cancelado: 'Cancelado',
    estornado: 'Estornado',
};
</script>

<template>
    <span class="badge badge-sm" :class="cores[status]">{{ labels[status] }}</span>
</template>

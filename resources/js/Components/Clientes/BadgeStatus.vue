<script setup lang="ts">
import { useClienteStatus } from '@/composables/useClienteStatus';
import type { StatusCliente } from '@/types/cliente';

defineProps<{ status: StatusCliente }>();

const { labelStatus, corStatus } = useClienteStatus();
</script>

<template>
    <span class="badge badge-sm font-medium" :class="corStatus(status)">
        {{ labelStatus(status) }}
    </span>
</template>

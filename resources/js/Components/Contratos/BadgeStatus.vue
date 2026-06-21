<script setup lang="ts">
import { useContratoStatus } from '@/composables/useContratoStatus';
import type { StatusContrato } from '@/types/contrato';

defineProps<{ status: StatusContrato }>();

const { labelStatus, corStatus } = useContratoStatus();
</script>

<template>
    <span class="badge" :class="corStatus(status)">{{ labelStatus(status) }}</span>
</template>

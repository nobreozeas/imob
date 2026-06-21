<script setup lang="ts">
import { useClienteStatus } from '@/composables/useClienteStatus';
import type { PapelCliente } from '@/types/cliente';

defineProps<{ papel: PapelCliente }>();

const { labelPapel, corPapel } = useClienteStatus();
</script>

<template>
    <span class="badge badge-sm font-medium" :class="corPapel(papel)">
        {{ labelPapel(papel) }}
    </span>
</template>

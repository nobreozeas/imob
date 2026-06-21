<script setup lang="ts">
import { computed } from 'vue';
import type { TipoImovel } from '@/types/imovel';
import { useImovelStatus } from '@/composables/useImovelStatus';

const props = defineProps<{ tipo: TipoImovel }>();
const { labelTipo } = useImovelStatus();
const label = computed(() => labelTipo(props.tipo));
</script>

<template>
    <span class="badge badge-neutral">{{ label }}</span>
</template>

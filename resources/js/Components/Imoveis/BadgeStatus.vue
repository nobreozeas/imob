<script setup lang="ts">
import { computed } from 'vue';
import type { StatusImovel } from '@/types/imovel';
import { useImovelStatus } from '@/composables/useImovelStatus';

const props = defineProps<{ status: StatusImovel }>();
const { labelStatus, corStatus } = useImovelStatus();
const classe = computed(() => corStatus(props.status));
const label = computed(() => labelStatus(props.status));
</script>

<template>
    <span class="badge" :class="classe">{{ label }}</span>
</template>

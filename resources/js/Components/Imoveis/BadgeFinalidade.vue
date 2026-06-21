<script setup lang="ts">
import { computed } from 'vue';
import type { FinalidadeImovel } from '@/types/imovel';
import { useImovelStatus } from '@/composables/useImovelStatus';

const props = defineProps<{ finalidade: FinalidadeImovel }>();
const { labelFinalidade, corFinalidade } = useImovelStatus();
const label = computed(() => labelFinalidade(props.finalidade));
const classe = computed(() => corFinalidade(props.finalidade));
</script>

<template>
    <span class="badge" :class="classe">{{ label }}</span>
</template>

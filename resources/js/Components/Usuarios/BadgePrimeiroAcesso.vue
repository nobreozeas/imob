<script setup lang="ts">
defineProps<{ pendente: boolean }>();
</script>

<template>
    <span class="badge badge-sm font-medium" :class="pendente ? 'badge-warning' : 'badge-success'">
        {{ pendente ? 'Pendente' : 'Concluído' }}
    </span>
</template>

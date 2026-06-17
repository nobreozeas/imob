<script setup lang="ts">
defineProps<{
    mensagem?: string;
}>();
</script>

<template>
    <div v-if="mensagem" class="alert alert-error">
        <span>{{ mensagem }}</span>
    </div>
</template>

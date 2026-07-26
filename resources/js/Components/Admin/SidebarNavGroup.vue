<script setup lang="ts">
defineProps<{
    title: string;
}>();
</script>

<template>
    <div class="mb-4">
        <p class="px-4 mb-1.5 font-mono text-[10px] font-medium uppercase tracking-[0.14em] text-base-content/35 is-drawer-close:hidden">
            {{ title }}
        </p>
        <ul class="menu menu-sm gap-0.5 p-0">
            <slot />
        </ul>
    </div>
</template>

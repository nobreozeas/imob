<script setup lang="ts">
defineProps<{
    title: string;
}>();
</script>

<template>
    <div class="mb-4">
        <p class="px-4 mb-1 text-xs font-semibold uppercase tracking-wider text-base-content/40 is-drawer-close:hidden">
            {{ title }}
        </p>
        <ul class="menu menu-sm p-0">
            <slot />
        </ul>
    </div>
</template>

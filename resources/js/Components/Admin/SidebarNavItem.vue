<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

defineProps<{
    label: string;
    icon: LucideIcon;
    href: string;
    active?: boolean;
}>();
</script>

<template>
    <li
        class="is-drawer-close:tooltip is-drawer-close:tooltip-right"
        :data-tip="label"
    >
        <Link
            :href="href"
            :class="['flex items-center gap-3', active ? 'active' : '']"
        >
            <component :is="icon" class="w-5 h-5 shrink-0" />
            <span class="is-drawer-close:hidden">{{ label }}</span>
        </Link>
    </li>
</template>

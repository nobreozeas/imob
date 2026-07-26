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
            :class="[
                'flex items-center gap-3 rounded-l-none rounded-r-field border-l-2 px-3 py-2 text-sm transition-colors',
                active
                    ? 'border-primary bg-primary/8 font-medium text-primary'
                    : 'border-transparent text-base-content/65 hover:border-base-300 hover:bg-base-200 hover:text-base-content',
            ]"
        >
            <component :is="icon" class="h-[18px] w-[18px] shrink-0" />
            <span class="is-drawer-close:hidden">{{ label }}</span>
        </Link>
    </li>
</template>

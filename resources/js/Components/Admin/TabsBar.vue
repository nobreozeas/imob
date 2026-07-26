<script setup lang="ts">
defineProps<{
    tabs: { slug: string; label: string; count?: number }[];
    active: string;
}>();

defineEmits<{ select: [slug: string] }>();
</script>

<template>
    <div role="tablist" class="flex gap-6 overflow-x-auto">
        <button
            v-for="tab in tabs"
            :key="tab.slug"
            type="button"
            role="tab"
            :aria-selected="active === tab.slug"
            class="flex shrink-0 items-center gap-1.5 border-b-2 py-3 text-sm transition-colors"
            :class="
                active === tab.slug
                    ? 'border-primary font-medium text-base-content'
                    : 'border-base-300 text-base-content/50 hover:border-base-content/20 hover:text-base-content'
            "
            @click="$emit('select', tab.slug)"
        >
            {{ tab.label }}
            <span
                v-if="tab.count"
                class="rounded-field bg-base-200 px-1.5 py-0.5 font-mono text-[11px] leading-none text-base-content/50"
            >
                {{ tab.count }}
            </span>
        </button>
    </div>
</template>

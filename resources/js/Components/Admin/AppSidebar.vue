<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useNavigation } from '@/composables/useNavigation';
import SidebarNavGroup from '@/Components/Admin/SidebarNavGroup.vue';
import SidebarNavItem from '@/Components/Admin/SidebarNavItem.vue';

const { NAV_GROUPS } = useNavigation();
const page = usePage();

const permissions = computed<string[]>(() => (page.props as any).auth?.permissions ?? []);

function isActive(href: string): boolean {
    return page.url.startsWith(href);
}

function podeVer(permission?: string): boolean {
    if (!permission) return true;
    return permissions.value.includes(permission);
}

const grupos = computed(() =>
    NAV_GROUPS.map(g => ({
        ...g,
        items: g.items.filter(i => podeVer(i.permission)),
    })).filter(g => g.items.length > 0),
);
</script>

<template>
    <div class="flex min-h-full flex-col border-r border-base-300 bg-base-100 transition-[width] duration-300 is-drawer-close:w-14 is-drawer-open:w-64">
        <!-- Logo -->
        <div class="bg-blueprint relative flex h-16 shrink-0 items-center overflow-hidden border-b border-base-300 px-3">
            <img
                :src="'/assets/images/logo.png'"
                alt="ImobGestor"
                class="h-8 object-contain is-drawer-close:hidden"
            />
            <img
                :src="'/assets/images/logo1.png'"
                alt="ImobGestor"
                class="h-8 object-contain is-drawer-open:hidden"
            />
        </div>

        <!-- Navegação -->
        <nav class="flex-1 overflow-y-auto px-2 py-4">
            <SidebarNavGroup
                v-for="group in grupos"
                :key="group.title"
                :title="group.title"
            >
                <SidebarNavItem
                    v-for="item in group.items"
                    :key="item.routeName"
                    :label="item.label"
                    :icon="item.icon"
                    :href="item.href"
                    :active="isActive(item.href)"
                />
            </SidebarNavGroup>
        </nav>
    </div>
</template>

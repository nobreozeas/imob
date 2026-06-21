<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { useNavigation } from '@/composables/useNavigation';
import SidebarNavGroup from '@/Components/Admin/SidebarNavGroup.vue';
import SidebarNavItem from '@/Components/Admin/SidebarNavItem.vue';

const { NAV_GROUPS } = useNavigation();
const page = usePage();

function isActive(href: string): boolean {
    return page.url.startsWith(href);
}
</script>

<template>
    <div class="flex min-h-full flex-col bg-base-100 border-r border-base-200 is-drawer-close:w-14 is-drawer-open:w-64 transition-[width] duration-300">
        <!-- Logo -->
        <div class="flex items-center h-16 shrink-0 border-b border-base-200 px-3 overflow-hidden">
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
        <nav class="flex-1 overflow-y-auto py-4 px-1">
            <SidebarNavGroup
                v-for="group in NAV_GROUPS"
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

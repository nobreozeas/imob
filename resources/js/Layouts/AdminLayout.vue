<script setup lang="ts">
import AppSidebar from '@/Components/Admin/AppSidebar.vue';
import AppTopbar from '@/Components/Admin/AppTopbar.vue';
</script>

<template>
    <div class="drawer lg:drawer-open">
        <input id="admin-drawer" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex flex-col min-h-screen">
            <AppTopbar />
            <main class="flex-1 p-6 bg-base-200 overflow-auto">
                <slot />
            </main>
        </div>

        <div class="drawer-side is-drawer-close:overflow-visible">
            <label for="admin-drawer" aria-label="fechar menu" class="drawer-overlay" />
            <AppSidebar />
        </div>
    </div>
</template>

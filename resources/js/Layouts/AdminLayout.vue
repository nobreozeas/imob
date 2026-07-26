<script setup lang="ts">
import AppSidebar from '@/Components/Admin/AppSidebar.vue';
import AppTopbar from '@/Components/Admin/AppTopbar.vue';
</script>

<template>
    <div class="drawer lg:drawer-open">
        <input id="admin-drawer" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex min-h-screen flex-col">
            <AppTopbar />
            <main class="flex-1 overflow-auto bg-base-200 p-6 lg:p-8">
                <slot />
            </main>
        </div>

        <div class="drawer-side is-drawer-close:overflow-visible">
            <label for="admin-drawer" aria-label="fechar menu" class="drawer-overlay" />
            <AppSidebar />
        </div>
    </div>
</template>

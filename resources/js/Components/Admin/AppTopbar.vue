<script setup lang="ts">
import { usePage, router } from '@inertiajs/vue3';
import { LogOut, PanelLeft } from 'lucide-vue-next';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';

const page = usePage();

function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <nav class="navbar sticky top-0 z-30 h-16 w-full border-b border-base-300 bg-base-100 px-4">
        <!-- Botão toggle do sidebar (DaisyUI label) -->
        <label
            for="admin-drawer"
            aria-label="alternar menu"
            class="btn btn-square btn-ghost btn-sm"
        >
            <PanelLeft class="w-5 h-5" />
        </label>

        <!-- Breadcrumb -->
        <div class="flex-1 px-3">
            <AppBreadcrumb />
        </div>

        <!-- Usuário + Logout -->
        <div class="flex items-center gap-3">
            <span class="hidden text-sm text-base-content/70 sm:inline">
                {{ page.props.auth?.user?.name }}
            </span>
            <button class="btn btn-ghost btn-sm gap-2" @click="logout">
                <LogOut class="w-4 h-4" />
                <span class="hidden sm:inline">Sair</span>
            </button>
        </div>
    </nav>
</template>

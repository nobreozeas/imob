<script setup lang="ts">
import type { ImovelFoto } from '@/types/imovel';

defineProps<{ fotos: ImovelFoto[] }>();

function fotoPrincipal(fotos: ImovelFoto[]): ImovelFoto | null {
    return fotos.find(f => f.is_principal) ?? fotos[0] ?? null;
}

function fotosSecundarias(fotos: ImovelFoto[]): ImovelFoto[] {
    const principal = fotoPrincipal(fotos);
    return principal ? fotos.filter(f => f.id !== principal.id) : [];
}
</script>

<template>
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Fotos</h3>
            <div v-if="fotos.length > 0">
                <div class="mb-3">
                    <img :src="fotoPrincipal(fotos)!.url"
                         :alt="fotoPrincipal(fotos)!.nome_original"
                         class="w-full max-h-80 object-cover rounded-lg border border-base-200" />
                </div>
                <div v-if="fotosSecundarias(fotos).length > 0" class="grid grid-cols-3 md:grid-cols-4 gap-2">
                    <img v-for="foto in fotosSecundarias(fotos)" :key="foto.id"
                         :src="foto.url" :alt="foto.nome_original"
                         class="w-full h-24 object-cover rounded-md border border-base-200 cursor-pointer hover:opacity-80 transition-opacity" />
                </div>
            </div>
            <div v-else class="flex flex-col items-center py-8 text-base-content/40">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm">Nenhuma foto cadastrada</p>
            </div>
        </div>
    </div>
</template>

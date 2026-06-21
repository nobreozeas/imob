<script setup lang="ts">
import type { ImovelCaracteristicas } from '@/types/imovel';

defineProps<{ caracteristicas: ImovelCaracteristicas | null }>();

function formatarArea(val: string | null): string {
    if (!val || val === '0.00') return '—';
    return `${parseFloat(val).toLocaleString('pt-BR')} m²`;
}
</script>

<template>
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Características</h3>
            <div v-if="caracteristicas" class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-base-content/50">Área Total</p>
                    <p class="font-medium">{{ formatarArea(caracteristicas.area_total) }}</p>
                </div>
                <div>
                    <p class="text-base-content/50">Área Construída</p>
                    <p class="font-medium">{{ formatarArea(caracteristicas.area_construida) }}</p>
                </div>
                <div>
                    <p class="text-base-content/50">Quartos</p>
                    <p class="font-medium">{{ caracteristicas.quartos }}</p>
                </div>
                <div>
                    <p class="text-base-content/50">Suítes</p>
                    <p class="font-medium">{{ caracteristicas.suites }}</p>
                </div>
                <div>
                    <p class="text-base-content/50">Banheiros</p>
                    <p class="font-medium">{{ caracteristicas.banheiros }}</p>
                </div>
                <div>
                    <p class="text-base-content/50">Vagas de Garagem</p>
                    <p class="font-medium">{{ caracteristicas.vagas_garagem }}</p>
                </div>
                <div class="col-span-2 md:col-span-3">
                    <p class="text-base-content/50 mb-1">Diferenciais</p>
                    <div class="flex flex-wrap gap-2">
                        <span v-if="caracteristicas.mobiliado" class="badge badge-outline badge-sm">Mobiliado</span>
                        <span v-if="caracteristicas.aceita_pet" class="badge badge-outline badge-sm">Aceita Pet</span>
                        <span v-if="caracteristicas.possui_piscina" class="badge badge-outline badge-sm">Piscina</span>
                        <span v-if="caracteristicas.possui_quintal" class="badge badge-outline badge-sm">Quintal</span>
                        <span v-if="caracteristicas.possui_varanda" class="badge badge-outline badge-sm">Varanda</span>
                        <span v-if="!caracteristicas.mobiliado && !caracteristicas.aceita_pet && !caracteristicas.possui_piscina && !caracteristicas.possui_quintal && !caracteristicas.possui_varanda"
                              class="text-base-content/40 text-xs">Nenhum</span>
                    </div>
                </div>
                <div v-if="caracteristicas.outras_caracteristicas" class="col-span-2 md:col-span-3">
                    <p class="text-base-content/50">Outras Características</p>
                    <p class="font-medium whitespace-pre-line">{{ caracteristicas.outras_caracteristicas }}</p>
                </div>
            </div>
            <p v-else class="text-base-content/40 text-sm">Não informadas.</p>
        </div>
    </div>
</template>

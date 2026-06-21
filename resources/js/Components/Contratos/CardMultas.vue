<script setup lang="ts">
import type { ContratoMultas } from '@/types/contrato';

defineProps<{ multas: ContratoMultas | null | undefined }>();
</script>

<template>
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h3 class="card-title text-base">Multas</h3>
            <div v-if="!multas || (!multas.possui_multa_atraso && !multas.possui_multa_rescisao)" class="text-sm text-base-content/60">
                Nenhuma multa configurada.
            </div>
            <template v-else>
                <div v-if="multas.possui_multa_atraso" class="mb-3">
                    <p class="font-medium text-sm mb-1">Multa por Atraso</p>
                    <div class="grid grid-cols-2 gap-2 text-sm pl-2">
                        <div><span class="text-base-content/60">Percentual:</span> {{ multas.percentual_multa_atraso }}%</div>
                        <div><span class="text-base-content/60">Juros diários:</span> {{ multas.valor_juros_dia }}%</div>
                    </div>
                </div>
                <div v-if="multas.possui_multa_rescisao">
                    <p class="font-medium text-sm mb-1">Multa por Rescisão</p>
                    <div class="grid grid-cols-2 gap-2 text-sm pl-2">
                        <div><span class="text-base-content/60">Percentual:</span> {{ multas.percentual_multa_rescisao }}%</div>
                        <div>
                            <span class="text-base-content/60">Base:</span>
                            {{ multas.base_calculo_rescisao === 'alugueis_restantes' ? 'Aluguéis restantes' : 'Valor fixo' }}
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

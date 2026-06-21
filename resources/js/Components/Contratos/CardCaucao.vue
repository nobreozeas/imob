<script setup lang="ts">
import { useContratoStatus } from '@/composables/useContratoStatus';
import type { ContratoCaucao } from '@/types/contrato';

defineProps<{ caucao: ContratoCaucao | null | undefined }>();

const { labelTipoCaucao, labelStatusCaucao, corStatusCaucao } = useContratoStatus();

function moeda(valor: string | null): string {
    if (!valor) return '-';
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(valor));
}

function data(d: string | null): string {
    if (!d) return '-';
    return new Date(d + 'T00:00:00').toLocaleDateString('pt-BR');
}
</script>

<template>
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h3 class="card-title text-base">Caução</h3>
            <div v-if="!caucao || !caucao.possui_caucao" class="text-sm text-base-content/60">
                Contrato sem caução.
            </div>
            <template v-else>
                <div class="flex items-center gap-2 mb-2">
                    <span class="badge" :class="corStatusCaucao(caucao.status_caucao)">
                        {{ labelStatusCaucao(caucao.status_caucao) }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div><span class="font-medium">Tipo:</span> {{ labelTipoCaucao(caucao.tipo_caucao) }}</div>
                    <div><span class="font-medium">Valor:</span> {{ moeda(caucao.valor_caucao) }}</div>
                    <div><span class="font-medium">Recebida em:</span> {{ data(caucao.data_recebimento_caucao) }}</div>
                    <div v-if="caucao.valor_devolvido"><span class="font-medium">Devolvido:</span> {{ moeda(caucao.valor_devolvido) }}</div>
                    <div v-if="caucao.data_devolucao_caucao"><span class="font-medium">Data devolução:</span> {{ data(caucao.data_devolucao_caucao) }}</div>
                </div>
                <div v-if="caucao.observacao_caucao" class="text-sm text-base-content/70 mt-1">
                    {{ caucao.observacao_caucao }}
                </div>
            </template>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useContratoStatus } from '@/composables/useContratoStatus';
import type { ContratoHistorico } from '@/types/contrato';

defineProps<{ historicos: ContratoHistorico[] }>();

const { labelTipoEvento, iconeEvento } = useContratoStatus();

function formatarData(data: string): string {
    return new Date(data).toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' });
}
</script>

<template>
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h3 class="card-title text-base">Histórico</h3>
            <div v-if="historicos.length === 0" class="text-sm text-base-content/60">
                Nenhum evento registrado.
            </div>
            <ul v-else class="timeline timeline-vertical timeline-compact">
                <li v-for="(evento, idx) in historicos" :key="evento.id">
                    <div class="timeline-start text-xs text-base-content/50 text-right">
                        {{ formatarData(evento.created_at) }}
                    </div>
                    <div class="timeline-middle">
                        <span class="text-base">{{ iconeEvento(evento.tipo_evento) }}</span>
                    </div>
                    <div class="timeline-end timeline-box">
                        <p class="font-medium text-sm">{{ labelTipoEvento(evento.tipo_evento) }}</p>
                        <p class="text-xs text-base-content/70">{{ evento.descricao }}</p>
                        <p v-if="evento.usuario" class="text-xs text-base-content/50 mt-0.5">{{ evento.usuario.name }}</p>
                    </div>
                    <hr v-if="idx < historicos.length - 1" />
                </li>
            </ul>
        </div>
    </div>
</template>

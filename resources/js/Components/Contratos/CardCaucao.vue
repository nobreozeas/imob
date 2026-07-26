<script setup lang="ts">
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useContratoStatus } from '@/composables/useContratoStatus';
import type { ContratoCaucao } from '@/types/contrato';
import ModalMovimentacaoCaucao from '@/Components/Contratos/ModalMovimentacaoCaucao.vue';

const props = defineProps<{ contratoId: string; caucao: ContratoCaucao | null | undefined }>();

const { labelTipoCaucao, labelStatusCaucao, corStatusCaucao, labelTipoMovimentacaoCaucao } = useContratoStatus();

const page = usePage();
const auth = (page.props as any).auth;

const mostrarModalMovimentacao = ref(false);

function podeGerenciarCaucao(): boolean {
    return auth?.permissions?.includes('contratos.gerenciar-caucao');
}

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
            <div class="flex items-center justify-between">
                <h3 class="card-title text-base">Caução</h3>
                <button
                    v-if="caucao?.possui_caucao && podeGerenciarCaucao()"
                    class="btn btn-outline btn-xs"
                    @click="mostrarModalMovimentacao = true"
                >
                    Movimentar
                </button>
            </div>
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
                    <div><span class="font-medium">Saldo atual:</span> {{ moeda(caucao.saldo_atual) }}</div>
                    <div><span class="font-medium">Recebida em:</span> {{ data(caucao.data_recebimento_caucao) }}</div>
                </div>
                <div v-if="caucao.observacao_caucao" class="text-sm text-base-content/70 mt-1">
                    {{ caucao.observacao_caucao }}
                </div>
                <div v-if="caucao.movimentacoes?.length" class="mt-3">
                    <p class="text-xs uppercase tracking-wide text-base-content/50 mb-1">Movimentações</p>
                    <ul class="space-y-1 text-sm">
                        <li v-for="mov in caucao.movimentacoes" :key="mov.id" class="flex justify-between">
                            <span>{{ labelTipoMovimentacaoCaucao(mov.tipo_movimentacao) }} · {{ data(mov.data_movimentacao) }}</span>
                            <span class="font-medium">{{ moeda(mov.valor) }}</span>
                        </li>
                    </ul>
                </div>
            </template>
        </div>

        <ModalMovimentacaoCaucao
            v-if="mostrarModalMovimentacao"
            :contrato-id="contratoId"
            @fechado="mostrarModalMovimentacao = false"
        />
    </div>
</template>

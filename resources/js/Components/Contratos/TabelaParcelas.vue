<script setup lang="ts">
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import type { ParcelaAluguel, StatusParcelaAluguel } from '@/types/parcela';
import ModalRegistrarPagamento from '@/Components/Contratos/ModalRegistrarPagamento.vue';

const props = defineProps<{
    contratoId: string;
    parcelas: ParcelaAluguel[];
}>();

const page = usePage();
const auth = (page.props as any).auth;

const parcelaSelecionada = ref<ParcelaAluguel | null>(null);

function podeRegistrarPagamento(): boolean {
    return auth?.permissions?.includes('contratos.registrar-pagamento');
}

function moeda(val: string | null): string {
    if (!val) return '—';
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(val));
}

function referencia(p: ParcelaAluguel): string {
    return `${String(p.mes_referencia).padStart(2, '0')}/${p.ano_referencia}`;
}

function corStatus(status: StatusParcelaAluguel): string {
    const cores: Record<StatusParcelaAluguel, string> = {
        pendente: 'badge-ghost',
        pago: 'badge-success',
        vencido: 'badge-error',
        cancelado: 'badge-ghost',
        pago_parcial: 'badge-warning',
    };
    return cores[status] ?? 'badge-ghost';
}

function labelStatus(status: StatusParcelaAluguel): string {
    const labels: Record<StatusParcelaAluguel, string> = {
        pendente: 'Pendente',
        pago: 'Pago',
        vencido: 'Vencido',
        cancelado: 'Cancelado',
        pago_parcial: 'Pago Parcial',
    };
    return labels[status] ?? status;
}
</script>

<template>
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Parcelas</h3>
            <p v-if="!parcelas.length" class="text-sm text-base-content/40">Nenhuma parcela gerada.</p>
            <div v-else class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Referência</th>
                            <th>Vencimento</th>
                            <th>Total</th>
                            <th>Pago</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="parcela in parcelas" :key="parcela.id">
                            <td>{{ referencia(parcela) }}</td>
                            <td>{{ parcela.data_vencimento }}</td>
                            <td>{{ moeda(parcela.valor_total) }}</td>
                            <td>{{ moeda(parcela.valor_pago) }}</td>
                            <td><span class="badge badge-sm" :class="corStatus(parcela.status)">{{ labelStatus(parcela.status) }}</span></td>
                            <td>
                                <button
                                    v-if="['pendente', 'vencido', 'pago_parcial'].includes(parcela.status) && podeRegistrarPagamento()"
                                    class="btn btn-primary btn-xs"
                                    @click="parcelaSelecionada = parcela"
                                >
                                    Registrar Pagamento
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <ModalRegistrarPagamento
            v-if="parcelaSelecionada"
            :contrato-id="contratoId"
            :parcela="parcelaSelecionada"
            @fechado="parcelaSelecionada = null"
        />
    </div>
</template>

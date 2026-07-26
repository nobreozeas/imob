<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import StatusLancamentoBadge from '@/Components/Financeiro/StatusLancamentoBadge.vue';
import type { LancamentoFinanceiro } from '@/types/lancamentoFinanceiro';

defineOptions({ layout: AdminLayout });

interface Historico {
    id: string;
    acao: string;
    descricao: string | null;
    created_at: string;
    criador?: { name: string };
}

const props = defineProps<{
    lancamento: LancamentoFinanceiro & { historicos?: Historico[] };
}>();

function moeda(val: string | null): string {
    if (!val) return '—';
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(val));
}
</script>

<template>
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-bold text-base-content">Lançamento {{ lancamento.codigo }}</h1>
            <AppBreadcrumb />
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body">
                <div class="flex items-center gap-2 mb-4">
                    <span class="badge badge-sm" :class="lancamento.tipo === 'entrada' ? 'badge-success' : 'badge-error'">
                        {{ lancamento.tipo === 'entrada' ? 'Entrada' : 'Saída' }}
                    </span>
                    <StatusLancamentoBadge :status="lancamento.status" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div><span class="text-base-content/50">Categoria</span><p class="font-medium">{{ lancamento.categoria?.nome ?? '—' }}</p></div>
                    <div><span class="text-base-content/50">Descrição</span><p class="font-medium">{{ lancamento.descricao ?? '—' }}</p></div>
                    <div><span class="text-base-content/50">Valor</span><p class="font-medium">{{ moeda(lancamento.valor) }}</p></div>
                    <div><span class="text-base-content/50">Vencimento</span><p class="font-medium">{{ lancamento.data_vencimento ?? '—' }}</p></div>
                    <div><span class="text-base-content/50">Pagamento</span><p class="font-medium">{{ lancamento.data_pagamento ?? '—' }}</p></div>
                    <div><span class="text-base-content/50">Forma de pagamento</span><p class="font-medium">{{ lancamento.forma_pagamento ?? '—' }}</p></div>
                    <div><span class="text-base-content/50">Origem</span><p class="font-medium">{{ lancamento.origem }}</p></div>
                    <div v-if="lancamento.contrato"><span class="text-base-content/50">Contrato</span><p class="font-medium">{{ lancamento.contrato.numero }}</p></div>
                    <div v-if="lancamento.imovel"><span class="text-base-content/50">Imóvel</span><p class="font-medium">{{ lancamento.imovel.titulo }}</p></div>
                </div>
                <div v-if="lancamento.motivo_cancelamento" class="mt-4 text-sm">
                    <span class="text-base-content/50">Motivo do cancelamento</span>
                    <p class="font-medium">{{ lancamento.motivo_cancelamento }}</p>
                </div>
                <div v-if="lancamento.motivo_estorno" class="mt-4 text-sm">
                    <span class="text-base-content/50">Motivo do estorno</span>
                    <p class="font-medium">{{ lancamento.motivo_estorno }}</p>
                </div>
                <div v-if="lancamento.observacoes" class="mt-4 text-sm">
                    <span class="text-base-content/50">Observações</span>
                    <p class="font-medium">{{ lancamento.observacoes }}</p>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body">
                <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Histórico</h3>
                <p v-if="!lancamento.historicos?.length" class="text-sm text-base-content/40">Nenhum evento registrado.</p>
                <ul v-else class="space-y-2">
                    <li v-for="h in lancamento.historicos" :key="h.id" class="text-sm border-b border-base-200 pb-2">
                        <span class="font-medium">{{ h.acao }}</span>
                        <span v-if="h.descricao" class="text-base-content/60"> — {{ h.descricao }}</span>
                        <div class="text-xs text-base-content/40">{{ h.created_at }} · {{ h.criador?.name ?? 'Sistema' }}</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

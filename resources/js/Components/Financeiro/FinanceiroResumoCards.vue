<script setup lang="ts">
export interface ResumoFinanceiro {
    periodo: { data_inicio: string; data_fim: string };
    receitas: number;
    despesas: number;
    saldo: number;
    alugueis_recebidos: number;
    alugueis_em_aberto: number;
    alugueis_vencidos: number;
    repasses_pendentes: number;
    repasses_pagos: number;
    caucoes_recebidas: number;
}

defineProps<{ resumo: ResumoFinanceiro }>();

function moeda(val: number): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val);
}
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-4">
                <span class="text-xs uppercase text-base-content/50">Receitas do período</span>
                <span class="text-xl font-bold text-success">{{ moeda(resumo.receitas) }}</span>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-4">
                <span class="text-xs uppercase text-base-content/50">Despesas do período</span>
                <span class="text-xl font-bold text-error">{{ moeda(resumo.despesas) }}</span>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-4">
                <span class="text-xs uppercase text-base-content/50">Saldo do período</span>
                <span class="text-xl font-bold" :class="resumo.saldo >= 0 ? 'text-success' : 'text-error'">{{ moeda(resumo.saldo) }}</span>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-4">
                <span class="text-xs uppercase text-base-content/50">Aluguéis recebidos</span>
                <span class="text-xl font-bold">{{ moeda(resumo.alugueis_recebidos) }}</span>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-4">
                <span class="text-xs uppercase text-base-content/50">Aluguéis em aberto</span>
                <span class="text-xl font-bold text-warning">{{ moeda(resumo.alugueis_em_aberto) }}</span>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-4">
                <span class="text-xs uppercase text-base-content/50">Aluguéis vencidos</span>
                <span class="text-xl font-bold text-error">{{ moeda(resumo.alugueis_vencidos) }}</span>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-4">
                <span class="text-xs uppercase text-base-content/50">Repasses pendentes</span>
                <span class="text-xl font-bold text-warning">{{ moeda(resumo.repasses_pendentes) }}</span>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-4">
                <span class="text-xs uppercase text-base-content/50">Repasses pagos</span>
                <span class="text-xl font-bold">{{ moeda(resumo.repasses_pagos) }}</span>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body py-4">
                <span class="text-xs uppercase text-base-content/50">Cauções recebidas</span>
                <span class="text-xl font-bold">{{ moeda(resumo.caucoes_recebidas) }}</span>
            </div>
        </div>
    </div>
</template>

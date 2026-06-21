<script setup lang="ts">
import type { ClienteDadosInquilino } from '@/types/cliente';

defineProps<{ dados: ClienteDadosInquilino }>();

function formatarMoeda(valor: string | null): string {
    if (!valor) return '—';
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(valor));
}
</script>

<template>
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h3 class="card-title text-base">Dados de Inquilino</h3>

            <div class="divider my-1" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div v-if="dados.profissao">
                    <span class="text-base-content/60 block">Profissão</span>
                    <span>{{ dados.profissao }}</span>
                </div>
                <div v-if="dados.renda_mensal">
                    <span class="text-base-content/60 block">Renda Mensal Aproximada</span>
                    <span>{{ formatarMoeda(dados.renda_mensal) }}</span>
                </div>
                <div v-if="dados.local_trabalho" class="md:col-span-2">
                    <span class="text-base-content/60 block">Local de Trabalho</span>
                    <span>{{ dados.local_trabalho }}</span>
                </div>
                <div v-if="dados.telefone_comercial">
                    <span class="text-base-content/60 block">Telefone Comercial</span>
                    <span>{{ dados.telefone_comercial }}</span>
                </div>
                <div v-if="dados.contato_emergencia">
                    <span class="text-base-content/60 block">Contato de Emergência</span>
                    <span>{{ dados.contato_emergencia }}</span>
                </div>
                <div v-if="dados.observacoes_cadastrais" class="md:col-span-2">
                    <span class="text-base-content/60 block">Observações Cadastrais</span>
                    <span class="whitespace-pre-line">{{ dados.observacoes_cadastrais }}</span>
                </div>
                <div v-if="dados.restricoes" class="md:col-span-2">
                    <span class="text-base-content/60 block">Restrições</span>
                    <span class="whitespace-pre-line text-error">{{ dados.restricoes }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

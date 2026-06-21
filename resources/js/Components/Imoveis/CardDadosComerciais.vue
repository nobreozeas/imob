<script setup lang="ts">
import type { ImovelDadosComerciais } from '@/types/imovel';

defineProps<{ dadosComerciais: ImovelDadosComerciais | null }>();

function formatarMoeda(val: string | null): string {
    if (!val) return '—';
    return parseFloat(val).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function labelResponsavel(val: string | null): string {
    if (!val) return '—';
    return val === 'proprietario' ? 'Proprietário' : 'Inquilino';
}
</script>

<template>
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Dados Comerciais</h3>
            <div v-if="dadosComerciais" class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-base-content/50">Valor do Aluguel</p>
                    <p class="font-semibold text-success">{{ formatarMoeda(dadosComerciais.valor_aluguel) }}</p>
                </div>
                <div v-if="dadosComerciais.valor_venda">
                    <p class="text-base-content/50">Valor de Venda</p>
                    <p class="font-semibold text-info">{{ formatarMoeda(dadosComerciais.valor_venda) }}</p>
                </div>
                <div>
                    <p class="text-base-content/50">Condomínio</p>
                    <p class="font-medium">
                        {{ formatarMoeda(dadosComerciais.valor_condominio) }}
                        <span v-if="dadosComerciais.condominio_incluso" class="badge badge-success badge-xs ml-1">Incluso</span>
                    </p>
                </div>
                <div>
                    <p class="text-base-content/50">IPTU</p>
                    <p class="font-medium">{{ formatarMoeda(dadosComerciais.valor_iptu) }}</p>
                </div>
                <div>
                    <p class="text-base-content/50">Caução Sugerido</p>
                    <p class="font-medium">{{ formatarMoeda(dadosComerciais.valor_caucao_sugerido) }}</p>
                </div>
                <div class="col-span-2 md:col-span-3 border-t border-base-200 pt-3">
                    <p class="text-base-content/50 mb-2 text-xs uppercase tracking-wide">Responsabilidades</p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div><span class="text-base-content/40">IPTU: </span>{{ labelResponsavel(dadosComerciais.responsavel_iptu) }}</div>
                        <div><span class="text-base-content/40">Água: </span>{{ labelResponsavel(dadosComerciais.responsavel_agua) }}</div>
                        <div><span class="text-base-content/40">Energia: </span>{{ labelResponsavel(dadosComerciais.responsavel_energia) }}</div>
                        <div><span class="text-base-content/40">Condomínio: </span>{{ labelResponsavel(dadosComerciais.responsavel_condominio) }}</div>
                    </div>
                </div>
                <div v-if="dadosComerciais.observacoes_comerciais" class="col-span-2 md:col-span-3">
                    <p class="text-base-content/50">Observações Comerciais</p>
                    <p class="font-medium whitespace-pre-line">{{ dadosComerciais.observacoes_comerciais }}</p>
                </div>
            </div>
            <p v-else class="text-base-content/40 text-sm">Não informados.</p>
        </div>
    </div>
</template>

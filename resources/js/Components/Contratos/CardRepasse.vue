<script setup lang="ts">
import type { ContratoLocacao } from '@/types/contrato';

const props = defineProps<{
    contrato: Pick<ContratoLocacao, 'tipo_taxa_administracao' | 'valor_taxa_administracao' | 'valor_aluguel' | 'dia_repasse' | 'forma_repasse' | 'banco' | 'agencia' | 'conta' | 'tipo_conta' | 'pix_key'>;
}>();

function moeda(valor: string | null | undefined): string {
    if (!valor) return '-';
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(valor));
}

function calcularRepasse(): string {
    const aluguel = Number(props.contrato.valor_aluguel);
    const taxa = Number(props.contrato.valor_taxa_administracao);
    const repasse = props.contrato.tipo_taxa_administracao === 'percentual'
        ? aluguel - (aluguel * taxa / 100)
        : aluguel - taxa;
    return moeda(String(repasse));
}

function labelFormaRepasse(forma: string): string {
    const map: Record<string, string> = { pix: 'PIX', transferencia: 'Transferência', dinheiro: 'Dinheiro' };
    return map[forma] ?? forma;
}
</script>

<template>
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h3 class="card-title text-base">Repasse ao Proprietário</h3>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div>
                    <span class="text-base-content/60">Taxa administração:</span>
                    {{ contrato.tipo_taxa_administracao === 'percentual'
                        ? `${contrato.valor_taxa_administracao}%`
                        : moeda(contrato.valor_taxa_administracao) }}
                </div>
                <div><span class="text-base-content/60">Valor repasse (estimado):</span> {{ calcularRepasse() }}</div>
                <div v-if="contrato.dia_repasse"><span class="text-base-content/60">Dia do repasse:</span> Dia {{ contrato.dia_repasse }}</div>
                <div><span class="text-base-content/60">Forma:</span> {{ labelFormaRepasse(contrato.forma_repasse) }}</div>
            </div>
            <div v-if="contrato.pix_key || contrato.banco" class="mt-2 pt-2 border-t border-base-200">
                <p class="text-xs font-medium text-base-content/60 mb-1">Dados bancários</p>
                <div class="grid grid-cols-2 gap-1 text-sm">
                    <div v-if="contrato.banco"><span class="text-base-content/60">Banco:</span> {{ contrato.banco }}</div>
                    <div v-if="contrato.agencia"><span class="text-base-content/60">Agência:</span> {{ contrato.agencia }}</div>
                    <div v-if="contrato.conta"><span class="text-base-content/60">Conta:</span> {{ contrato.conta }}</div>
                    <div v-if="contrato.pix_key"><span class="text-base-content/60">PIX:</span> {{ contrato.pix_key }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

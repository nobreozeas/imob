<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface ParcelaInadimplente {
    id: string;
    contrato_id: string;
    mes_referencia: number;
    ano_referencia: number;
    data_vencimento: string;
    valor_total: string;
    valor_pago: string;
    contrato?: {
        numero: string;
        inquilino?: { nome: string | null; razao_social: string | null };
        proprietario?: { nome: string | null; razao_social: string | null };
        imovel?: { codigo: string; titulo: string };
    };
}

const props = defineProps<{ parcelas: ParcelaInadimplente[] }>();

function moeda(val: string): string {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(val));
}

function nome(c: { nome: string | null; razao_social: string | null } | undefined): string {
    if (!c) return '—';
    return c.nome ?? c.razao_social ?? '—';
}

function diasAtraso(dataVencimento: string): number {
    const hoje = new Date();
    const vencimento = new Date(dataVencimento);
    return Math.max(0, Math.floor((hoje.getTime() - vencimento.getTime()) / (1000 * 60 * 60 * 24)));
}
</script>

<template>
    <div class="overflow-x-auto">
        <table class="table table-sm">
            <thead>
                <tr class="bg-base-200/50">
                    <th>Contrato</th>
                    <th>Inquilino</th>
                    <th>Imóvel</th>
                    <th>Referência</th>
                    <th>Vencimento</th>
                    <th>Dias em atraso</th>
                    <th>Valor devido</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="parcela in props.parcelas" :key="parcela.id" class="hover">
                    <td class="font-mono text-xs">{{ parcela.contrato?.numero ?? '—' }}</td>
                    <td class="text-sm">{{ nome(parcela.contrato?.inquilino) }}</td>
                    <td class="text-sm">{{ parcela.contrato?.imovel?.titulo ?? '—' }}</td>
                    <td class="text-sm">{{ String(parcela.mes_referencia).padStart(2, '0') }}/{{ parcela.ano_referencia }}</td>
                    <td class="text-sm">{{ parcela.data_vencimento }}</td>
                    <td class="text-sm"><span class="badge badge-error badge-sm">{{ diasAtraso(parcela.data_vencimento) }} dias</span></td>
                    <td class="text-sm font-medium">{{ moeda((Number(parcela.valor_total) - Number(parcela.valor_pago)).toFixed(2)) }}</td>
                    <td>
                        <Link :href="route('contratos.show', { contrato: parcela.contrato_id, tab: 'parcelas' })" class="btn btn-ghost btn-xs">Ver contrato</Link>
                    </td>
                </tr>
                <tr v-if="props.parcelas.length === 0">
                    <td colspan="8" class="text-center py-8 text-base-content/40">Nenhuma parcela inadimplente.</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import type { ClienteDadosProprietario } from '@/types/cliente';

defineProps<{ dados: ClienteDadosProprietario }>();

const labelChavePix: Record<string, string> = {
    cpf: 'CPF',
    cnpj: 'CNPJ',
    email: 'E-mail',
    telefone: 'Telefone',
    aleatoria: 'Chave Aleatória',
};
</script>

<template>
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h3 class="card-title text-base">Dados de Proprietário</h3>

            <div class="divider my-1" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div v-if="dados.banco">
                    <span class="text-base-content/60 block">Banco</span>
                    <span>{{ dados.banco }}</span>
                </div>
                <div v-if="dados.agencia">
                    <span class="text-base-content/60 block">Agência</span>
                    <span>{{ dados.agencia }}</span>
                </div>
                <div v-if="dados.conta">
                    <span class="text-base-content/60 block">Conta</span>
                    <span>{{ dados.conta }} <span v-if="dados.tipo_conta">({{ dados.tipo_conta }})</span></span>
                </div>
                <div v-if="dados.chave_pix">
                    <span class="text-base-content/60 block">Chave PIX</span>
                    <span>{{ dados.chave_pix }}
                        <span v-if="dados.tipo_chave_pix" class="text-base-content/60">
                            ({{ labelChavePix[dados.tipo_chave_pix] }})
                        </span>
                    </span>
                </div>
                <div v-if="dados.percentual_administracao">
                    <span class="text-base-content/60 block">% Administração</span>
                    <span>{{ dados.percentual_administracao }}%</span>
                </div>
                <div>
                    <span class="text-base-content/60 block">Emite Nota Fiscal</span>
                    <span>{{ dados.emite_nota_fiscal ? 'Sim' : 'Não' }}</span>
                </div>
                <div v-if="dados.preferencia_recebimento">
                    <span class="text-base-content/60 block">Preferência de Recebimento</span>
                    <span>{{ dados.preferencia_recebimento }}</span>
                </div>
                <div v-if="dados.observacoes_repasse" class="md:col-span-2">
                    <span class="text-base-content/60 block">Observações de Repasse</span>
                    <span class="whitespace-pre-line">{{ dados.observacoes_repasse }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import type { FormularioClienteData } from '@/types/cliente';
import WizardStep1DadosPrincipais from './WizardStep1DadosPrincipais.vue';
import WizardStep2Papeis from './WizardStep2Papeis.vue';
import WizardStep3Contatos from './WizardStep3Contatos.vue';
import WizardStep4Endereco from './WizardStep4Endereco.vue';
import WizardStep5DadosAdicionais from './WizardStep5DadosAdicionais.vue';

const props = defineProps<{
    form: FormularioClienteData;
    errors: Partial<Record<string, string>>;
    processing?: boolean;
}>();

const emit = defineEmits<{ submit: [] }>();

const TOTAL_ETAPAS = 5;
const etapaAtual = ref(1);

const ETAPAS = [
    { numero: 1, label: 'Dados' },
    { numero: 2, label: 'Papéis' },
    { numero: 3, label: 'Contatos' },
    { numero: 4, label: 'Endereço' },
    { numero: 5, label: 'Finalizar' },
];

const CAMPO_PARA_ETAPA: Record<string, number> = {
    tipo_pessoa: 1, nome: 1, razao_social: 1, nome_fantasia: 1,
    cpf: 1, cnpj: 1, rg: 1, data_nascimento: 1, observacoes: 1,
    papeis: 2,
    telefone_principal: 3, whatsapp: 3, telefone_secundario: 3,
    email_principal: 3, email_alternativo: 3,
    cep: 4, logradouro: 4, numero: 4, complemento: 4,
    bairro: 4, cidade: 4, estado: 4, ponto_referencia: 4,
};

watch(() => props.errors, (erros) => {
    if (!erros || Object.keys(erros).length === 0) return;
    for (const campo of Object.keys(erros)) {
        const chaveBase = campo.split('.')[0];
        etapaAtual.value = CAMPO_PARA_ETAPA[chaveBase] ?? 5;
        break;
    }
}, { deep: true });

function avancar() {
    if (etapaAtual.value < TOTAL_ETAPAS) etapaAtual.value++;
}

function voltar() {
    if (etapaAtual.value > 1) etapaAtual.value--;
}
</script>

<template>
    <div class="space-y-6">
        <!-- Barra de progresso -->
        <ul class="steps steps-horizontal w-full">
            <li
                v-for="etapa in ETAPAS"
                :key="etapa.numero"
                class="step text-xs"
                :class="{
                    'step-primary': etapa.numero <= etapaAtual,
                    'cursor-pointer': etapa.numero < etapaAtual,
                }"
                @click="etapa.numero < etapaAtual ? (etapaAtual = etapa.numero) : null"
            >
                {{ etapa.label }}
            </li>
        </ul>

        <div class="text-sm text-base-content/50 text-right">
            Etapa {{ etapaAtual }} de {{ TOTAL_ETAPAS }}
        </div>

        <WizardStep1DadosPrincipais
            v-if="etapaAtual === 1"
            :form="form"
            :errors="errors"
            @next="avancar"
        />
        <WizardStep2Papeis
            v-else-if="etapaAtual === 2"
            :form="form"
            :errors="errors"
            @next="avancar"
        />
        <WizardStep3Contatos
            v-else-if="etapaAtual === 3"
            :form="form"
            :errors="errors"
            @next="avancar"
        />
        <WizardStep4Endereco
            v-else-if="etapaAtual === 4"
            :form="form"
            :errors="errors"
            @next="avancar"
        />
        <WizardStep5DadosAdicionais
            v-else-if="etapaAtual === 5"
            :form="form"
            :errors="errors"
            :processing="processing"
            @submit="$emit('submit')"
        />

        <div v-if="etapaAtual > 1" class="flex justify-start -mt-2">
            <button type="button" class="btn btn-ghost btn-sm" @click="voltar">
                ← Anterior
            </button>
        </div>
    </div>
</template>

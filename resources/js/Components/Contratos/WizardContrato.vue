<script setup lang="ts">
import { ref, watch } from 'vue';
import type { InertiaForm } from '@inertiajs/vue3';
import type { FormularioContratoData, ImovelOpcao, InquilinoOpcao, CorretorOpcao } from '@/types/contrato';
import WizardStep1ImovelPartes from './WizardStep1ImovelPartes.vue';
import WizardStep2DadosLocacao from './WizardStep2DadosLocacao.vue';
import WizardStep3Valores from './WizardStep3Valores.vue';
import WizardStep4Encargos from './WizardStep4Encargos.vue';
import WizardStep5Caucao from './WizardStep5Caucao.vue';
import WizardStep6Multas from './WizardStep6Multas.vue';
import WizardStep7Repasse from './WizardStep7Repasse.vue';
import WizardStep8Documentos from './WizardStep8Documentos.vue';
import WizardStep9Revisao from './WizardStep9Revisao.vue';

const props = defineProps<{
    form: InertiaForm<FormularioContratoData>;
    imoveis: ImovelOpcao[];
    inquilinos: InquilinoOpcao[];
    corretores: CorretorOpcao[];
    documentosExistentes?: { id: string; nome_original: string; tipo: string; url: string }[];
}>();

const emit = defineEmits<{ submit: [] }>();

const CAMPO_PARA_ETAPA: Record<string, number> = {
    imovel_id: 1, proprietario_id: 1, inquilino_id: 1, corretor_id: 1,
    data_inicio: 2, data_fim: 2, dia_vencimento: 2, duracao_meses: 2, tipo_contrato: 2, objetivo_contrato: 2,
    valor_aluguel: 3, indice_reajuste: 3, periodicidade_reajuste: 3, data_primeiro_reajuste: 3,
    'encargos.0.responsavel': 4, 'encargos.1.responsavel': 4,
    'caucao.possui_caucao': 5, 'caucao.tipo_caucao': 5, 'caucao.valor_caucao': 5,
    'multas.possui_multa_atraso': 6, 'multas.percentual_multa_atraso': 6, 'multas.possui_multa_rescisao': 6,
    tipo_taxa_administracao: 7, valor_taxa_administracao: 7, dia_repasse: 7, forma_repasse: 7, banco: 7, pix_key: 7,
    documentos_novos: 8, tipos_documentos: 8,
};

const ETAPAS = [
    { numero: 1, label: 'Imóvel' },
    { numero: 2, label: 'Locação' },
    { numero: 3, label: 'Valores' },
    { numero: 4, label: 'Encargos' },
    { numero: 5, label: 'Caução' },
    { numero: 6, label: 'Multas' },
    { numero: 7, label: 'Repasse' },
    { numero: 8, label: 'Docs' },
    { numero: 9, label: 'Revisão' },
];

const etapaAtual = ref(1);
const totalEtapas = ETAPAS.length;

watch(() => props.form.errors, (erros) => {
    const campoComErro = Object.keys(erros)[0];
    if (!campoComErro) return;
    const etapa = CAMPO_PARA_ETAPA[campoComErro];
    if (etapa) etapaAtual.value = etapa;
}, { deep: true });

function irParaEtapa(n: number) {
    etapaAtual.value = n;
}
</script>

<template>
    <div class="space-y-6">
        <ul class="steps steps-horizontal w-full">
            <li
                v-for="etapa in ETAPAS"
                :key="etapa.numero"
                class="step text-xs"
                :class="{
                    'step-primary': etapa.numero <= etapaAtual,
                    'cursor-pointer': etapa.numero < etapaAtual,
                }"
                @click="etapa.numero < etapaAtual ? irParaEtapa(etapa.numero) : null"
            >
                {{ etapa.label }}
            </li>
        </ul>

        <div class="text-sm text-base-content/50 text-right">
            Etapa {{ etapaAtual }} de {{ totalEtapas }}
        </div>

        <WizardStep1ImovelPartes
            v-if="etapaAtual === 1"
            :form="form"
            :imoveis="imoveis"
            :inquilinos="inquilinos"
            :corretores="corretores"
            @next="etapaAtual = 2"
        />
        <WizardStep2DadosLocacao v-else-if="etapaAtual === 2" :form="form" @prev="etapaAtual = 1" @next="etapaAtual = 3" />
        <WizardStep3Valores      v-else-if="etapaAtual === 3" :form="form" @prev="etapaAtual = 2" @next="etapaAtual = 4" />
        <WizardStep4Encargos     v-else-if="etapaAtual === 4" :form="form" @prev="etapaAtual = 3" @next="etapaAtual = 5" />
        <WizardStep5Caucao       v-else-if="etapaAtual === 5" :form="form" @prev="etapaAtual = 4" @next="etapaAtual = 6" />
        <WizardStep6Multas       v-else-if="etapaAtual === 6" :form="form" @prev="etapaAtual = 5" @next="etapaAtual = 7" />
        <WizardStep7Repasse      v-else-if="etapaAtual === 7" :form="form" @prev="etapaAtual = 6" @next="etapaAtual = 8" />
        <WizardStep8Documentos
            v-else-if="etapaAtual === 8"
            :form="form"
            :documentos-existentes="documentosExistentes"
            @prev="etapaAtual = 7"
            @next="etapaAtual = 9"
        />
        <WizardStep9Revisao
            v-else-if="etapaAtual === 9"
            :form="form"
            :imoveis="imoveis"
            :inquilinos="inquilinos"
            @prev="etapaAtual = 8"
            @submit="(acao) => { form.acao = acao; emit('submit'); }"
        />
    </div>
</template>

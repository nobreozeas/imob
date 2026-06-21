<script setup lang="ts">
import { ref, watch } from 'vue';
import type { FormularioImovelData, ProprietarioOpcao, CorretorOpcao } from '@/types/imovel';
import WizardStep1DadosPrincipais from './WizardStep1DadosPrincipais.vue';
import WizardStep2Endereco from './WizardStep2Endereco.vue';
import WizardStep3Caracteristicas from './WizardStep3Caracteristicas.vue';
import WizardStep4DadosComerciais from './WizardStep4DadosComerciais.vue';
import WizardStep5FotosDocumentos from './WizardStep5FotosDocumentos.vue';

const props = defineProps<{
    form: FormularioImovelData;
    errors: Partial<Record<string, string>>;
    processing?: boolean;
    proprietarios: ProprietarioOpcao[];
    corretores: CorretorOpcao[];
    fotosExistentes?: import('@/types/imovel').ImovelFoto[];
    documentosExistentes?: import('@/types/imovel').ImovelDocumento[];
}>();

const emit = defineEmits<{ submit: [] }>();

const etapaAtual = ref(1);
const totalEtapas = 5;

const ETAPAS = [
    'Dados Principais',
    'Endereço',
    'Características',
    'Dados Comerciais',
    'Fotos e Documentos',
];

const CAMPO_PARA_ETAPA: Record<string, number> = {
    titulo: 1, codigo: 1, tipo: 1, finalidade: 1, status: 1,
    proprietario_id: 1, corretor_id: 1, descricao: 1,
    cep: 2, logradouro: 2, numero: 2, complemento: 2,
    bairro: 2, cidade: 2, estado: 2, ponto_referencia: 2,
    'caracteristicas.area_total': 3, 'caracteristicas.area_construida': 3,
    'caracteristicas.quartos': 3, 'caracteristicas.suites': 3,
    'caracteristicas.banheiros': 3, 'caracteristicas.vagas_garagem': 3,
    'caracteristicas.mobiliado': 3, 'caracteristicas.aceita_pet': 3,
    'caracteristicas.possui_piscina': 3, 'caracteristicas.possui_quintal': 3,
    'caracteristicas.possui_varanda': 3, 'caracteristicas.outras_caracteristicas': 3,
    'dados_comerciais.valor_aluguel': 4, 'dados_comerciais.valor_venda': 4,
    'dados_comerciais.valor_condominio': 4, 'dados_comerciais.valor_iptu': 4,
    'dados_comerciais.condominio_incluso': 4, 'dados_comerciais.responsavel_iptu': 4,
    'dados_comerciais.responsavel_agua': 4, 'dados_comerciais.responsavel_energia': 4,
    'dados_comerciais.responsavel_condominio': 4, 'dados_comerciais.valor_caucao_sugerido': 4,
    'dados_comerciais.observacoes_comerciais': 4,
    'fotos_novas': 5, 'documentos_novos': 5, 'foto_principal_id': 5,
};

watch(() => props.errors, (erros) => {
    const campos = Object.keys(erros);
    if (campos.length === 0) return;
    const etapas = campos.map(c => CAMPO_PARA_ETAPA[c]).filter(Boolean);
    if (etapas.length > 0) {
        etapaAtual.value = Math.min(...etapas);
    }
}, { deep: true });

function irParaEtapa(n: number) {
    if (n >= 1 && n <= totalEtapas) etapaAtual.value = n;
}
</script>

<template>
    <div class="space-y-6">
        <!-- Progress steps -->
        <ul class="steps steps-horizontal w-full">
            <li v-for="(nome, i) in ETAPAS" :key="i"
                class="step cursor-pointer text-xs"
                :class="{ 'step-primary': etapaAtual >= i + 1 }"
                @click="irParaEtapa(i + 1)">
                {{ nome }}
            </li>
        </ul>

        <!-- Step content -->
        <WizardStep1DadosPrincipais
            v-if="etapaAtual === 1"
            :form="form"
            :errors="errors"
            :proprietarios="proprietarios"
            :corretores="corretores"
            @next="etapaAtual = 2"
        />
        <WizardStep2Endereco
            v-else-if="etapaAtual === 2"
            :form="form"
            :errors="errors"
            @prev="etapaAtual = 1"
            @next="etapaAtual = 3"
        />
        <WizardStep3Caracteristicas
            v-else-if="etapaAtual === 3"
            :form="form"
            :errors="errors"
            @prev="etapaAtual = 2"
            @next="etapaAtual = 4"
        />
        <WizardStep4DadosComerciais
            v-else-if="etapaAtual === 4"
            :form="form"
            :errors="errors"
            @prev="etapaAtual = 3"
            @next="etapaAtual = 5"
        />
        <WizardStep5FotosDocumentos
            v-else-if="etapaAtual === 5"
            :form="form"
            :errors="errors"
            :processing="processing"
            :fotos-existentes="fotosExistentes ?? []"
            :documentos-existentes="documentosExistentes ?? []"
            @prev="etapaAtual = 4"
            @submit="emit('submit')"
        />
    </div>
</template>

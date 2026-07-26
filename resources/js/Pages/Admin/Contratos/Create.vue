<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import WizardContrato from '@/Components/Contratos/WizardContrato.vue';
import type { FormularioContratoData, ImovelOpcao, InquilinoOpcao, CorretorOpcao } from '@/types/contrato';

defineOptions({ layout: AdminLayout });

defineProps<{
    imoveis: ImovelOpcao[];
    inquilinos: InquilinoOpcao[];
    corretores: CorretorOpcao[];
}>();

const form = useForm<FormularioContratoData>({
    numero: '',
    tipo_contrato: 'residencial',
    objetivo_contrato: 'aluguel',
    acao: 'rascunho',
    imovel_id: '',
    proprietario_id: '',
    inquilino_id: '',
    corretor_id: '',
    data_inicio: '',
    data_fim: '',
    dia_vencimento: '',
    duracao_meses: '',
    valor_aluguel: '',
    indice_reajuste: 'igpm',
    periodicidade_reajuste: '12',
    data_primeiro_reajuste: '',
    tipo_taxa_administracao: 'percentual',
    valor_taxa_administracao: '0',
    gerar_parcelas_automaticamente: true,
    quantidade_parcelas: '',
    dia_repasse: '',
    forma_repasse: 'pix',
    banco: '',
    agencia: '',
    conta: '',
    tipo_conta: '',
    pix_key: '',
    observacoes: '',
    encargos: [],
    caucao: {
        possui_caucao: false,
        tipo_caucao: '',
        valor_caucao: '',
        data_recebimento_caucao: '',
    },
    multas: {
        possui_multa_atraso: false,
        percentual_multa_atraso: '',
        valor_juros_dia: '',
        dias_tolerancia_atraso: '',
        possui_multa_rescisao: false,
        percentual_multa_rescisao: '',
        base_calculo_rescisao: '',
    },
    documentos_novos: [],
    tipos_documentos: [],
});

function submit() {
    form.post(route('contratos.store'), { forceFormData: true });
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Novo Contrato</h1>
                <AppBreadcrumb />
            </div>
            <Link :href="route('contratos.index')" class="btn btn-ghost btn-sm">Cancelar</Link>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body">
                <WizardContrato
                    :form="form"
                    :imoveis="imoveis"
                    :inquilinos="inquilinos"
                    :corretores="corretores"
                    @submit="submit"
                />
            </div>
        </div>
    </div>
</template>

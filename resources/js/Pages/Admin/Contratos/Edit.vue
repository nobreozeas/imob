<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import WizardContrato from '@/Components/Contratos/WizardContrato.vue';
import type { ContratoLocacao, FormularioContratoData, ImovelOpcao, InquilinoOpcao, CorretorOpcao } from '@/types/contrato';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    contrato: ContratoLocacao;
    imoveis: ImovelOpcao[];
    inquilinos: InquilinoOpcao[];
    corretores: CorretorOpcao[];
}>();

const c = props.contrato;

const form = useForm<FormularioContratoData>({
    numero: c.numero ?? '',
    tipo_contrato: c.tipo_contrato ?? 'residencial',
    objetivo_contrato: c.objetivo_contrato ?? 'aluguel',
    acao: 'rascunho',
    imovel_id: c.imovel_id ?? '',
    proprietario_id: c.proprietario_id ?? '',
    inquilino_id: c.inquilino_id ?? '',
    corretor_id: c.corretor_id ?? '',
    data_inicio: c.data_inicio ? String(c.data_inicio).substring(0, 10) : '',
    data_fim: c.data_fim ? String(c.data_fim).substring(0, 10) : '',
    dia_vencimento: String(c.dia_vencimento ?? ''),
    duracao_meses: String(c.duracao_meses ?? ''),
    valor_aluguel: String(c.valor_aluguel ?? ''),
    indice_reajuste: c.indice_reajuste ?? 'igpm',
    periodicidade_reajuste: String(c.periodicidade_reajuste ?? '12'),
    data_primeiro_reajuste: c.data_primeiro_reajuste ? String(c.data_primeiro_reajuste).substring(0, 10) : '',
    tipo_taxa_administracao: c.tipo_taxa_administracao ?? 'percentual',
    valor_taxa_administracao: String(c.valor_taxa_administracao ?? '0'),
    gerar_parcelas_automaticamente: c.gerar_parcelas_automaticamente ?? true,
    quantidade_parcelas: String(c.quantidade_parcelas ?? ''),
    dia_repasse: String(c.dia_repasse ?? ''),
    forma_repasse: c.forma_repasse ?? 'pix',
    banco: c.banco ?? '',
    agencia: c.agencia ?? '',
    conta: c.conta ?? '',
    tipo_conta: c.tipo_conta ?? '',
    pix_key: c.pix_key ?? '',
    observacoes: c.observacoes ?? '',
    encargos: (c.encargos ?? []).map(e => ({
        tipo_encargo: e.tipo_encargo,
        responsavel: e.responsavel,
        valor_estimado: String(e.valor_estimado ?? ''),
        cobrar_junto_aluguel: e.cobrar_junto_aluguel ?? false,
        observacao: e.observacao ?? '',
    })),
    caucao: {
        possui_caucao: c.caucao?.possui_caucao ?? false,
        tipo_caucao: c.caucao?.tipo_caucao ?? '',
        valor_caucao: String(c.caucao?.valor_caucao ?? ''),
        data_recebimento_caucao: c.caucao?.data_recebimento_caucao
            ? String(c.caucao.data_recebimento_caucao).substring(0, 10)
            : '',
    },
    multas: {
        possui_multa_atraso: c.multas?.possui_multa_atraso ?? false,
        percentual_multa_atraso: String(c.multas?.percentual_multa_atraso ?? ''),
        valor_juros_dia: String(c.multas?.valor_juros_dia ?? ''),
        dias_tolerancia_atraso: String(c.multas?.dias_tolerancia_atraso ?? ''),
        possui_multa_rescisao: c.multas?.possui_multa_rescisao ?? false,
        percentual_multa_rescisao: String(c.multas?.percentual_multa_rescisao ?? ''),
        base_calculo_rescisao: c.multas?.base_calculo_rescisao ?? '',
    },
    documentos_novos: [],
    tipos_documentos: [],
});

function submit() {
    form.put(route('contratos.update', c.id), { forceFormData: true });
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Editar Contrato {{ contrato.numero }}</h1>
                <AppBreadcrumb />
            </div>
            <Link :href="route('contratos.show', contrato.id)" class="btn btn-ghost btn-sm">Cancelar</Link>
        </div>

        <div v-if="!contrato.permite_edicao_completa" class="alert alert-warning">
            <span>Contrato em status <strong>{{ contrato.status }}</strong> — apenas campos informativos podem ser editados.</span>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body">
                <WizardContrato
                    :form="form"
                    :imoveis="imoveis"
                    :inquilinos="inquilinos"
                    :corretores="corretores"
                    :documentos-existentes="contrato.documentos"
                    @submit="submit"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import WizardImovel from '@/Components/Imoveis/WizardImovel.vue';
import type { Imovel, FormularioImovelData, ProprietarioOpcao, CorretorOpcao } from '@/types/imovel';

defineOptions({ layout: AdminLayout });

const props = defineProps<{
    imovel: Imovel;
    proprietarios: ProprietarioOpcao[];
    corretores: CorretorOpcao[];
}>();

const c = props.imovel.caracteristicas;
const d = props.imovel.dados_comerciais;

const form = useForm<FormularioImovelData>({
    titulo: props.imovel.titulo,
    codigo: props.imovel.codigo,
    tipo: props.imovel.tipo,
    finalidade: props.imovel.finalidade,
    status: props.imovel.status,
    proprietario_id: props.imovel.proprietario_id,
    corretor_id: props.imovel.corretor_id ?? '',
    descricao: props.imovel.descricao ?? '',
    cep: props.imovel.cep ?? '',
    logradouro: props.imovel.logradouro ?? '',
    numero: props.imovel.numero ?? '',
    complemento: props.imovel.complemento ?? '',
    bairro: props.imovel.bairro ?? '',
    cidade: props.imovel.cidade ?? '',
    estado: props.imovel.estado ?? '',
    ponto_referencia: props.imovel.ponto_referencia ?? '',
    caracteristicas: {
        area_total: c?.area_total ?? '',
        area_construida: c?.area_construida ?? '',
        quartos: c?.quartos ?? 0,
        suites: c?.suites ?? 0,
        banheiros: c?.banheiros ?? 0,
        vagas_garagem: c?.vagas_garagem ?? 0,
        mobiliado: c?.mobiliado ?? false,
        aceita_pet: c?.aceita_pet ?? false,
        possui_piscina: c?.possui_piscina ?? false,
        possui_quintal: c?.possui_quintal ?? false,
        possui_varanda: c?.possui_varanda ?? false,
        outras_caracteristicas: c?.outras_caracteristicas ?? '',
    },
    dados_comerciais: {
        valor_aluguel: d?.valor_aluguel ?? '',
        valor_venda: d?.valor_venda ?? '',
        valor_condominio: d?.valor_condominio ?? '',
        valor_iptu: d?.valor_iptu ?? '',
        condominio_incluso: d?.condominio_incluso ?? false,
        responsavel_iptu: d?.responsavel_iptu ?? '',
        responsavel_agua: d?.responsavel_agua ?? '',
        responsavel_energia: d?.responsavel_energia ?? '',
        responsavel_condominio: d?.responsavel_condominio ?? '',
        valor_caucao_sugerido: d?.valor_caucao_sugerido ?? '',
        observacoes_comerciais: d?.observacoes_comerciais ?? '',
    },
    fotos_novas: [],
    foto_principal_id: props.imovel.fotos.find(f => f.is_principal)?.id ?? '',
    fotos_remover: [],
    documentos_novos: [],
    tipos_documentos: [],
    documentos_remover: [],
});

function submit() {
    form.put(route('imoveis.update', props.imovel.id), { forceFormData: true });
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Editar Imóvel</h1>
                <AppBreadcrumb />
            </div>
            <Link :href="route('imoveis.show', imovel.id)" class="btn btn-ghost btn-sm">Cancelar</Link>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body">
                <WizardImovel
                    :form="form"
                    :errors="form.errors"
                    :processing="form.processing"
                    :proprietarios="proprietarios"
                    :corretores="corretores"
                    :fotos-existentes="imovel.fotos"
                    :documentos-existentes="imovel.documentos"
                    @submit="submit"
                />
            </div>
        </div>
    </div>
</template>

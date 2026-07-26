<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import WizardImovel from '@/Components/Imoveis/WizardImovel.vue';
import type { FormularioImovelData, ProprietarioOpcao, CorretorOpcao } from '@/types/imovel';

defineOptions({ layout: AdminLayout });

defineProps<{
    proprietarios: ProprietarioOpcao[];
    corretores: CorretorOpcao[];
}>();

const form = useForm<FormularioImovelData>({
    titulo: '',
    codigo: '',
    tipo: 'apartamento',
    finalidade: 'aluguel',
    status: 'disponivel',
    proprietario_id: '',
    corretor_id: '',
    descricao: '',
    cep: '',
    logradouro: '',
    numero: '',
    complemento: '',
    bairro: '',
    cidade: '',
    estado: '',
    ponto_referencia: '',
    caracteristicas: {
        area_total: '',
        area_construida: '',
        quartos: 0,
        suites: 0,
        banheiros: 0,
        vagas_garagem: 0,
        mobiliado: false,
        aceita_pet: false,
        possui_piscina: false,
        possui_quintal: false,
        possui_varanda: false,
        outras_caracteristicas: '',
    },
    dados_comerciais: {
        valor_aluguel: '',
        valor_venda: '',
        valor_condominio: '',
        valor_iptu: '',
        condominio_incluso: false,
        valor_caucao_sugerido: '',
        observacoes_comerciais: '',
    },
    fotos_novas: [],
    foto_principal_id: '',
    fotos_remover: [],
    documentos_novos: [],
    tipos_documentos: [],
    documentos_remover: [],
});

function submit() {
    form.post(route('imoveis.store'), { forceFormData: true });
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Novo Imóvel</h1>
                <AppBreadcrumb />
            </div>
            <Link :href="route('imoveis.index')" class="btn btn-ghost btn-sm">Cancelar</Link>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body">
                <WizardImovel
                    :form="form"
                    :errors="form.errors"
                    :processing="form.processing"
                    :proprietarios="proprietarios"
                    :corretores="corretores"
                    @submit="submit"
                />
            </div>
        </div>
    </div>
</template>

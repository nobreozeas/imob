<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import WizardCliente from '@/Components/Clientes/WizardCliente.vue';
import type { FormularioClienteData } from '@/types/cliente';

defineOptions({ layout: AdminLayout });

const form = useForm<FormularioClienteData>({
    tipo_pessoa: 'fisica',
    nome: '',
    razao_social: '',
    nome_fantasia: '',
    cpf: '',
    cnpj: '',
    rg: '',
    data_nascimento: '',
    telefone_principal: '',
    whatsapp: '',
    telefone_secundario: '',
    email_principal: '',
    email_alternativo: '',
    cep: '',
    logradouro: '',
    numero: '',
    complemento: '',
    bairro: '',
    cidade: '',
    estado: '',
    ponto_referencia: '',
    observacoes: '',
    papeis: [],
    proprietario: {
        banco: '',
        agencia: '',
        conta: '',
        tipo_conta: '',
        chave_pix: '',
        tipo_chave_pix: '',
        percentual_administracao: '',
        emite_nota_fiscal: false,
        preferencia_recebimento: '',
        observacoes_repasse: '',
    },
    inquilino: {
        profissao: '',
        renda_mensal: '',
        local_trabalho: '',
        telefone_comercial: '',
        contato_emergencia: '',
        observacoes_cadastrais: '',
        restricoes: '',
    },
});

function submit() {
    form.post(route('clientes.store'));
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Novo Cliente</h1>
                <AppBreadcrumb />
            </div>
            <Link :href="route('clientes.index')" class="btn btn-ghost btn-sm">Cancelar</Link>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body">
                <WizardCliente
                    :form="form"
                    :errors="form.errors"
                    :processing="form.processing"
                    @submit="submit"
                />
            </div>
        </div>
    </div>
</template>

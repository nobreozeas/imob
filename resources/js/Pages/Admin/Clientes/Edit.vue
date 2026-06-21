<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import WizardCliente from '@/Components/Clientes/WizardCliente.vue';
import type { Cliente, FormularioClienteData, PapelCliente } from '@/types/cliente';

defineOptions({ layout: AdminLayout });

const props = defineProps<{ cliente: Cliente }>();


const form = useForm<FormularioClienteData>({
    tipo_pessoa: props.cliente.tipo_pessoa,
    nome: props.cliente.nome ?? '',
    razao_social: props.cliente.razao_social ?? '',
    nome_fantasia: props.cliente.nome_fantasia ?? '',
    cpf: props.cliente.cpf ?? '',
    cnpj: props.cliente.cnpj ?? '',
    rg: props.cliente.rg ?? '',
    data_nascimento: props.cliente.data_nascimento
        ? String(props.cliente.data_nascimento).substring(0, 10)
        : '',
    telefone_principal: props.cliente.telefone_principal ?? '',
    whatsapp: props.cliente.whatsapp ?? '',
    telefone_secundario: props.cliente.telefone_secundario ?? '',
    email_principal: props.cliente.email_principal ?? '',
    email_alternativo: props.cliente.email_alternativo ?? '',
    cep: props.cliente.cep ?? '',
    logradouro: props.cliente.logradouro ?? '',
    numero: props.cliente.numero ?? '',
    complemento: props.cliente.complemento ?? '',
    bairro: props.cliente.bairro ?? '',
    cidade: props.cliente.cidade ?? '',
    estado: props.cliente.estado ?? '',
    ponto_referencia: props.cliente.ponto_referencia ?? '',
    observacoes: props.cliente.observacoes ?? '',
    papeis: props.cliente.papeis.map((p) => p.papel as PapelCliente),
    proprietario: {
        banco: props.cliente.dados_proprietario?.banco ?? '',
        agencia: props.cliente.dados_proprietario?.agencia ?? '',
        conta: props.cliente.dados_proprietario?.conta ?? '',
        tipo_conta: props.cliente.dados_proprietario?.tipo_conta ?? '',
        chave_pix: props.cliente.dados_proprietario?.chave_pix ?? '',
        tipo_chave_pix: props.cliente.dados_proprietario?.tipo_chave_pix ?? '',
        percentual_administracao: props.cliente.dados_proprietario?.percentual_administracao ?? '',
        emite_nota_fiscal: props.cliente.dados_proprietario?.emite_nota_fiscal ?? false,
        preferencia_recebimento: props.cliente.dados_proprietario?.preferencia_recebimento ?? '',
        observacoes_repasse: props.cliente.dados_proprietario?.observacoes_repasse ?? '',
    },
    inquilino: {
        profissao: props.cliente.dados_inquilino?.profissao ?? '',
        renda_mensal: props.cliente.dados_inquilino?.renda_mensal ?? '',
        local_trabalho: props.cliente.dados_inquilino?.local_trabalho ?? '',
        telefone_comercial: props.cliente.dados_inquilino?.telefone_comercial ?? '',
        contato_emergencia: props.cliente.dados_inquilino?.contato_emergencia ?? '',
        observacoes_cadastrais: props.cliente.dados_inquilino?.observacoes_cadastrais ?? '',
        restricoes: props.cliente.dados_inquilino?.restricoes ?? '',
    },
});

function submit() {
    form.put(route('clientes.update', props.cliente.id));
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Editar Cliente</h1>
                <AppBreadcrumb />
            </div>
            <Link :href="route('clientes.show', cliente.id)" class="btn btn-ghost btn-sm">Cancelar</Link>
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

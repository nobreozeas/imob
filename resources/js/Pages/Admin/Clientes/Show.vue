<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import BadgePapel from '@/Components/Clientes/BadgePapel.vue';
import BadgeStatus from '@/Components/Clientes/BadgeStatus.vue';
import CardDadosProprietario from '@/Components/Clientes/CardDadosProprietario.vue';
import CardDadosInquilino from '@/Components/Clientes/CardDadosInquilino.vue';
import Swal from 'sweetalert2';
import type { Cliente } from '@/types/cliente';

defineOptions({ layout: AdminLayout });

const props = defineProps<{ cliente: Cliente }>();
const page = usePage();

const temPropriietario = () => props.cliente.papeis.some((p) => p.papel === 'proprietario');
const temInquilino = () => props.cliente.papeis.some((p) => p.papel === 'inquilino');

async function confirmarAlterarStatus() {
    const acao = props.cliente.status === 'ativo' ? 'inativar' : 'ativar';
    const novoStatus = props.cliente.status === 'ativo' ? 'inativo' : 'ativo';

    const result = await Swal.fire({
        title: `Deseja ${acao} este cliente?`,
        text: `${props.cliente.nome_exibicao} será ${acao === 'ativar' ? 'ativado' : 'inativado'}.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Sim, ${acao}`,
        cancelButtonText: 'Cancelar',
        confirmButtonColor: acao === 'ativar' ? '#22c55e' : '#ef4444',
    });

    if (result.isConfirmed) {
        router.patch(route('clientes.alterar-status', props.cliente.id), { status: novoStatus });
    }
}

function formatarData(data: string | null): string {
    if (!data) return '—';
    return new Date(data).toLocaleDateString('pt-BR');
}//

function formatarMoeda(valor: string | null): string {
    if (!valor) return '—';
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(valor));
}
</script>

<template>
    <div class="space-y-4">
        <!-- Cabeçalho -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-base-content">
                    {{ cliente.nome_exibicao }}
                </h1>
                <AppBreadcrumb />
            </div>
            <div class="flex gap-2">
                <button
                    class="btn btn-sm"
                    :class="cliente.status === 'ativo' ? 'btn-error btn-outline' : 'btn-success btn-outline'"
                    @click="confirmarAlterarStatus">
                    {{ cliente.status === 'ativo' ? 'Inativar' : 'Ativar' }}
                </button>
                <Link :href="route('clientes.edit', cliente.id)" class="btn btn-sm btn-primary">
                    Editar
                </Link>
            </div>
        </div>

        <!-- Flash -->
        <div v-if="(page.props as any).flash?.status" class="alert alert-success">
            {{ (page.props as any).flash.status }}
        </div>

        <!-- Dados Principais -->
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body">
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="card-title text-base">Dados Principais</h2>
                    <BadgeStatus :status="cliente.status" />
                    <BadgePapel v-for="p in cliente.papeis" :key="p.papel" :papel="p.papel" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-base-content/60 block">Tipo de Pessoa</span>
                        <span>{{ cliente.tipo_pessoa === 'fisica' ? 'Pessoa Física' : 'Pessoa Jurídica' }}</span>
                    </div>
                    <div v-if="cliente.tipo_pessoa === 'fisica' && cliente.cpf">
                        <span class="text-base-content/60 block">CPF</span>
                        <span>{{ cliente.cpf }}</span>
                    </div>
                    <div v-if="cliente.tipo_pessoa === 'juridica' && cliente.cnpj">
                        <span class="text-base-content/60 block">CNPJ</span>
                        <span>{{ cliente.cnpj }}</span>
                    </div>
                    <div v-if="cliente.tipo_pessoa === 'juridica' && cliente.nome_fantasia">
                        <span class="text-base-content/60 block">Nome Fantasia</span>
                        <span>{{ cliente.nome_fantasia }}</span>
                    </div>
                    <div v-if="cliente.rg">
                        <span class="text-base-content/60 block">RG / Documento</span>
                        <span>{{ cliente.rg }}</span>
                    </div>
                    <div v-if="cliente.tipo_pessoa === 'fisica' && cliente.data_nascimento">
                        <span class="text-base-content/60 block">Data de Nascimento</span>
                        <span>{{ formatarData(cliente.data_nascimento) }}</span>
                    </div>
                    <div>
                        <span class="text-base-content/60 block">Cadastrado em</span>
                        <span>{{ formatarData(cliente.created_at) }}</span>
                    </div>
                    <div v-if="cliente.observacoes" class="md:col-span-2">
                        <span class="text-base-content/60 block">Observações</span>
                        <span class="whitespace-pre-line">{{ cliente.observacoes }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contatos -->
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-base mb-2">Contatos</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div v-if="cliente.telefone_principal">
                        <span class="text-base-content/60 block">Telefone Principal</span>
                        <span>{{ cliente.telefone_principal }}</span>
                    </div>
                    <div v-if="cliente.whatsapp">
                        <span class="text-base-content/60 block">WhatsApp</span>
                        <span>{{ cliente.whatsapp }}</span>
                    </div>
                    <div v-if="cliente.telefone_secundario">
                        <span class="text-base-content/60 block">Telefone Secundário</span>
                        <span>{{ cliente.telefone_secundario }}</span>
                    </div>
                    <div v-if="cliente.email_principal">
                        <span class="text-base-content/60 block">E-mail Principal</span>
                        <span>{{ cliente.email_principal }}</span>
                    </div>
                    <div v-if="cliente.email_alternativo">
                        <span class="text-base-content/60 block">E-mail Alternativo</span>
                        <span>{{ cliente.email_alternativo }}</span>
                    </div>
                    <p v-if="!cliente.telefone_principal && !cliente.email_principal && !cliente.whatsapp"
                        class="text-base-content/40">Nenhum contato informado.</p>
                </div>
            </div>
        </div>

        <!-- Endereço -->
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body">
                <h2 class="card-title text-base mb-2">Endereço</h2>
                <div v-if="cliente.logradouro || cliente.cidade" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div v-if="cliente.cep">
                        <span class="text-base-content/60 block">CEP</span>
                        <span>{{ cliente.cep }}</span>
                    </div>
                    <div v-if="cliente.logradouro" class="md:col-span-2">
                        <span class="text-base-content/60 block">Logradouro</span>
                        <span>{{ cliente.logradouro }}{{ cliente.numero ? `, ${cliente.numero}` : '' }}{{ cliente.complemento ? ` — ${cliente.complemento}` : '' }}</span>
                    </div>
                    <div v-if="cliente.bairro">
                        <span class="text-base-content/60 block">Bairro</span>
                        <span>{{ cliente.bairro }}</span>
                    </div>
                    <div v-if="cliente.cidade">
                        <span class="text-base-content/60 block">Cidade / Estado</span>
                        <span>{{ cliente.cidade }}{{ cliente.estado ? ` — ${cliente.estado}` : '' }}</span>
                    </div>
                    <div v-if="cliente.ponto_referencia" class="md:col-span-2">
                        <span class="text-base-content/60 block">Ponto de Referência</span>
                        <span>{{ cliente.ponto_referencia }}</span>
                    </div>
                </div>
                <p v-else class="text-base-content/40 text-sm">Nenhum endereço informado.</p>
            </div>
        </div>

        <!-- Dados de Proprietário -->
        <CardDadosProprietario
            v-if="temPropriietario() && cliente.dados_proprietario"
            :dados="cliente.dados_proprietario"
        />

        <!-- Dados de Inquilino -->
        <CardDadosInquilino
            v-if="temInquilino() && cliente.dados_inquilino"
            :dados="cliente.dados_inquilino"
        />

        <div class="flex justify-start">
            <Link :href="route('clientes.index')" class="btn btn-ghost btn-sm">← Voltar à lista</Link>
        </div>
    </div>
</template>

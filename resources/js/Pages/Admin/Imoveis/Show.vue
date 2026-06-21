<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import BadgeStatus from '@/Components/Imoveis/BadgeStatus.vue';
import BadgeTipo from '@/Components/Imoveis/BadgeTipo.vue';
import BadgeFinalidade from '@/Components/Imoveis/BadgeFinalidade.vue';
import CardCaracteristicas from '@/Components/Imoveis/CardCaracteristicas.vue';
import CardDadosComerciais from '@/Components/Imoveis/CardDadosComerciais.vue';
import GaleriaFotos from '@/Components/Imoveis/GaleriaFotos.vue';
import Swal from 'sweetalert2';
import type { Imovel, StatusImovel } from '@/types/imovel';

defineOptions({ layout: AdminLayout });

const props = defineProps<{ imovel: Imovel }>();

async function alterarStatus() {
    const opcoes: Record<string, string> = {
        disponivel: 'Disponível',
        reservado: 'Reservado',
        alugado: 'Alugado',
        em_manutencao: 'Em Manutenção',
        inativo: 'Inativo',
    };
    delete opcoes[props.imovel.status];

    const result = await Swal.fire({
        title: 'Alterar Status do Imóvel',
        input: 'select',
        inputOptions: opcoes,
        inputPlaceholder: 'Selecione o novo status',
        showCancelButton: true,
        confirmButtonText: 'Alterar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3b82f6',
    });

    if (result.isConfirmed && result.value) {
        router.patch(route('imoveis.alterar-status', props.imovel.id), { status: result.value as StatusImovel });
    }
}

function nomeProprietario(): string {
    const p = props.imovel.proprietario;
    if (!p) return '—';
    return p.tipo_pessoa === 'juridica' ? (p.razao_social ?? '—') : (p.nome ?? '—');
}
</script>

<template>
    <div class="space-y-4">
        <!-- Cabeçalho -->
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h1 class="text-2xl font-bold text-base-content">{{ imovel.titulo }}</h1>
                    <BadgeStatus :status="imovel.status" />
                </div>
                <div class="flex items-center gap-2 text-sm text-base-content/60">
                    <span class="font-mono">{{ imovel.codigo }}</span>
                    <span>·</span>
                    <BadgeTipo :tipo="imovel.tipo" />
                    <BadgeFinalidade :finalidade="imovel.finalidade" />
                </div>
                <AppBreadcrumb />
            </div>
            <div class="flex gap-2">
                <button class="btn btn-outline btn-sm" @click="alterarStatus">Alterar Status</button>
                <Link :href="route('imoveis.edit', imovel.id)" class="btn btn-primary btn-sm">Editar</Link>
                <Link :href="route('imoveis.index')" class="btn btn-ghost btn-sm">← Voltar</Link>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Coluna principal -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Dados principais -->
                <div class="card bg-base-100 shadow-sm border border-base-200">
                    <div class="card-body">
                        <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Dados Principais</h3>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-base-content/50">Proprietário</p>
                                <p class="font-medium">{{ nomeProprietario() }}</p>
                            </div>
                            <div v-if="imovel.corretor">
                                <p class="text-base-content/50">Corretor Responsável</p>
                                <p class="font-medium">{{ imovel.corretor.name }}</p>
                            </div>
                            <div v-if="imovel.descricao" class="col-span-2">
                                <p class="text-base-content/50">Descrição</p>
                                <p class="font-medium whitespace-pre-line">{{ imovel.descricao }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Endereço -->
                <div class="card bg-base-100 shadow-sm border border-base-200">
                    <div class="card-body">
                        <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Endereço</h3>
                        <div class="text-sm space-y-1">
                            <p v-if="imovel.logradouro">
                                {{ imovel.logradouro }}, {{ imovel.numero ?? 's/n' }}
                                <span v-if="imovel.complemento"> - {{ imovel.complemento }}</span>
                            </p>
                            <p v-if="imovel.bairro">{{ imovel.bairro }}</p>
                            <p v-if="imovel.cidade">{{ imovel.cidade }}{{ imovel.estado ? ` — ${imovel.estado}` : '' }}</p>
                            <p v-if="imovel.cep" class="text-base-content/50">CEP: {{ imovel.cep }}</p>
                            <p v-if="imovel.ponto_referencia" class="text-base-content/50">Ref: {{ imovel.ponto_referencia }}</p>
                            <p v-if="!imovel.logradouro && !imovel.cidade" class="text-base-content/40">Endereço não informado.</p>
                        </div>
                    </div>
                </div>

                <CardCaracteristicas :caracteristicas="imovel.caracteristicas" />
                <CardDadosComerciais :dados-comerciais="imovel.dados_comerciais" />

                <!-- Contratos -->
                <div class="card bg-base-100 shadow-sm border border-base-200">
                    <div class="card-body">
                        <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Contratos Vinculados</h3>
                        <p class="text-sm text-base-content/40">Nenhum contrato vinculado. O módulo de contratos estará disponível em breve.</p>
                    </div>
                </div>
            </div>

            <!-- Coluna lateral -->
            <div class="space-y-4">
                <GaleriaFotos :fotos="imovel.fotos" />

                <!-- Documentos -->
                <div class="card bg-base-100 shadow-sm border border-base-200">
                    <div class="card-body">
                        <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Documentos</h3>
                        <ul v-if="imovel.documentos.length > 0" class="space-y-2">
                            <li v-for="doc in imovel.documentos" :key="doc.id" class="text-sm">
                                <a :href="doc.url" target="_blank" class="link link-hover flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="truncate">{{ doc.nome_original }}</span>
                                    <span v-if="doc.tipo" class="badge badge-ghost badge-xs shrink-0">{{ doc.tipo }}</span>
                                </a>
                            </li>
                        </ul>
                        <p v-else class="text-sm text-base-content/40">Nenhum documento anexado.</p>
                    </div>
                </div>

                <!-- Meta -->
                <div class="card bg-base-100 shadow-sm border border-base-200">
                    <div class="card-body py-3">
                        <p class="text-xs text-base-content/40">
                            Cadastrado em {{ new Date(imovel.created_at).toLocaleDateString('pt-BR') }}
                        </p>
                        <p class="text-xs text-base-content/40">
                            Atualizado em {{ new Date(imovel.updated_at).toLocaleDateString('pt-BR') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

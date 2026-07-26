<script setup lang="ts">
import { ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TabsBar from '@/Components/Admin/TabsBar.vue';
import BadgeStatus from '@/Components/Imoveis/BadgeStatus.vue';
import BadgeTipo from '@/Components/Imoveis/BadgeTipo.vue';
import BadgeFinalidade from '@/Components/Imoveis/BadgeFinalidade.vue';
import CardCaracteristicas from '@/Components/Imoveis/CardCaracteristicas.vue';
import CardDadosComerciais from '@/Components/Imoveis/CardDadosComerciais.vue';
import GerenciadorFotosImovel from '@/Components/Imoveis/GerenciadorFotosImovel.vue';
import GerenciadorDocumentosImovel from '@/Components/Imoveis/GerenciadorDocumentosImovel.vue';
import HistoricoImovel from '@/Components/Imoveis/HistoricoImovel.vue';
import Swal from '@/lib/swal';
import type { Imovel, ImovelHistoricoPaginado, StatusImovel } from '@/types/imovel';

defineOptions({ layout: AdminLayout });

const props = defineProps<{ imovel: Imovel; historicos: ImovelHistoricoPaginado }>();

const page = usePage();
const auth = (page.props as any).auth;

const ABAS = [
    { slug: 'resumo', label: 'Resumo' },
    { slug: 'fotos', label: 'Fotos' },
    { slug: 'documentos', label: 'Documentos' },
    { slug: 'historico', label: 'Histórico' },
] as const;
type SlugAba = (typeof ABAS)[number]['slug'];
const abaAtiva = ref<SlugAba>('resumo');

function podeGerenciarFotos(): boolean {
    return auth?.permissions?.includes('imoveis.gerenciar-fotos');
}

function podeGerenciarDocumentos(): boolean {
    return auth?.permissions?.includes('imoveis.gerenciar-documentos');
}

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
            </div>
            <div class="flex gap-2">
                <button class="btn btn-outline btn-sm" @click="alterarStatus">Alterar Status</button>
                <Link :href="route('imoveis.edit', imovel.id)" class="btn btn-primary btn-sm">Editar</Link>
                <Link :href="route('imoveis.index')" class="btn btn-ghost btn-sm">← Voltar</Link>
            </div>
        </div>

        <!-- Tabs -->
        <TabsBar :tabs="ABAS" :active="abaAtiva" @select="(slug) => (abaAtiva = slug as SlugAba)" />

        <!-- Aba Resumo -->
        <div v-show="abaAtiva === 'resumo'" class="space-y-4">
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

            <div class="card bg-base-100 shadow-sm border border-base-200">
                <div class="card-body">
                    <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Contratos Vinculados</h3>
                    <p class="text-sm text-base-content/40">Nenhum contrato vinculado. O módulo de contratos estará disponível em breve.</p>
                </div>
            </div>

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

        <!-- Aba Fotos -->
        <div v-show="abaAtiva === 'fotos'">
            <GerenciadorFotosImovel :imovel="imovel" :pode-gerenciar="podeGerenciarFotos()" />
        </div>

        <!-- Aba Documentos -->
        <div v-show="abaAtiva === 'documentos'">
            <GerenciadorDocumentosImovel :imovel="imovel" :pode-gerenciar="podeGerenciarDocumentos()" />
        </div>

        <!-- Aba Histórico -->
        <div v-show="abaAtiva === 'historico'">
            <HistoricoImovel :historicos="historicos" />
        </div>
    </div>
</template>

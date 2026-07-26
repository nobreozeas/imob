<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TabsBar from '@/Components/Admin/TabsBar.vue';
import BadgeStatus from '@/Components/Contratos/BadgeStatus.vue';
import CardEncargos from '@/Components/Contratos/CardEncargos.vue';
import CardCaucao from '@/Components/Contratos/CardCaucao.vue';
import CardMultas from '@/Components/Contratos/CardMultas.vue';
import CardHistorico from '@/Components/Contratos/CardHistorico.vue';
import TabelaParcelas from '@/Components/Contratos/TabelaParcelas.vue';
import TabelaRepasses from '@/Components/Contratos/TabelaRepasses.vue';
import FormularioDocumentoContrato from '@/Components/Contratos/FormularioDocumentoContrato.vue';
import ModalEncerrar from '@/Components/Contratos/ModalEncerrar.vue';
import ModalRescindir from '@/Components/Contratos/ModalRescindir.vue';
import ModalRenovarContrato from '@/Components/Contratos/ModalRenovarContrato.vue';
import type { ContratoLocacao } from '@/types/contrato';
import { useContratoStatus } from '@/composables/useContratoStatus';
import Swal, { swalClass } from '@/lib/swal';

defineOptions({ layout: AdminLayout });

const props = defineProps<{ contrato: ContratoLocacao }>();

const { labelTipoContrato, labelTipoDocumento } = useContratoStatus();

const page = usePage();
const auth = (page.props as any).auth;

const mostrarModalEncerrar = ref(false);
const mostrarModalRescindir = ref(false);
const mostrarModalRenovar = ref(false);

const formAcao = useForm({});
const formRemoverDocumento = useForm({});

type SlugAba = 'resumo' | 'parcelas' | 'encargos' | 'caucao' | 'repasses' | 'documentos' | 'historico';

const ABAS: { slug: SlugAba; label: string }[] = [
    { slug: 'resumo', label: 'Resumo' },
    { slug: 'parcelas', label: 'Parcelas' },
    { slug: 'encargos', label: 'Encargos' },
    { slug: 'caucao', label: 'Caução' },
    { slug: 'repasses', label: 'Repasses' },
    { slug: 'documentos', label: 'Documentos' },
    { slug: 'historico', label: 'Histórico' },
];

const SLUGS_VALIDOS = ABAS.map(a => a.slug);

function abaInicial(): SlugAba {
    const valor = new URLSearchParams(window.location.search).get('tab');
    return (SLUGS_VALIDOS as string[]).includes(valor ?? '') ? (valor as SlugAba) : 'resumo';
}

const abaAtiva = ref<SlugAba>(abaInicial());

function selecionarAba(slug: SlugAba) {
    abaAtiva.value = slug;
    const url = new URL(window.location.href);
    url.searchParams.set('tab', slug);
    window.history.replaceState(null, '', url);
}

onMounted(() => {
    window.addEventListener('popstate', () => {
        abaAtiva.value = abaInicial();
    });
});

const contagemDocumentos = computed(() => props.contrato.documentos?.length ?? 0);
const contagemParcelas = computed(() => props.contrato.parcelas?.length ?? 0);
const contagemRepasses = computed(() => props.contrato.repasses?.length ?? 0);

const abasComContagem = computed(() =>
    ABAS.map((aba) => ({
        ...aba,
        count:
            aba.slug === 'parcelas'
                ? contagemParcelas.value
                : aba.slug === 'repasses'
                  ? contagemRepasses.value
                  : aba.slug === 'documentos'
                    ? contagemDocumentos.value
                    : undefined,
    })),
);

function podeRenovar(): boolean {
    return auth?.permissions?.includes('contratos.renovar');
}

function podeVisualizar(): boolean {
    return auth?.permissions?.includes('contratos.view');
}

function podeGerenciarDocumentos(): boolean {
    return auth?.permissions?.includes('contratos.documentos');
}

function ativar() {
    Swal.fire({
        title: 'Ativar Contrato?',
        text: 'O imóvel será marcado como Alugado.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ativar',
        cancelButtonText: 'Cancelar',
    }).then(result => {
        if (result.isConfirmed) {
            formAcao.post(route('contratos.ativar', props.contrato.id));
        }
    });
}

function enviarParaAssinatura() {
    Swal.fire({
        title: 'Enviar para Assinatura?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Enviar',
        cancelButtonText: 'Cancelar',
    }).then(result => {
        if (result.isConfirmed) {
            formAcao.post(route('contratos.enviar-assinatura', props.contrato.id));
        }
    });
}

function cancelar() {
    Swal.fire({
        title: 'Cancelar Contrato?',
        text: 'Esta ação não pode ser desfeita.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Cancelar Contrato',
        cancelButtonText: 'Voltar',
        customClass: swalClass('error'),
    }).then(result => {
        if (result.isConfirmed) {
            formAcao.post(route('contratos.cancelar', props.contrato.id));
        }
    });
}

function removerDocumento(documentoId: string) {
    Swal.fire({
        title: 'Remover documento?',
        text: 'Esta ação não pode ser desfeita.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Remover',
        cancelButtonText: 'Voltar',
        customClass: swalClass('error'),
    }).then(result => {
        if (result.isConfirmed) {
            formRemoverDocumento.delete(route('contratos.documentos.remover', [props.contrato.id, documentoId]));
        }
    });
}

function moeda(val: string | undefined | null): string {
    if (!val) return '—';
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(val));
}

function nomeCliente(c: ContratoLocacao['proprietario']): string {
    if (!c) return '—';
    return c.tipo_pessoa === 'juridica' ? (c.razao_social ?? '—') : (c.nome ?? '—');
}
</script>

<template>
    <div class="space-y-4">
        <div v-if="(page.props as any).flash?.status" class="alert alert-success">
            {{ (page.props as any).flash.status }}
        </div>

        <!-- Cabeçalho -->
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h1 class="text-2xl font-bold text-base-content font-mono">{{ contrato.numero }}</h1>
                    <BadgeStatus :status="contrato.status" />
                </div>
                <div class="flex items-center gap-2 text-sm text-base-content/60">
                    <span>{{ labelTipoContrato(contrato.tipo_contrato) }}</span>
                    <span>·</span>
                    <span>{{ contrato.data_inicio }} → {{ contrato.data_fim ?? 'Indefinido' }}</span>
                </div>
            </div>
            <div class="flex gap-2 flex-wrap justify-end">
                <a
                    v-if="podeVisualizar()"
                    :href="route('contratos.imprimir', contrato.id)"
                    target="_blank"
                    rel="noopener"
                    class="btn btn-outline btn-sm"
                >
                    Imprimir Contrato
                </a>
                <template v-if="contrato.status === 'rascunho'">
                    <button class="btn btn-outline btn-sm" @click="enviarParaAssinatura">Enviar para Assinatura</button>
                    <Link :href="route('contratos.edit', contrato.id)" class="btn btn-primary btn-sm">Editar</Link>
                    <button class="btn btn-error btn-outline btn-sm" @click="cancelar">Cancelar</button>
                </template>
                <template v-else-if="contrato.status === 'aguardando_assinatura'">
                    <button class="btn btn-success btn-sm" @click="ativar">Ativar Contrato</button>
                    <button class="btn btn-error btn-outline btn-sm" @click="cancelar">Cancelar</button>
                </template>
                <template v-else-if="contrato.status === 'ativo'">
                    <button v-if="podeRenovar()" class="btn btn-outline btn-sm" @click="mostrarModalRenovar = true">Renovar</button>
                    <button class="btn btn-warning btn-sm" @click="mostrarModalEncerrar = true">Encerrar</button>
                    <button class="btn btn-error btn-sm" @click="mostrarModalRescindir = true">Rescindir</button>
                </template>
                <template v-else-if="contrato.status === 'vencido'">
                    <button v-if="podeRenovar()" class="btn btn-primary btn-sm" @click="mostrarModalRenovar = true">Renovar</button>
                    <button class="btn btn-warning btn-sm" @click="mostrarModalEncerrar = true">Encerrar</button>
                </template>
                <Link :href="route('contratos.index')" class="btn btn-ghost btn-sm">← Voltar</Link>
            </div>
        </div>

        <!-- Abas -->
        <TabsBar :tabs="abasComContagem" :active="abaAtiva" @select="(slug) => selecionarAba(slug as SlugAba)" />

        <!-- Conteúdo: Resumo -->
        <div v-show="abaAtiva === 'resumo'" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 space-y-4">
                <div class="card bg-base-100 shadow-sm border border-base-300">
                    <div class="card-body">
                        <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Dados Gerais</h3>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-base-content/50">Imóvel</p>
                                <p class="font-medium">{{ contrato.imovel?.titulo ?? '—' }}</p>
                                <p class="text-xs text-base-content/40 font-mono">{{ contrato.imovel?.codigo }}</p>
                            </div>
                            <div>
                                <p class="text-base-content/50">Vencimento</p>
                                <p class="font-medium">Dia {{ contrato.dia_vencimento }}</p>
                            </div>
                            <div>
                                <p class="text-base-content/50">Proprietário</p>
                                <p class="font-medium">{{ nomeCliente(contrato.proprietario) }}</p>
                            </div>
                            <div>
                                <p class="text-base-content/50">Inquilino</p>
                                <p class="font-medium">{{ nomeCliente(contrato.inquilino) }}</p>
                            </div>
                            <div v-if="contrato.corretor">
                                <p class="text-base-content/50">Corretor</p>
                                <p class="font-medium">{{ contrato.corretor.name }}</p>
                            </div>
                            <div>
                                <p class="text-base-content/50">Aluguel</p>
                                <p class="font-medium text-lg">{{ moeda(contrato.valor_aluguel) }}</p>
                            </div>
                            <div v-if="contrato.observacoes" class="col-span-2">
                                <p class="text-base-content/50">Observações</p>
                                <p class="font-medium whitespace-pre-line">{{ contrato.observacoes }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="card bg-base-100 shadow-sm border border-base-300">
                    <div class="card-body">
                        <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Resumo Financeiro</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-base-content/60">Aluguel</span>
                                <span class="font-medium">{{ moeda(contrato.valor_aluguel) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/60">Taxa adm.</span>
                                <span class="font-medium">
                                    {{ contrato.tipo_taxa_administracao === 'percentual'
                                        ? `${contrato.valor_taxa_administracao}%`
                                        : moeda(contrato.valor_taxa_administracao) }}
                                </span>
                            </div>
                            <div v-if="contrato.caucao?.possui_caucao" class="flex justify-between">
                                <span class="text-base-content/60">Caução</span>
                                <span class="font-medium">{{ moeda(contrato.caucao.valor_caucao) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-sm border border-base-300">
                    <div class="card-body">
                        <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Vigência</h3>
                        <div class="space-y-2 text-sm">
                            <div>
                                <p class="text-base-content/50">Início</p>
                                <p class="font-medium">{{ contrato.data_inicio }}</p>
                            </div>
                            <div v-if="contrato.data_fim">
                                <p class="text-base-content/50">Fim</p>
                                <p class="font-medium">{{ contrato.data_fim }}</p>
                            </div>
                            <div v-if="contrato.duracao_meses">
                                <p class="text-base-content/50">Duração</p>
                                <p class="font-medium">{{ contrato.duracao_meses }} meses</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-sm border border-base-300">
                    <div class="card-body">
                        <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Informações</h3>
                        <div class="space-y-1 text-sm text-base-content/60">
                            <p>Criado em: {{ contrato.created_at ? new Date(contrato.created_at).toLocaleDateString('pt-BR') : '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conteúdo: Parcelas -->
        <div v-show="abaAtiva === 'parcelas'">
            <TabelaParcelas :contrato-id="contrato.id" :parcelas="contrato.parcelas ?? []" />
        </div>

        <!-- Conteúdo: Encargos -->
        <div v-show="abaAtiva === 'encargos'" class="space-y-4">
            <CardEncargos :encargos="contrato.encargos ?? []" />
            <CardMultas :multas="contrato.multas" />
        </div>

        <!-- Conteúdo: Caução -->
        <div v-show="abaAtiva === 'caucao'">
            <CardCaucao :contrato-id="contrato.id" :caucao="contrato.caucao" />
        </div>

        <!-- Conteúdo: Repasses -->
        <div v-show="abaAtiva === 'repasses'">
            <TabelaRepasses :repasses="contrato.repasses ?? []" />
        </div>

        <!-- Conteúdo: Documentos -->
        <div v-show="abaAtiva === 'documentos'" class="space-y-4">
            <FormularioDocumentoContrato v-if="podeGerenciarDocumentos()" :contrato-id="contrato.id" />

            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Documentos</h3>
                    <p v-if="!contrato.documentos?.length" class="text-sm text-base-content/40">Nenhum documento anexado.</p>
                    <ul v-else class="space-y-2">
                        <li v-for="doc in contrato.documentos" :key="doc.id" class="text-sm flex items-center gap-2">
                            <a :href="doc.url" target="_blank" class="link link-hover link-primary">{{ doc.nome_original }}</a>
                            <span class="badge badge-ghost badge-sm">{{ labelTipoDocumento(doc.tipo) }}</span>
                            <span v-if="doc.criador" class="text-base-content/40 text-xs">{{ doc.criador.name }}</span>
                            <button
                                v-if="podeGerenciarDocumentos()"
                                type="button"
                                class="btn btn-ghost btn-xs text-error ml-auto"
                                @click="removerDocumento(doc.id)"
                            >
                                Remover
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Conteúdo: Histórico -->
        <div v-show="abaAtiva === 'historico'">
            <CardHistorico :historicos="contrato.historicos ?? []" />
        </div>

        <ModalEncerrar
            v-if="mostrarModalEncerrar"
            :contrato-id="contrato.id"
            :caucao="contrato.caucao"
            @fechado="mostrarModalEncerrar = false"
        />
        <ModalRescindir
            v-if="mostrarModalRescindir"
            :contrato-id="contrato.id"
            :caucao="contrato.caucao"
            :multas="contrato.multas"
            :valor-aluguel="contrato.valor_aluguel"
            :data-fim="contrato.data_fim"
            @fechado="mostrarModalRescindir = false"
        />
        <ModalRenovarContrato
            v-if="mostrarModalRenovar"
            :contrato-id="contrato.id"
            :valor-aluguel-atual="contrato.valor_aluguel"
            :data-fim-atual="contrato.data_fim"
            :possui-caucao="!!contrato.caucao?.possui_caucao"
            @fechado="mostrarModalRenovar = false"
        />
    </div>
</template>

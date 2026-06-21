<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import type { FormularioContratoData, ImovelOpcao, InquilinoOpcao } from '@/types/contrato';
import { useContratoStatus } from '@/composables/useContratoStatus';

const props = defineProps<{
    form: InertiaForm<FormularioContratoData>;
    imoveis: ImovelOpcao[];
    inquilinos: InquilinoOpcao[];
}>();

const emit = defineEmits<{
    (e: 'prev'): void;
    (e: 'submit', acao: 'rascunho' | 'ativar'): void;
}>();

const { labelTipoContrato, labelIndice, labelTipoEncargo, labelResponsavel, labelTipoCaucao } = useContratoStatus();

function moeda(val: string): string {
    if (!val) return '-';
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(val));
}

function nomeImovel() {
    return props.imoveis.find(i => i.id === props.form.imovel_id)?.titulo ?? '-';
}

function nomeInquilino() {
    const inq = props.inquilinos.find(i => i.id === props.form.inquilino_id);
    if (!inq) return '-';
    return inq.tipo_pessoa === 'juridica' ? (inq.razao_social ?? '-') : (inq.nome ?? '-');
}

function submeter(acao: 'rascunho' | 'ativar') {
    emit('submit', acao);
}
</script>

<template>
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="card bg-base-200">
                <div class="card-body py-3">
                    <p class="font-medium text-sm mb-2">Imóvel e Partes</p>
                    <div class="text-sm space-y-1">
                        <div><span class="text-base-content/60">Imóvel:</span> {{ nomeImovel() }}</div>
                        <div><span class="text-base-content/60">Inquilino:</span> {{ nomeInquilino() }}</div>
                    </div>
                </div>
            </div>
            <div class="card bg-base-200">
                <div class="card-body py-3">
                    <p class="font-medium text-sm mb-2">Dados da Locação</p>
                    <div class="text-sm space-y-1">
                        <div><span class="text-base-content/60">Tipo:</span> {{ labelTipoContrato(form.tipo_contrato) }}</div>
                        <div><span class="text-base-content/60">Início:</span> {{ form.data_inicio }}</div>
                        <div><span class="text-base-content/60">Vencimento:</span> Dia {{ form.dia_vencimento }}</div>
                    </div>
                </div>
            </div>
            <div class="card bg-base-200">
                <div class="card-body py-3">
                    <p class="font-medium text-sm mb-2">Valores</p>
                    <div class="text-sm space-y-1">
                        <div><span class="text-base-content/60">Aluguel:</span> {{ moeda(form.valor_aluguel) }}</div>
                        <div><span class="text-base-content/60">Índice:</span> {{ labelIndice(form.indice_reajuste) }}</div>
                        <div><span class="text-base-content/60">Periodicidade:</span> {{ form.periodicidade_reajuste }} meses</div>
                    </div>
                </div>
            </div>
            <div class="card bg-base-200">
                <div class="card-body py-3">
                    <p class="font-medium text-sm mb-2">Caução</p>
                    <div class="text-sm">
                        <div v-if="!form.caucao.possui_caucao" class="text-base-content/60">Sem caução</div>
                        <div v-else>
                            <div><span class="text-base-content/60">Tipo:</span> {{ labelTipoCaucao(form.caucao.tipo_caucao) }}</div>
                            <div><span class="text-base-content/60">Valor:</span> {{ moeda(form.caucao.valor_caucao) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-200">
            <div class="card-body py-3">
                <p class="font-medium text-sm mb-2">Encargos</p>
                <div class="flex flex-wrap gap-2">
                    <span v-for="enc in form.encargos.filter(e => e.responsavel !== 'nao_se_aplica')" :key="enc.tipo_encargo" class="badge badge-sm">
                        {{ labelTipoEncargo(enc.tipo_encargo) }}: {{ labelResponsavel(enc.responsavel) }}
                    </span>
                    <span v-if="form.encargos.every(e => e.responsavel === 'nao_se_aplica')" class="text-sm text-base-content/60">Nenhum configurado</span>
                </div>
            </div>
        </div>

        <div v-if="form.documentos_novos?.length > 0" class="card bg-base-200">
            <div class="card-body py-3">
                <p class="font-medium text-sm mb-2">Documentos ({{ form.documentos_novos.length }})</p>
                <ul class="text-sm space-y-0.5">
                    <li v-for="(doc, idx) in form.documentos_novos" :key="idx">{{ doc.name }}</li>
                </ul>
            </div>
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text font-medium">Observações</span></label>
            <textarea v-model="form.observacoes" class="textarea textarea-bordered w-full" rows="3" />
        </div>

        <div class="flex justify-between pt-2">
            <button type="button" class="btn btn-ghost" @click="$emit('prev')">← Anterior</button>
            <div class="flex gap-2">
                <button type="button" class="btn btn-outline" :disabled="form.processing" @click="submeter('rascunho')">
                    <span v-if="form.processing && form.acao === 'rascunho'" class="loading loading-spinner loading-sm" />
                    Salvar como Rascunho
                </button>
                <button type="button" class="btn btn-primary" :disabled="form.processing" @click="submeter('ativar')">
                    <span v-if="form.processing && form.acao === 'ativar'" class="loading loading-spinner loading-sm" />
                    Ativar Contrato
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { ContratoCaucao, ContratoMultas } from '@/types/contrato';

const props = defineProps<{
    contratoId: string;
    caucao?: ContratoCaucao | null;
    multas?: ContratoMultas | null;
    valorAluguel?: string;
    dataFim?: string | null;
}>();

const emit = defineEmits<{ (e: 'fechado'): void }>();

const form = useForm({
    data_rescisao: '',
    motivo: '',
    solicitado_por: 'locatario' as 'locatario' | 'locador' | 'imobiliaria' | 'acordo',
    destino_imovel: 'disponivel' as 'disponivel' | 'inativo',
    acao_parcelas_futuras: 'cancelar_parcelas_futuras' as 'cancelar_parcelas_futuras' | 'manter_parcelas_futuras',
    valor_desconto: '',
    valor_caucao_abatida: '',
    valor_caucao_retida: '',
    valor_caucao_devolvida: '',
    motivo_retencao_caucao: '',
    observacoes: '',
});

const multaEstimada = computed(() => {
    if (!props.multas?.possui_multa_rescisao || !form.data_rescisao || !props.dataFim || !props.valorAluguel) return null;
    const rescisao = new Date(form.data_rescisao);
    const fim = new Date(props.dataFim);
    if (rescisao >= fim) return null;
    const mesesRestantes = Math.ceil((fim.getTime() - rescisao.getTime()) / (1000 * 60 * 60 * 24 * 30));
    const percentual = Number(props.multas.percentual_multa_rescisao) / 100;
    const base = props.multas.base_calculo_rescisao === 'alugueis_restantes'
        ? Number(props.valorAluguel) * mesesRestantes
        : Number(props.valorAluguel);
    return base * percentual;
});

function moeda(val: number | null): string {
    if (val === null) return '';
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val);
}

function submeter() {
    form.post(route('contratos.rescindir', props.contratoId), {
        onSuccess: () => emit('fechado'),
    });
}
</script>

<template>
    <dialog id="modal-rescindir" class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Rescindir Contrato</h3>
            <form @submit.prevent="submeter" class="space-y-4">
                <div class="form-control">
                    <label class="label"><span class="label-text">Data da Rescisão *</span></label>
                    <input v-model="form.data_rescisao" type="date" class="input input-bordered" required />
                    <p v-if="form.errors.data_rescisao" class="text-error text-sm mt-1">{{ form.errors.data_rescisao }}</p>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Solicitado por *</span></label>
                    <select v-model="form.solicitado_por" class="select select-bordered">
                        <option value="locatario">Locatário (inquilino)</option>
                        <option value="locador">Locador (proprietário)</option>
                        <option value="imobiliaria">Imobiliária</option>
                        <option value="acordo">Acordo entre as partes</option>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Motivo *</span></label>
                    <textarea v-model="form.motivo" class="textarea textarea-bordered" rows="3" required />
                    <p v-if="form.errors.motivo" class="text-error text-sm mt-1">{{ form.errors.motivo }}</p>
                </div>
                <div v-if="multaEstimada" class="alert alert-warning">
                    <span class="text-sm">Multa estimada por rescisão: <strong>{{ moeda(multaEstimada) }}</strong> (o valor final é recalculado pelo sistema)</span>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Desconto sobre a multa</span></label>
                    <input v-model="form.valor_desconto" type="number" step="0.01" min="0" class="input input-bordered" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Destino do imóvel *</span></label>
                        <select v-model="form.destino_imovel" class="select select-bordered">
                            <option value="disponivel">Disponível</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Parcelas futuras *</span></label>
                        <select v-model="form.acao_parcelas_futuras" class="select select-bordered">
                            <option value="cancelar_parcelas_futuras">Cancelar</option>
                            <option value="manter_parcelas_futuras">Manter</option>
                        </select>
                    </div>
                </div>
                <template v-if="caucao?.possui_caucao && Number(caucao.saldo_atual) > 0">
                    <div class="divider text-sm">Caução (saldo atual: {{ moeda(Number(caucao.saldo_atual)) }})</div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Abater débitos com a caução</span></label>
                        <input v-model="form.valor_caucao_abatida" type="number" step="0.01" min="0" class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Reter da caução</span></label>
                        <input v-model="form.valor_caucao_retida" type="number" step="0.01" min="0" class="input input-bordered" />
                    </div>
                    <div class="form-control" v-if="form.valor_caucao_retida">
                        <label class="label"><span class="label-text">Motivo da retenção</span></label>
                        <textarea v-model="form.motivo_retencao_caucao" class="textarea textarea-bordered" rows="2" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Devolver da caução</span></label>
                        <input v-model="form.valor_caucao_devolvida" type="number" step="0.01" min="0" class="input input-bordered" />
                    </div>
                </template>
                <div class="form-control">
                    <label class="label"><span class="label-text">Observações</span></label>
                    <textarea v-model="form.observacoes" class="textarea textarea-bordered" rows="2" />
                </div>
                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" @click="$emit('fechado')">Cancelar</button>
                    <button type="submit" class="btn btn-error" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner loading-sm" />
                        Confirmar Rescisão
                    </button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" @click="$emit('fechado')" />
    </dialog>
</template>

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
    motivo_rescisao: '',
    parte_requerente: 'inquilino' as 'proprietario' | 'inquilino' | 'ambos',
    valor_devolvido: '',
    data_devolucao_caucao: '',
    observacao_caucao: '',
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
                    <label class="label"><span class="label-text">Parte Requerente</span></label>
                    <select v-model="form.parte_requerente" class="select select-bordered">
                        <option value="inquilino">Inquilino</option>
                        <option value="proprietario">Proprietário</option>
                        <option value="ambos">Ambos</option>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Motivo</span></label>
                    <textarea v-model="form.motivo_rescisao" class="textarea textarea-bordered" rows="3" />
                </div>
                <div v-if="multaEstimada" class="alert alert-warning">
                    <span class="text-sm">Multa estimada por rescisão: <strong>{{ moeda(multaEstimada) }}</strong> (informativo)</span>
                </div>
                <template v-if="caucao?.possui_caucao">
                    <div class="divider text-sm">Devolução da Caução</div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Valor Devolvido</span></label>
                        <input v-model="form.valor_devolvido" type="number" step="0.01" min="0" class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Data de Devolução</span></label>
                        <input v-model="form.data_devolucao_caucao" type="date" class="input input-bordered" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Observação</span></label>
                        <textarea v-model="form.observacao_caucao" class="textarea textarea-bordered" rows="2" />
                    </div>
                </template>
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

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    contratoId: string;
    valorAluguelAtual: string;
    dataFimAtual: string | null;
    possuiCaucao: boolean;
}>();

const emit = defineEmits<{ (e: 'fechado'): void }>();

const form = useForm({
    data_renovacao: new Date().toISOString().substring(0, 10),
    nova_data_inicio: props.dataFimAtual ?? new Date().toISOString().substring(0, 10),
    nova_data_fim: '',
    valor_aluguel_novo: props.valorAluguelAtual,
    manter_encargos: true,
    manter_regras_multa: true,
    gerar_novas_parcelas: true,
    caucao_acao: 'manter' as 'manter' | 'devolver' | 'complementar',
    observacoes: '',
});

function submeter() {
    form.post(route('contratos.renovar', props.contratoId), {
        onSuccess: () => emit('fechado'),
    });
}
</script>

<template>
    <dialog class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Renovar Contrato</h3>
            <form @submit.prevent="submeter" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Nova Data de Início *</span></label>
                        <input v-model="form.nova_data_inicio" type="date" class="input input-bordered" required />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Nova Data de Fim</span></label>
                        <input v-model="form.nova_data_fim" type="date" class="input input-bordered" />
                    </div>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Novo Valor do Aluguel</span></label>
                    <input v-model="form.valor_aluguel_novo" type="number" step="0.01" min="0.01" class="input input-bordered" />
                </div>
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input v-model="form.manter_encargos" type="checkbox" class="checkbox" />
                        <span class="label-text">Manter encargos anteriores</span>
                    </label>
                </div>
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input v-model="form.manter_regras_multa" type="checkbox" class="checkbox" />
                        <span class="label-text">Manter regras de multa</span>
                    </label>
                </div>
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input v-model="form.gerar_novas_parcelas" type="checkbox" class="checkbox" />
                        <span class="label-text">Gerar novas parcelas</span>
                    </label>
                </div>
                <div v-if="possuiCaucao" class="form-control">
                    <label class="label"><span class="label-text">Caução</span></label>
                    <select v-model="form.caucao_acao" class="select select-bordered">
                        <option value="manter">Manter</option>
                        <option value="devolver">Devolver saldo</option>
                        <option value="complementar">Complementar</option>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Observações</span></label>
                    <textarea v-model="form.observacoes" class="textarea textarea-bordered" rows="2" />
                </div>
                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" @click="$emit('fechado')">Cancelar</button>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner loading-sm" />
                        Renovar Contrato
                    </button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" @click="$emit('fechado')" />
    </dialog>
</template>

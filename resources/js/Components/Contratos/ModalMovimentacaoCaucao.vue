<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import type { TipoMovimentacaoCaucao } from '@/types/caucao';

const props = defineProps<{ contratoId: string }>();
const emit = defineEmits<{ (e: 'fechado'): void }>();

const form = useForm({
    tipo_movimentacao: 'recebimento' as TipoMovimentacaoCaucao,
    valor: '',
    data_movimentacao: new Date().toISOString().substring(0, 10),
    forma_movimentacao: 'pix' as string,
    descricao: '',
});

function submeter() {
    form.post(route('contratos.caucao.movimentacoes', props.contratoId), {
        onSuccess: () => emit('fechado'),
    });
}
</script>

<template>
    <dialog class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Movimentar Caução</h3>
            <form @submit.prevent="submeter" class="space-y-4">
                <div class="form-control">
                    <label class="label"><span class="label-text">Tipo de Movimentação *</span></label>
                    <select v-model="form.tipo_movimentacao" class="select select-bordered">
                        <option value="recebimento">Recebimento</option>
                        <option value="devolucao">Devolução</option>
                        <option value="abatimento">Abatimento</option>
                        <option value="retencao_parcial">Retenção Parcial</option>
                        <option value="retencao_integral">Retenção Integral</option>
                        <option value="ajuste">Ajuste</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Valor *</span></label>
                        <input v-model="form.valor" type="number" step="0.01" min="0.01" class="input input-bordered" required />
                        <p v-if="form.errors.valor" class="text-error text-sm mt-1">{{ form.errors.valor }}</p>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Data *</span></label>
                        <input v-model="form.data_movimentacao" type="date" class="input input-bordered" required />
                    </div>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Forma</span></label>
                    <select v-model="form.forma_movimentacao" class="select select-bordered">
                        <option value="pix">PIX</option>
                        <option value="dinheiro">Dinheiro</option>
                        <option value="transferencia">Transferência</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Descrição</span></label>
                    <textarea v-model="form.descricao" class="textarea textarea-bordered" rows="2" />
                </div>
                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" @click="$emit('fechado')">Cancelar</button>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner loading-sm" />
                        Registrar
                    </button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" @click="$emit('fechado')" />
    </dialog>
</template>

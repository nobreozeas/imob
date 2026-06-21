<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { ContratoCaucao } from '@/types/contrato';

const props = defineProps<{
    contratoId: string;
    caucao?: ContratoCaucao | null;
}>();

const emit = defineEmits<{ (e: 'fechado'): void }>();

const form = useForm({
    data_encerramento: '',
    motivo_encerramento: '',
    valor_devolvido: '',
    data_devolucao_caucao: '',
    observacao_caucao: '',
});

function submeter() {
    form.post(route('contratos.encerrar', props.contratoId), {
        onSuccess: () => emit('fechado'),
    });
}
</script>

<template>
    <dialog id="modal-encerrar" class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Encerrar Contrato</h3>
            <form @submit.prevent="submeter" class="space-y-4">
                <div class="form-control">
                    <label class="label"><span class="label-text">Data de Encerramento *</span></label>
                    <input v-model="form.data_encerramento" type="date" class="input input-bordered" required />
                    <p v-if="form.errors.data_encerramento" class="text-error text-sm mt-1">{{ form.errors.data_encerramento }}</p>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Motivo</span></label>
                    <textarea v-model="form.motivo_encerramento" class="textarea textarea-bordered" rows="3" />
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
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner loading-sm" />
                        Confirmar Encerramento
                    </button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" @click="$emit('fechado')" />
    </dialog>
</template>

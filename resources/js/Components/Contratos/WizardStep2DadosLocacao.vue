<script setup lang="ts">
import { computed } from 'vue';
import type { InertiaForm } from '@inertiajs/vue3';
import type { FormularioContratoData } from '@/types/contrato';

const props = defineProps<{ form: InertiaForm<FormularioContratoData> }>();
const emit = defineEmits<{ prev: []; next: [] }>();

const duracaoCalculada = computed(() => {
    if (!props.form.data_inicio || !props.form.data_fim) return '';
    const inicio = new Date(props.form.data_inicio);
    const fim = new Date(props.form.data_fim);
    const meses = (fim.getFullYear() - inicio.getFullYear()) * 12 + (fim.getMonth() - inicio.getMonth());
    return meses > 0 ? String(meses) : '';
});

function avancar() {
    if (!props.form.data_inicio || !props.form.dia_vencimento) return;
    props.form.duracao_meses = duracaoCalculada.value;
    emit('next');
}
</script>

<template>
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Tipo de Contrato</span></label>
                <select v-model="form.tipo_contrato" class="select select-bordered w-full">
                    <option value="residencial">Residencial</option>
                    <option value="comercial">Comercial</option>
                    <option value="temporada">Temporada</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Objetivo</span></label>
                <select v-model="form.objetivo_contrato" class="select select-bordered w-full">
                    <option value="aluguel">Aluguel</option>
                    <option value="temporada">Temporada</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Data de Início <span class="text-error">*</span></span></label>
                <input
                    v-model="form.data_inicio"
                    type="date"
                    class="input input-bordered w-full"
                    :class="{ 'input-error': form.errors.data_inicio }"
                    required
                />
                <p v-if="form.errors.data_inicio" class="text-error text-xs mt-1">{{ form.errors.data_inicio }}</p>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Data de Fim</span></label>
                <input v-model="form.data_fim" type="date" class="input input-bordered w-full" />
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Dia de Vencimento <span class="text-error">*</span></span></label>
                <input
                    v-model="form.dia_vencimento"
                    type="number"
                    min="1"
                    max="31"
                    class="input input-bordered w-full"
                    :class="{ 'input-error': form.errors.dia_vencimento }"
                    placeholder="Ex: 10"
                    required
                />
                <p v-if="form.errors.dia_vencimento" class="text-error text-xs mt-1">{{ form.errors.dia_vencimento }}</p>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Duração (meses)</span></label>
                <input
                    :value="duracaoCalculada"
                    type="text"
                    class="input input-bordered w-full bg-base-200"
                    readonly
                    placeholder="Calculado automaticamente"
                />
            </div>
        </div>

        <div class="flex justify-between pt-2">
            <button type="button" class="btn btn-ghost" @click="emit('prev')">← Anterior</button>
            <button type="button" class="btn btn-primary" @click="avancar">Próximo →</button>
        </div>
    </div>
</template>

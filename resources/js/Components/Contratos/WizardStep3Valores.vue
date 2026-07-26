<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import type { FormularioContratoData } from '@/types/contrato';

const props = defineProps<{ form: InertiaForm<FormularioContratoData> }>();
const emit = defineEmits<{ prev: []; next: [] }>();

function avancar() {
    if (!props.form.valor_aluguel) return;
    emit('next');
}
</script>

<template>
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control md:col-span-2">
                <label class="label"><span class="label-text font-medium">Valor do Aluguel (R$) <span class="text-error">*</span></span></label>
                <input
                    v-model="form.valor_aluguel"
                    type="number"
                    step="0.01"
                    min="0"
                    class="input input-bordered w-full"
                    :class="{ 'input-error': form.errors.valor_aluguel }"
                    placeholder="0,00"
                    required
                />
                <p v-if="form.errors.valor_aluguel" class="text-error text-xs mt-1">{{ form.errors.valor_aluguel }}</p>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Índice de Reajuste</span></label>
                <select v-model="form.indice_reajuste" class="select select-bordered w-full">
                    <option value="igpm">IGP-M</option>
                    <option value="ipca">IPCA</option>
                    <option value="inpc">INPC</option>
                    <option value="fixo">Fixo</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Periodicidade de Reajuste (meses)</span></label>
                <input
                    v-model="form.periodicidade_reajuste"
                    type="number"
                    min="1"
                    class="input input-bordered w-full"
                    placeholder="12"
                />
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Data do Primeiro Reajuste</span></label>
                <input v-model="form.data_primeiro_reajuste" type="date" class="input input-bordered w-full" />
            </div>

            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-3">
                    <input v-model="form.gerar_parcelas_automaticamente" type="checkbox" class="checkbox checkbox-primary" />
                    <span class="label-text font-medium">Gerar parcelas automaticamente ao ativar</span>
                </label>
            </div>

            <div v-if="form.gerar_parcelas_automaticamente && !form.data_fim" class="form-control">
                <label class="label"><span class="label-text font-medium">Quantidade de parcelas</span></label>
                <input
                    v-model="form.quantidade_parcelas"
                    type="number"
                    min="1"
                    class="input input-bordered w-full"
                    placeholder="Ex: 12"
                />
            </div>
        </div>

        <div class="flex justify-between pt-2">
            <button type="button" class="btn btn-ghost" @click="emit('prev')">← Anterior</button>
            <button type="button" class="btn btn-primary" :disabled="!form.valor_aluguel" @click="avancar">Próximo →</button>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import type { FormularioContratoData } from '@/types/contrato';

defineProps<{ form: InertiaForm<FormularioContratoData> }>();
const emit = defineEmits<{ prev: []; next: [] }>();
</script>

<template>
    <div class="space-y-4">
        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-3">
                <input v-model="form.caucao.possui_caucao" type="checkbox" class="checkbox checkbox-primary" />
                <span class="label-text font-medium">Contrato possui caução</span>
            </label>
        </div>

        <template v-if="form.caucao.possui_caucao">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control md:col-span-2">
                    <label class="label"><span class="label-text font-medium">Tipo de Caução <span class="text-error">*</span></span></label>
                    <select v-model="form.caucao.tipo_caucao" class="select select-bordered w-full" required>
                        <option value="">Selecione</option>
                        <option value="dinheiro">Dinheiro</option>
                        <option value="imovel">Imóvel</option>
                        <option value="fiador">Fiador</option>
                        <option value="seguro_fianca">Seguro Fiança</option>
                        <option value="deposito_bancario">Depósito Bancário</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Valor da Caução (R$)</span></label>
                    <input
                        v-model="form.caucao.valor_caucao"
                        type="number"
                        step="0.01"
                        min="0"
                        class="input input-bordered w-full"
                        placeholder="0,00"
                    />
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Data de Recebimento</span></label>
                    <input v-model="form.caucao.data_recebimento_caucao" type="date" class="input input-bordered w-full" />
                </div>
            </div>
        </template>

        <div class="flex justify-between pt-2">
            <button type="button" class="btn btn-ghost" @click="emit('prev')">← Anterior</button>
            <button type="button" class="btn btn-primary" @click="emit('next')">Próximo →</button>
        </div>
    </div>
</template>

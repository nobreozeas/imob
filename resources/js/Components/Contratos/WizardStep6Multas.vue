<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import type { FormularioContratoData } from '@/types/contrato';

defineProps<{ form: InertiaForm<FormularioContratoData> }>();
const emit = defineEmits<{ prev: []; next: [] }>();
</script>

<template>
    <div class="space-y-4">
        <div class="card bg-base-200 border border-base-300">
            <div class="card-body py-4">
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input v-model="form.multas.possui_multa_atraso" type="checkbox" class="checkbox checkbox-primary" />
                        <span class="label-text font-medium">Multa por atraso no pagamento</span>
                    </label>
                </div>
                <template v-if="form.multas.possui_multa_atraso">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-medium">Percentual de multa (%)</span></label>
                            <input
                                v-model="form.multas.percentual_multa_atraso"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                class="input input-bordered w-full"
                                placeholder="Ex: 2"
                            />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-medium">Juros por dia (%)</span></label>
                            <input
                                v-model="form.multas.valor_juros_dia"
                                type="number"
                                step="0.0001"
                                min="0"
                                class="input input-bordered w-full"
                                placeholder="Ex: 0,0333"
                            />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-medium">Dias de tolerância</span></label>
                            <input
                                v-model="form.multas.dias_tolerancia_atraso"
                                type="number"
                                step="1"
                                min="0"
                                class="input input-bordered w-full"
                                placeholder="Ex: 3"
                            />
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="card bg-base-200 border border-base-300">
            <div class="card-body py-4">
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input v-model="form.multas.possui_multa_rescisao" type="checkbox" class="checkbox checkbox-primary" />
                        <span class="label-text font-medium">Multa por rescisão antecipada</span>
                    </label>
                </div>
                <template v-if="form.multas.possui_multa_rescisao">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-medium">Percentual (%)</span></label>
                            <input
                                v-model="form.multas.percentual_multa_rescisao"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                class="input input-bordered w-full"
                                placeholder="Ex: 10"
                            />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-medium">Base de cálculo</span></label>
                            <select v-model="form.multas.base_calculo_rescisao" class="select select-bordered w-full">
                                <option value="">Selecione</option>
                                <option value="alugueis_restantes">Aluguéis restantes</option>
                                <option value="valor_fixo">Valor fixo</option>
                            </select>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex justify-between pt-2">
            <button type="button" class="btn btn-ghost" @click="emit('prev')">← Anterior</button>
            <button type="button" class="btn btn-primary" @click="emit('next')">Próximo →</button>
        </div>
    </div>
</template>

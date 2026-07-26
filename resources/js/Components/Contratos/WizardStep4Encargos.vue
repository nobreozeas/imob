<script setup lang="ts">
import { onMounted } from 'vue';
import type { InertiaForm } from '@inertiajs/vue3';
import type { FormularioContratoData, TipoEncargo } from '@/types/contrato';
import { useContratoStatus } from '@/composables/useContratoStatus';

const props = defineProps<{ form: InertiaForm<FormularioContratoData> }>();
const emit = defineEmits<{ prev: []; next: [] }>();

const { labelTipoEncargo } = useContratoStatus();

const tiposEncargo: TipoEncargo[] = ['iptu', 'condominio', 'agua', 'energia', 'gas', 'internet', 'outros'];

onMounted(() => {
    if (props.form.encargos.length === 0) {
        props.form.encargos = tiposEncargo.map(tipo => ({
            tipo_encargo: tipo,
            responsavel: 'nao_se_aplica' as const,
            valor_estimado: '',
            cobrar_junto_aluguel: false,
            observacao: '',
        }));
    }
});
</script>

<template>
    <div class="space-y-4">
        <p class="text-sm text-base-content/60">Defina o responsável por cada encargo do imóvel.</p>

        <div class="divide-y divide-base-200">
            <div
                v-for="(encargo, idx) in form.encargos"
                :key="encargo.tipo_encargo"
                class="grid grid-cols-1 md:grid-cols-5 gap-3 py-3 items-center"
            >
                <span class="text-sm font-medium text-base-content">{{ labelTipoEncargo(encargo.tipo_encargo) }}</span>
                <select v-model="form.encargos[idx].responsavel" class="select select-bordered select-sm w-full">
                    <option value="nao_se_aplica">N/A</option>
                    <option value="proprietario">Proprietário</option>
                    <option value="inquilino">Inquilino</option>
                </select>
                <input
                    v-model="form.encargos[idx].valor_estimado"
                    type="number"
                    step="0.01"
                    min="0"
                    class="input input-bordered input-sm w-full"
                    placeholder="Valor estimado"
                />
                <label class="label cursor-pointer gap-2 justify-start">
                    <input v-model="form.encargos[idx].cobrar_junto_aluguel" type="checkbox" class="checkbox checkbox-sm" />
                    <span class="label-text text-sm">Cobrar junto ao aluguel</span>
                </label>
                <input
                    v-model="form.encargos[idx].observacao"
                    type="text"
                    class="input input-bordered input-sm w-full"
                    placeholder="Observação"
                />
            </div>
        </div>

        <div class="flex justify-between pt-2">
            <button type="button" class="btn btn-ghost" @click="emit('prev')">← Anterior</button>
            <button type="button" class="btn btn-primary" @click="emit('next')">Próximo →</button>
        </div>
    </div>
</template>

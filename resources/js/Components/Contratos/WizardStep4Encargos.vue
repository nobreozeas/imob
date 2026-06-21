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
                class="grid grid-cols-1 md:grid-cols-3 gap-3 py-3 items-center"
            >
                <span class="text-sm font-medium text-base-content">{{ labelTipoEncargo(encargo.tipo_encargo) }}</span>
                <select v-model="form.encargos[idx].responsavel" class="select select-bordered select-sm w-full">
                    <option value="nao_se_aplica">N/A</option>
                    <option value="proprietario">Proprietário</option>
                    <option value="inquilino">Inquilino</option>
                </select>
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

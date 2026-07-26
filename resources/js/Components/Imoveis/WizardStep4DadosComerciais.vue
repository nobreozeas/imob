<script setup lang="ts">
import type { FormularioImovelData } from '@/types/imovel';

defineProps<{
    form: FormularioImovelData;
    errors: Partial<Record<string, string>>;
}>();

defineEmits<{ prev: []; next: [] }>();
</script>

<template>
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Valor do Aluguel (R$)</span></label>
                <input type="number" class="input input-bordered w-full" min="0" step="0.01"
                    v-model="form.dados_comerciais.valor_aluguel" />
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Valor de Venda (R$)</span></label>
                <input type="number" class="input input-bordered w-full" min="0" step="0.01"
                    v-model="form.dados_comerciais.valor_venda" />
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Valor do Condomínio (R$)</span></label>
                <input type="number" class="input input-bordered w-full" min="0" step="0.01"
                    v-model="form.dados_comerciais.valor_condominio" />
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Valor do IPTU (R$)</span></label>
                <input type="number" class="input input-bordered w-full" min="0" step="0.01"
                    v-model="form.dados_comerciais.valor_iptu" />
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Caução Sugerido (R$)</span></label>
                <input type="number" class="input input-bordered w-full" min="0" step="0.01"
                    v-model="form.dados_comerciais.valor_caucao_sugerido" />
            </div>
            <div class="form-control flex flex-row items-end pb-3">
                <label class="label cursor-pointer gap-2">
                    <input type="checkbox" class="checkbox checkbox-primary"
                        v-model="form.dados_comerciais.condominio_incluso" />
                    <span class="label-text font-medium">Condomínio incluso no aluguel</span>
                </label>
            </div>
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text font-medium">Observações Comerciais</span></label>
            <textarea class="textarea textarea-bordered w-full" rows="3"
                v-model="form.dados_comerciais.observacoes_comerciais" />
        </div>

        <div class="flex justify-between pt-2">
            <button type="button" class="btn btn-ghost" @click="$emit('prev')">← Anterior</button>
            <button type="button" class="btn btn-primary" @click="$emit('next')">Próximo →</button>
        </div>
    </div>
</template>

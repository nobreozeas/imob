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
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Área Total (m²)</span></label>
                <input type="number" class="input input-bordered w-full" min="0" step="0.01"
                    v-model="form.caracteristicas.area_total" />
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Área Construída (m²)</span></label>
                <input type="number" class="input input-bordered w-full" min="0" step="0.01"
                    v-model="form.caracteristicas.area_construida" />
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Quartos</span></label>
                <input type="number" class="input input-bordered w-full" min="0"
                    v-model="form.caracteristicas.quartos" />
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Suítes</span></label>
                <input type="number" class="input input-bordered w-full" min="0"
                    v-model="form.caracteristicas.suites" />
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Banheiros</span></label>
                <input type="number" class="input input-bordered w-full" min="0"
                    v-model="form.caracteristicas.banheiros" />
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Vagas de Garagem</span></label>
                <input type="number" class="input input-bordered w-full" min="0"
                    v-model="form.caracteristicas.vagas_garagem" />
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <label class="label cursor-pointer gap-2 justify-start">
                <input type="checkbox" class="checkbox checkbox-primary" v-model="form.caracteristicas.mobiliado" />
                <span class="label-text">Mobiliado</span>
            </label>
            <label class="label cursor-pointer gap-2 justify-start">
                <input type="checkbox" class="checkbox checkbox-primary" v-model="form.caracteristicas.aceita_pet" />
                <span class="label-text">Aceita Pet</span>
            </label>
            <label class="label cursor-pointer gap-2 justify-start">
                <input type="checkbox" class="checkbox checkbox-primary" v-model="form.caracteristicas.possui_piscina" />
                <span class="label-text">Piscina</span>
            </label>
            <label class="label cursor-pointer gap-2 justify-start">
                <input type="checkbox" class="checkbox checkbox-primary" v-model="form.caracteristicas.possui_quintal" />
                <span class="label-text">Quintal</span>
            </label>
            <label class="label cursor-pointer gap-2 justify-start">
                <input type="checkbox" class="checkbox checkbox-primary" v-model="form.caracteristicas.possui_varanda" />
                <span class="label-text">Varanda</span>
            </label>
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text font-medium">Outras Características</span></label>
            <textarea class="textarea textarea-bordered w-full" rows="3"
                placeholder="Descreva outras características relevantes..."
                v-model="form.caracteristicas.outras_caracteristicas" />
        </div>

        <div class="flex justify-between pt-2">
            <button type="button" class="btn btn-ghost" @click="$emit('prev')">← Anterior</button>
            <button type="button" class="btn btn-primary" @click="$emit('next')">Próximo →</button>
        </div>
    </div>
</template>

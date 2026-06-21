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
                <label class="label"><span class="label-text font-medium">CEP</span></label>
                <input type="text" class="input input-bordered w-full" placeholder="00000-000" v-model="form.cep" />
            </div>

            <div class="form-control md:col-span-2">
                <label class="label"><span class="label-text font-medium">Logradouro</span></label>
                <input type="text" class="input input-bordered w-full" placeholder="Rua, Avenida, etc." v-model="form.logradouro" />
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Número</span></label>
                <input type="text" class="input input-bordered w-full" v-model="form.numero" />
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Complemento</span></label>
                <input type="text" class="input input-bordered w-full" placeholder="Apto, Bloco, etc." v-model="form.complemento" />
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Bairro</span></label>
                <input type="text" class="input input-bordered w-full" v-model="form.bairro" />
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Cidade</span></label>
                <input type="text" class="input input-bordered w-full" v-model="form.cidade" />
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Estado</span></label>
                <input type="text" class="input input-bordered w-full" maxlength="2" placeholder="UF" v-model="form.estado" />
            </div>

            <div class="form-control md:col-span-2">
                <label class="label"><span class="label-text font-medium">Ponto de Referência</span></label>
                <input type="text" class="input input-bordered w-full" v-model="form.ponto_referencia" />
            </div>
        </div>

        <div class="flex justify-between pt-2">
            <button type="button" class="btn btn-ghost" @click="$emit('prev')">← Anterior</button>
            <button type="button" class="btn btn-primary" @click="$emit('next')">Próximo →</button>
        </div>
    </div>
</template>

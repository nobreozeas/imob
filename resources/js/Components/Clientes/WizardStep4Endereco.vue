<script setup lang="ts">
import type { FormularioClienteData } from '@/types/cliente';

defineProps<{
    form: FormularioClienteData;
    errors: Partial<Record<string, string>>;
}>();

defineEmits<{ next: [] }>();

const estados = [
    'AC','AL','AM','AP','BA','CE','DF','ES','GO','MA','MG','MS','MT',
    'PA','PB','PE','PI','PR','RJ','RN','RO','RR','RS','SC','SE','SP','TO',
];
</script>

<template>
    <div class="space-y-4">
        <p class="text-base-content/60 text-sm">Todos os campos de endereço são opcionais.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">CEP</span></label>
                <input type="text" class="input input-bordered w-full" placeholder="00000-000" v-model="form.cep" />
            </div>
            <div class="form-control md:col-span-2">
                <label class="label"><span class="label-text font-medium">Logradouro</span></label>
                <input type="text" class="input input-bordered w-full" v-model="form.logradouro" />
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Número</span></label>
                <input type="text" class="input input-bordered w-full" v-model="form.numero" />
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Complemento</span></label>
                <input type="text" class="input input-bordered w-full" v-model="form.complemento" />
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
                <select class="select select-bordered w-full" v-model="form.estado">
                    <option value="">Selecione</option>
                    <option v-for="uf in estados" :key="uf" :value="uf">{{ uf }}</option>
                </select>
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Ponto de Referência</span></label>
                <input type="text" class="input input-bordered w-full" v-model="form.ponto_referencia" />
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="button" class="btn btn-primary" @click="$emit('next')">Próximo →</button>
        </div>
    </div>
</template>

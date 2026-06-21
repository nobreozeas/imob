<script setup lang="ts">
import { ref, computed } from 'vue';
import type { FormularioClienteData } from '@/types/cliente';

const props = defineProps<{
    form: FormularioClienteData;
    errors: Partial<Record<string, string>>;
}>();

const emit = defineEmits<{ next: [] }>();

const localErrors = ref<Partial<Record<string, string>>>({});

const isPF = computed(() => props.form.tipo_pessoa === 'fisica');
const isPJ = computed(() => props.form.tipo_pessoa === 'juridica');

function validar(): boolean {
    localErrors.value = {};
    if (isPF.value && !props.form.nome?.trim())
        localErrors.value.nome = 'O nome completo é obrigatório.';
    if (isPJ.value && !props.form.razao_social?.trim())
        localErrors.value.razao_social = 'A razão social é obrigatória.';
    if (isPF.value && !props.form.cpf?.trim())
        localErrors.value.cpf = 'O CPF é obrigatório para pessoa física.';
    if (isPJ.value && !props.form.cnpj?.trim())
        localErrors.value.cnpj = 'O CNPJ é obrigatório para pessoa jurídica.';
    return Object.keys(localErrors.value).length === 0;
}

function avancar() {
    if (validar()) emit('next');
}

const erros = computed(() => ({ ...props.errors, ...localErrors.value }));
</script>

<template>
    <div class="space-y-4">
        <div class="form-control">
            <label class="label"><span class="label-text font-medium">Tipo de Pessoa <span class="text-error">*</span></span></label>
            <div class="flex gap-4">
                <label class="label cursor-pointer gap-2">
                    <input type="radio" class="radio radio-primary" value="fisica"
                        :checked="form.tipo_pessoa === 'fisica'"
                        @change="form.tipo_pessoa = 'fisica'" />
                    <span class="label-text">Pessoa Física</span>
                </label>
                <label class="label cursor-pointer gap-2">
                    <input type="radio" class="radio radio-primary" value="juridica"
                        :checked="form.tipo_pessoa === 'juridica'"
                        @change="form.tipo_pessoa = 'juridica'" />
                    <span class="label-text">Pessoa Jurídica</span>
                </label>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-if="isPF" class="form-control md:col-span-2">
                <label class="label"><span class="label-text font-medium">Nome Completo <span class="text-error">*</span></span></label>
                <input type="text" class="input input-bordered w-full" :class="{ 'input-error': erros.nome }"
                    v-model="form.nome" @input="localErrors.nome = ''" />
                <p v-if="erros.nome" class="text-error text-xs mt-1">{{ erros.nome }}</p>
            </div>

            <div v-if="isPJ" class="form-control md:col-span-2">
                <label class="label"><span class="label-text font-medium">Razão Social <span class="text-error">*</span></span></label>
                <input type="text" class="input input-bordered w-full" :class="{ 'input-error': erros.razao_social }"
                    v-model="form.razao_social" @input="localErrors.razao_social = ''" />
                <p v-if="erros.razao_social" class="text-error text-xs mt-1">{{ erros.razao_social }}</p>
            </div>

            <div v-if="isPJ" class="form-control">
                <label class="label"><span class="label-text font-medium">Nome Fantasia</span></label>
                <input type="text" class="input input-bordered w-full" v-model="form.nome_fantasia" />
            </div>

            <div v-if="isPF" class="form-control">
                <label class="label"><span class="label-text font-medium">CPF <span class="text-error">*</span></span></label>
                <input type="text" class="input input-bordered w-full" :class="{ 'input-error': erros.cpf }"
                    placeholder="000.000.000-00" v-model="form.cpf" @input="localErrors.cpf = ''" />
                <p v-if="erros.cpf" class="text-error text-xs mt-1">{{ erros.cpf }}</p>
            </div>

            <div v-if="isPJ" class="form-control">
                <label class="label"><span class="label-text font-medium">CNPJ <span class="text-error">*</span></span></label>
                <input type="text" class="input input-bordered w-full" :class="{ 'input-error': erros.cnpj }"
                    placeholder="00.000.000/0000-00" v-model="form.cnpj" @input="localErrors.cnpj = ''" />
                <p v-if="erros.cnpj" class="text-error text-xs mt-1">{{ erros.cnpj }}</p>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">RG / Documento</span></label>
                <input type="text" class="input input-bordered w-full" v-model="form.rg" />
            </div>

            <div v-if="isPF" class="form-control">
                <label class="label"><span class="label-text font-medium">Data de Nascimento</span></label>
                <input type="date" class="input input-bordered w-full" v-model="form.data_nascimento" />
            </div>
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text font-medium">Observações</span></label>
            <textarea class="textarea textarea-bordered w-full" rows="3" v-model="form.observacoes" />
        </div>

        <div class="flex justify-end pt-2">
            <button type="button" class="btn btn-primary" @click="avancar">Próximo →</button>
        </div>
    </div>
</template>

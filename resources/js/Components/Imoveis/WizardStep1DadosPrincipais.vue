<script setup lang="ts">
import { ref, computed } from 'vue';
import type { FormularioImovelData, ProprietarioOpcao, CorretorOpcao } from '@/types/imovel';

const props = defineProps<{
    form: FormularioImovelData;
    errors: Partial<Record<string, string>>;
    proprietarios: ProprietarioOpcao[];
    corretores: CorretorOpcao[];
}>();

const emit = defineEmits<{ next: [] }>();

const localErrors = ref<Partial<Record<string, string>>>({});

const erros = computed(() => ({ ...props.errors, ...localErrors.value }));

function nomeProprietario(p: ProprietarioOpcao): string {
    return p.tipo_pessoa === 'juridica' ? (p.razao_social ?? '') : (p.nome ?? '');
}

function validar(): boolean {
    localErrors.value = {};
    if (!props.form.titulo?.trim())
        localErrors.value.titulo = 'O título é obrigatório.';
    if (!props.form.tipo)
        localErrors.value.tipo = 'O tipo do imóvel é obrigatório.';
    if (!props.form.finalidade)
        localErrors.value.finalidade = 'A finalidade é obrigatória.';
    if (!props.form.proprietario_id)
        localErrors.value.proprietario_id = 'O proprietário é obrigatório.';
    return Object.keys(localErrors.value).length === 0;
}

function avancar() {
    if (validar()) emit('next');
}
</script>

<template>
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control md:col-span-2">
                <label class="label"><span class="label-text font-medium">Título do Imóvel <span class="text-error">*</span></span></label>
                <input type="text" class="input input-bordered w-full" :class="{ 'input-error': erros.titulo }"
                    placeholder="Ex: Apartamento 2 quartos - Centro" v-model="form.titulo" @input="localErrors.titulo = ''" />
                <p v-if="erros.titulo" class="text-error text-xs mt-1">{{ erros.titulo }}</p>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Código Interno</span></label>
                <input type="text" class="input input-bordered w-full" :class="{ 'input-error': erros.codigo }"
                    placeholder="Gerado automaticamente se vazio" v-model="form.codigo" />
                <p v-if="erros.codigo" class="text-error text-xs mt-1">{{ erros.codigo }}</p>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Status <span class="text-error">*</span></span></label>
                <select class="select select-bordered w-full" v-model="form.status">
                    <option value="disponivel">Disponível</option>
                    <option value="reservado">Reservado</option>
                    <option value="alugado">Alugado</option>
                    <option value="em_manutencao">Em Manutenção</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Tipo do Imóvel <span class="text-error">*</span></span></label>
                <select class="select select-bordered w-full" :class="{ 'select-error': erros.tipo }" v-model="form.tipo" @change="localErrors.tipo = ''">
                    <option value="">Selecione</option>
                    <option value="apartamento">Apartamento</option>
                    <option value="casa">Casa</option>
                    <option value="comercial">Comercial</option>
                    <option value="sala">Sala</option>
                    <option value="galpao">Galpão</option>
                    <option value="terreno">Terreno</option>
                    <option value="rural">Rural</option>
                    <option value="outro">Outro</option>
                </select>
                <p v-if="erros.tipo" class="text-error text-xs mt-1">{{ erros.tipo }}</p>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Finalidade <span class="text-error">*</span></span></label>
                <select class="select select-bordered w-full" :class="{ 'select-error': erros.finalidade }" v-model="form.finalidade" @change="localErrors.finalidade = ''">
                    <option value="">Selecione</option>
                    <option value="aluguel">Aluguel</option>
                    <option value="venda">Venda</option>
                    <option value="aluguel_venda">Aluguel / Venda</option>
                </select>
                <p v-if="erros.finalidade" class="text-error text-xs mt-1">{{ erros.finalidade }}</p>
            </div>

            <div class="form-control md:col-span-2">
                <label class="label"><span class="label-text font-medium">Proprietário <span class="text-error">*</span></span></label>
                <select class="select select-bordered w-full" :class="{ 'select-error': erros.proprietario_id }" v-model="form.proprietario_id" @change="localErrors.proprietario_id = ''">
                    <option value="">Selecione o proprietário</option>
                    <option v-for="p in proprietarios" :key="p.id" :value="p.id">{{ nomeProprietario(p) }}</option>
                </select>
                <p v-if="erros.proprietario_id" class="text-error text-xs mt-1">{{ erros.proprietario_id }}</p>
            </div>

            <div class="form-control md:col-span-2">
                <label class="label"><span class="label-text font-medium">Corretor Responsável</span></label>
                <select class="select select-bordered w-full" v-model="form.corretor_id">
                    <option value="">Nenhum</option>
                    <option v-for="c in corretores" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
            </div>

            <div class="form-control md:col-span-2">
                <label class="label"><span class="label-text font-medium">Descrição</span></label>
                <textarea class="textarea textarea-bordered w-full" rows="3"
                    placeholder="Descreva o imóvel..." v-model="form.descricao" />
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="button" class="btn btn-primary" @click="avancar">Próximo →</button>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import type { FormularioContratoData, ImovelOpcao, InquilinoOpcao, CorretorOpcao } from '@/types/contrato';

const props = defineProps<{
    form: InertiaForm<FormularioContratoData>;
    imoveis: ImovelOpcao[];
    inquilinos: InquilinoOpcao[];
    corretores: CorretorOpcao[];
}>();

const emit = defineEmits<{ next: [] }>();

function onImovelChange(e: Event) {
    const imovelId = (e.target as HTMLSelectElement).value;
    props.form.imovel_id = imovelId;
    const imovel = props.imoveis.find(i => i.id === imovelId);
    if (imovel) props.form.proprietario_id = imovel.proprietario_id;
}

function nomeCliente(c: InquilinoOpcao): string {
    return c.tipo_pessoa === 'juridica' ? (c.razao_social ?? '') : (c.nome ?? '');
}

function avancar() {
    if (!props.form.imovel_id || !props.form.inquilino_id) return;
    emit('next');
}
</script>

<template>
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control md:col-span-2">
                <label class="label"><span class="label-text font-medium">Imóvel <span class="text-error">*</span></span></label>
                <select
                    :value="form.imovel_id"
                    class="select select-bordered w-full"
                    :class="{ 'select-error': form.errors.imovel_id }"
                    @change="onImovelChange"
                    required
                >
                    <option value="">Selecione o imóvel</option>
                    <option v-for="im in imoveis" :key="im.id" :value="im.id">
                        {{ im.codigo }} — {{ im.titulo }}
                    </option>
                </select>
                <p v-if="form.errors.imovel_id" class="text-error text-xs mt-1">{{ form.errors.imovel_id }}</p>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Proprietário</span></label>
                <input
                    type="text"
                    class="input input-bordered w-full bg-base-200"
                    :value="imoveis.find(i => i.id === form.imovel_id)?.proprietario_nome ?? ''"
                    readonly
                    placeholder="Preenchido ao selecionar o imóvel"
                />
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Inquilino <span class="text-error">*</span></span></label>
                <select
                    v-model="form.inquilino_id"
                    class="select select-bordered w-full"
                    :class="{ 'select-error': form.errors.inquilino_id }"
                    required
                >
                    <option value="">Selecione o inquilino</option>
                    <option v-for="inq in inquilinos" :key="inq.id" :value="inq.id">
                        {{ nomeCliente(inq) }}
                    </option>
                </select>
                <p v-if="form.errors.inquilino_id" class="text-error text-xs mt-1">{{ form.errors.inquilino_id }}</p>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Corretor <span class="text-base-content/40 font-normal">(opcional)</span></span></label>
                <select v-model="form.corretor_id" class="select select-bordered w-full">
                    <option value="">Nenhum</option>
                    <option v-for="cor in corretores" :key="cor.id" :value="cor.id">{{ cor.name }}</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Número do Contrato</span>
                    <span class="label-text-alt text-base-content/40">deixe em branco para gerar</span>
                </label>
                <input
                    v-model="form.numero"
                    type="text"
                    class="input input-bordered w-full"
                    placeholder="LOC-202601-0001"
                />
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button
                type="button"
                class="btn btn-primary"
                :disabled="!form.imovel_id || !form.inquilino_id"
                @click="avancar"
            >
                Próximo →
            </button>
        </div>
    </div>
</template>

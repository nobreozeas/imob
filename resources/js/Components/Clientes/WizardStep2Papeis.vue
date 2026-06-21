<script setup lang="ts">
import { ref, computed } from 'vue';
import type { FormularioClienteData, PapelCliente } from '@/types/cliente';

const props = defineProps<{
    form: FormularioClienteData;
    errors: Partial<Record<string, string>>;
}>();

const emit = defineEmits<{ next: [] }>();

const localError = ref('');

function togglePapel(papel: PapelCliente) {
    localError.value = '';
    const idx = props.form.papeis.indexOf(papel);
    if (idx >= 0) {
        props.form.papeis.splice(idx, 1);
    } else {
        props.form.papeis.push(papel);
    }
}

function avancar() {
    if (props.form.papeis.length === 0) {
        localError.value = 'Selecione ao menos um papel para o cliente.';
        return;
    }
    emit('next');
}

const temProprietario = computed(() => props.form.papeis.includes('proprietario'));
const temInquilino = computed(() => props.form.papeis.includes('inquilino'));
</script>

<template>
    <div class="space-y-6">
        <p class="text-base-content/60 text-sm">
            Selecione um ou mais papéis que este cliente exercerá no sistema.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label
                class="cursor-pointer border-2 rounded-xl p-5 flex items-start gap-4 transition-colors"
                :class="temProprietario ? 'border-info bg-info/5' : 'border-base-200 hover:border-info/40'"
            >
                <input type="checkbox" class="checkbox checkbox-info mt-0.5"
                    :checked="temProprietario" @change="togglePapel('proprietario')" />
                <div>
                    <span class="font-semibold text-base block">Proprietário</span>
                    <span class="text-sm text-base-content/60">
                        Pode ser vinculado como proprietário de imóveis. Dados bancários serão solicitados na última etapa.
                    </span>
                </div>
            </label>

            <label
                class="cursor-pointer border-2 rounded-xl p-5 flex items-start gap-4 transition-colors"
                :class="temInquilino ? 'border-success bg-success/5' : 'border-base-200 hover:border-success/40'"
            >
                <input type="checkbox" class="checkbox checkbox-success mt-0.5"
                    :checked="temInquilino" @change="togglePapel('inquilino')" />
                <div>
                    <span class="font-semibold text-base block">Inquilino</span>
                    <span class="text-sm text-base-content/60">
                        Pode ser vinculado como locatário em contratos de aluguel.
                    </span>
                </div>
            </label>
        </div>

        <p v-if="localError || errors.papeis" class="text-error text-sm">
            {{ localError || errors.papeis }}
        </p>

        <div v-if="form.papeis.length > 0" class="alert alert-info alert-soft">
            <span class="text-sm">
                <strong>Selecionado:</strong>
                {{ form.papeis.map(p => p === 'proprietario' ? 'Proprietário' : 'Inquilino').join(' e ') }}
            </span>
        </div>

        <div class="flex justify-end pt-2">
            <button type="button" class="btn btn-primary" @click="avancar">Próximo →</button>
        </div>
    </div>
</template>

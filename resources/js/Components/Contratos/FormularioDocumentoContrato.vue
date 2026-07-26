<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import type { TipoDocumentoContrato } from '@/types/contrato';

const props = defineProps<{ contratoId: string }>();

const form = useForm<{ documento: File | null; tipo: TipoDocumentoContrato }>({
    documento: null,
    tipo: 'contrato_assinado',
});

function selecionarArquivo(event: Event) {
    const input = event.target as HTMLInputElement;
    form.documento = input.files?.[0] ?? null;
}

function enviar() {
    form.post(route('contratos.documentos.adicionar', props.contratoId), {
        forceFormData: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Anexar documento</h3>
            <form @submit.prevent="enviar" class="flex flex-col sm:flex-row gap-3 sm:items-end">
                <div class="form-control flex-1">
                    <label class="label"><span class="label-text">Arquivo</span></label>
                    <input
                        type="file"
                        class="file-input file-input-bordered file-input-sm w-full"
                        accept=".pdf,.jpg,.jpeg,.png,.docx"
                        @change="selecionarArquivo"
                    />
                    <p v-if="form.errors.documento" class="text-error text-xs mt-1">{{ form.errors.documento }}</p>
                </div>
                <div class="form-control w-full sm:w-56">
                    <label class="label"><span class="label-text">Tipo</span></label>
                    <select v-model="form.tipo" class="select select-bordered select-sm w-full">
                        <option value="contrato_assinado">Contrato Assinado</option>
                        <option value="laudo_vistoria">Laudo de Vistoria</option>
                        <option value="comprovante_caucao">Comprovante de Caução</option>
                        <option value="outros">Outros</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" :disabled="!form.documento || form.processing">
                    Anexar
                </button>
            </form>
            <p class="text-xs text-base-content/50 mt-2">PDF, JPG, PNG ou DOCX até 20MB.</p>
        </div>
    </div>
</template>

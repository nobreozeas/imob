<script setup lang="ts">
import { ref } from 'vue';
import type { InertiaForm } from '@inertiajs/vue3';
import type { FormularioContratoData } from '@/types/contrato';

const props = defineProps<{
    form: InertiaForm<FormularioContratoData>;
    documentosExistentes?: { id: string; nome_original: string; tipo: string; url: string }[];
}>();
defineEmits<{ (e: 'prev'): void; (e: 'next'): void }>();

const fileInput = ref<HTMLInputElement>();

function onArquivosSelecionados(e: Event) {
    const files = Array.from((e.target as HTMLInputElement).files ?? []);
    const total = (props.form.documentos_novos?.length ?? 0) + files.length;
    if (total > 10) {
        alert('Máximo de 10 documentos por contrato.');
        return;
    }
    props.form.documentos_novos = [...(props.form.documentos_novos ?? []), ...files];
    files.forEach(() => props.form.tipos_documentos.push('outros'));
}

function removerNovo(idx: number) {
    props.form.documentos_novos.splice(idx, 1);
    props.form.tipos_documentos.splice(idx, 1);
}
</script>

<template>
    <div class="space-y-4">
        <div v-if="documentosExistentes && documentosExistentes.length > 0">
            <p class="text-sm font-medium mb-2">Documentos existentes</p>
            <ul class="space-y-1">
                <li v-for="doc in documentosExistentes" :key="doc.id" class="flex items-center gap-2 text-sm">
                    <a :href="doc.url" target="_blank" class="link link-primary">{{ doc.nome_original }}</a>
                    <span class="badge badge-ghost badge-sm">{{ doc.tipo }}</span>
                </li>
            </ul>
            <div class="divider" />
        </div>

        <p class="text-sm text-base-content/60">Adicione até 10 documentos (PDF, JPEG, PNG, DOCX — max 20MB cada)</p>

        <div class="space-y-2">
            <div v-for="(arquivo, idx) in form.documentos_novos" :key="idx" class="flex items-center gap-2">
                <span class="text-sm flex-1 truncate">{{ arquivo.name }}</span>
                <select v-model="form.tipos_documentos[idx]" class="select select-bordered select-sm">
                    <option value="contrato_assinado">Contrato Assinado</option>
                    <option value="laudo_vistoria">Laudo de Vistoria</option>
                    <option value="comprovante_caucao">Comprovante de Caução</option>
                    <option value="outros">Outros</option>
                </select>
                <button type="button" class="btn btn-ghost btn-sm btn-square" @click="removerNovo(idx)">✕</button>
            </div>
        </div>

        <button
            v-if="(form.documentos_novos?.length ?? 0) < 10"
            type="button"
            class="btn btn-outline btn-sm"
            @click="fileInput?.click()"
        >
            + Adicionar Documento
        </button>
        <input
            ref="fileInput"
            type="file"
            class="hidden"
            accept=".pdf,.jpg,.jpeg,.png,.docx"
            multiple
            @change="onArquivosSelecionados"
        />

        <div class="flex justify-between mt-6">
            <button type="button" class="btn btn-ghost" @click="$emit('prev')">Anterior</button>
            <button type="button" class="btn btn-primary" @click="$emit('next')">Próximo</button>
        </div>
    </div>
</template>

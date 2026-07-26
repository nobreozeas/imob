<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { FileText } from 'lucide-vue-next';
import Swal, { swalClass } from '@/lib/swal';
import type { Imovel } from '@/types/imovel';

const props = defineProps<{ imovel: Imovel; podeGerenciar: boolean }>();

const form = useForm<{ documento: File | null; tipo: string }>({ documento: null, tipo: '' });

function onFileSelected(event: Event) {
    const input = event.target as HTMLInputElement;
    form.documento = input.files?.[0] ?? null;
}

function enviar() {
    if (!form.documento) return;

    form.post(route('imoveis.documentos.store', props.imovel.id), {
        forceFormData: true,
        onSuccess: () => {
            form.reset('documento', 'tipo');
        },
    });
}

async function remover(documentoId: string) {
    const result = await Swal.fire({
        title: 'Remover documento?',
        showCancelButton: true,
        confirmButtonText: 'Remover',
        cancelButtonText: 'Cancelar',
        customClass: swalClass('error'),
    });

    if (result.isConfirmed) {
        router.delete(route('imoveis.documentos.destroy', [props.imovel.id, documentoId]));
    }
}
</script>

<template>
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Documentos</h3>

            <form v-if="podeGerenciar" @submit.prevent="enviar" class="flex flex-wrap items-center gap-2 mb-4">
                <input type="file" accept=".pdf,.jpg,.jpeg,.png,.docx"
                       class="file-input file-input-bordered file-input-sm w-full max-w-xs"
                       @change="onFileSelected" />
                <input type="text" class="input input-bordered input-sm w-full max-w-xs"
                       placeholder="Tipo do documento (opcional)"
                       v-model="form.tipo" />
                <button type="submit" class="btn btn-primary btn-sm" :disabled="!form.documento || form.processing">
                    Enviar
                </button>
            </form>

            <ul v-if="imovel.documentos.length > 0" class="space-y-2">
                <li v-for="doc in imovel.documentos" :key="doc.id" class="flex items-center justify-between text-sm">
                    <a :href="doc.url" target="_blank" class="link link-hover flex items-center gap-1 truncate">
                        <FileText class="w-4 h-4 shrink-0" />
                        <span class="truncate">{{ doc.nome_original }}</span>
                        <span v-if="doc.tipo" class="badge badge-ghost badge-xs shrink-0">{{ doc.tipo }}</span>
                    </a>
                    <button v-if="podeGerenciar" class="btn btn-ghost btn-xs text-error shrink-0" @click="remover(doc.id)">Remover</button>
                </li>
            </ul>
            <p v-else class="text-sm text-base-content/40">Nenhum documento anexado.</p>
        </div>
    </div>
</template>

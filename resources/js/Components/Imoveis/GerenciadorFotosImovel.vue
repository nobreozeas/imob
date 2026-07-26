<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import Swal, { swalClass } from '@/lib/swal';
import type { Imovel } from '@/types/imovel';

const props = defineProps<{ imovel: Imovel; podeGerenciar: boolean }>();

const form = useForm<{ fotos: File[] }>({ fotos: [] });

function onFilesSelected(event: Event) {
    const input = event.target as HTMLInputElement;
    form.fotos = input.files ? Array.from(input.files) : [];
}

function enviar() {
    if (form.fotos.length === 0) return;

    form.post(route('imoveis.fotos.store', props.imovel.id), {
        forceFormData: true,
        onSuccess: () => {
            form.reset('fotos');
        },
    });
}

async function remover(fotoId: string) {
    const result = await Swal.fire({
        title: 'Remover foto?',
        showCancelButton: true,
        confirmButtonText: 'Remover',
        cancelButtonText: 'Cancelar',
        customClass: swalClass('error'),
    });

    if (result.isConfirmed) {
        router.delete(route('imoveis.fotos.destroy', [props.imovel.id, fotoId]));
    }
}

function definirPrincipal(fotoId: string) {
    router.patch(route('imoveis.fotos.principal', [props.imovel.id, fotoId]));
}
</script>

<template>
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Fotos</h3>

            <form v-if="podeGerenciar" @submit.prevent="enviar" class="flex items-center gap-2 mb-4">
                <input type="file" accept="image/jpeg,image/png,image/webp" multiple
                       class="file-input file-input-bordered file-input-sm w-full max-w-xs"
                       @change="onFilesSelected" />
                <button type="submit" class="btn btn-primary btn-sm" :disabled="form.fotos.length === 0 || form.processing">
                    Enviar
                </button>
            </form>

            <div v-if="imovel.fotos.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div v-for="foto in imovel.fotos" :key="foto.id" class="relative group">
                    <img :src="foto.url" :alt="foto.nome_original"
                         class="w-full h-28 object-cover rounded-md border"
                         :class="foto.is_principal ? 'border-primary' : 'border-base-200'" />
                    <span v-if="foto.is_principal" class="badge badge-primary badge-xs absolute top-1 left-1">Principal</span>
                    <div v-if="podeGerenciar" class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1">
                        <button v-if="!foto.is_principal" class="btn btn-xs" @click="definirPrincipal(foto.id)">Principal</button>
                        <button class="btn btn-xs btn-error" @click="remover(foto.id)">Remover</button>
                    </div>
                </div>
            </div>
            <p v-else class="text-sm text-base-content/40">Nenhuma foto cadastrada.</p>
        </div>
    </div>
</template>

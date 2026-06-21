<script setup lang="ts">
import { ref } from 'vue';
import type { FormularioImovelData, ImovelFoto, ImovelDocumento } from '@/types/imovel';

const props = defineProps<{
    form: FormularioImovelData;
    errors: Partial<Record<string, string>>;
    processing?: boolean;
    fotosExistentes: ImovelFoto[];
    documentosExistentes: ImovelDocumento[];
}>();

const emit = defineEmits<{ prev: []; submit: [] }>();

const previewFotos = ref<string[]>([]);
const nomesDocumentos = ref<string[]>([]);

function onFotosChange(event: Event) {
    const input = event.target as HTMLInputElement;
    if (!input.files) return;

    const arquivos = Array.from(input.files);
    props.form.fotos_novas = arquivos;

    previewFotos.value = [];
    arquivos.forEach(f => {
        const reader = new FileReader();
        reader.onload = e => previewFotos.value.push(e.target?.result as string);
        reader.readAsDataURL(f);
    });
}

function onDocumentosChange(event: Event) {
    const input = event.target as HTMLInputElement;
    if (!input.files) return;

    const arquivos = Array.from(input.files);
    props.form.documentos_novos = arquivos;
    props.form.tipos_documentos = arquivos.map(() => '');
    nomesDocumentos.value = arquivos.map(f => f.name);
}

function removerFotoExistente(id: string) {
    if (!props.form.fotos_remover.includes(id)) {
        props.form.fotos_remover.push(id);
    }
}

function restaurarFotoExistente(id: string) {
    const idx = props.form.fotos_remover.indexOf(id);
    if (idx !== -1) props.form.fotos_remover.splice(idx, 1);
}

function removerDocumentoExistente(id: string) {
    if (!props.form.documentos_remover.includes(id)) {
        props.form.documentos_remover.push(id);
    }
}

function estaRemovido(id: string): boolean {
    return props.form.fotos_remover.includes(id) || props.form.documentos_remover.includes(id);
}
</script>

<template>
    <div class="space-y-6">
        <!-- Fotos existentes -->
        <div v-if="fotosExistentes.length > 0" class="card bg-base-200/50 border border-base-300">
            <div class="card-body py-4">
                <h3 class="font-semibold text-sm mb-3">Fotos Atuais</h3>
                <div class="grid grid-cols-3 md:grid-cols-4 gap-3">
                    <div v-for="foto in fotosExistentes" :key="foto.id" class="relative">
                        <img :src="foto.url" :alt="foto.nome_original"
                             class="w-full h-24 object-cover rounded-lg border"
                             :class="estaRemovido(foto.id) ? 'opacity-30 border-error' : 'border-base-300'" />
                        <div class="absolute inset-0 flex flex-col items-center justify-center gap-1">
                            <button v-if="!estaRemovido(foto.id)" type="button"
                                    class="btn btn-error btn-xs"
                                    @click="removerFotoExistente(foto.id)">Remover</button>
                            <button v-else type="button"
                                    class="btn btn-ghost btn-xs"
                                    @click="restaurarFotoExistente(foto.id)">Restaurar</button>
                        </div>
                        <div v-if="foto.is_principal && !estaRemovido(foto.id)"
                             class="absolute top-1 left-1 badge badge-success badge-xs">Principal</div>
                        <!-- Radio principal -->
                        <label v-if="!estaRemovido(foto.id)" class="absolute bottom-1 left-1/2 -translate-x-1/2">
                            <input type="radio" class="radio radio-xs radio-success"
                                   :value="foto.id" v-model="form.foto_principal_id" />
                        </label>
                    </div>
                </div>
                <p class="text-xs text-base-content/50 mt-1">Selecione o radio abaixo da foto para definir a principal.</p>
            </div>
        </div>

        <!-- Upload novas fotos -->
        <div class="card bg-base-100 border border-base-200">
            <div class="card-body py-4 space-y-3">
                <h3 class="font-semibold text-sm">Adicionar Fotos</h3>
                <p class="text-xs text-base-content/50">Formatos aceitos: JPEG, PNG, WebP. Máximo 5 MB por foto.</p>
                <input type="file" class="file-input file-input-bordered w-full"
                       accept="image/jpeg,image/png,image/webp" multiple
                       @change="onFotosChange" />
                <p v-if="errors['fotos_novas.0']" class="text-error text-xs">{{ errors['fotos_novas.0'] }}</p>
                <div v-if="previewFotos.length > 0" class="grid grid-cols-3 md:grid-cols-5 gap-2 mt-2">
                    <img v-for="(src, i) in previewFotos" :key="i" :src="src"
                         class="w-full h-20 object-cover rounded-md border border-base-300" />
                </div>
            </div>
        </div>

        <!-- Documentos existentes -->
        <div v-if="documentosExistentes.length > 0" class="card bg-base-200/50 border border-base-300">
            <div class="card-body py-4">
                <h3 class="font-semibold text-sm mb-3">Documentos Atuais</h3>
                <ul class="space-y-2">
                    <li v-for="doc in documentosExistentes" :key="doc.id"
                        class="flex items-center justify-between text-sm p-2 rounded-lg"
                        :class="estaRemovido(doc.id) ? 'bg-error/10 line-through text-error' : 'bg-base-100'">
                        <a v-if="!estaRemovido(doc.id)" :href="doc.url" target="_blank" class="link link-hover truncate mr-2">
                            {{ doc.nome_original }}
                            <span v-if="doc.tipo" class="badge badge-ghost badge-xs ml-1">{{ doc.tipo }}</span>
                        </a>
                        <span v-else class="truncate mr-2">{{ doc.nome_original }}</span>
                        <button v-if="!estaRemovido(doc.id)" type="button"
                                class="btn btn-error btn-xs shrink-0"
                                @click="removerDocumentoExistente(doc.id)">Remover</button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Upload novos documentos -->
        <div class="card bg-base-100 border border-base-200">
            <div class="card-body py-4 space-y-3">
                <h3 class="font-semibold text-sm">Adicionar Documentos</h3>
                <p class="text-xs text-base-content/50">Formatos aceitos: PDF, JPEG, PNG, DOCX. Máximo 20 MB por arquivo.</p>
                <input type="file" class="file-input file-input-bordered w-full"
                       accept=".pdf,.jpg,.jpeg,.png,.docx" multiple
                       @change="onDocumentosChange" />
                <div v-if="nomesDocumentos.length > 0" class="space-y-2">
                    <div v-for="(nome, i) in nomesDocumentos" :key="i" class="flex items-center gap-2">
                        <span class="text-sm truncate flex-1">{{ nome }}</span>
                        <input type="text" class="input input-bordered input-sm w-40"
                               placeholder="Tipo (ex: matrícula)"
                               v-model="form.tipos_documentos[i]" />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-between pt-2">
            <button type="button" class="btn btn-ghost" @click="emit('prev')">← Anterior</button>
            <button type="button" class="btn btn-success btn-lg" :disabled="processing" @click="emit('submit')">
                <span v-if="processing" class="loading loading-spinner loading-sm" />
                Salvar Imóvel
            </button>
        </div>
    </div>
</template>

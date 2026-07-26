<script setup lang="ts">
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Swal, { swalClass } from '@/lib/swal';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppBreadcrumb from '@/Components/Admin/AppBreadcrumb.vue';
import type { CategoriaFinanceira } from '@/types/categoriaFinanceira';

defineOptions({ layout: AdminLayout });

const props = defineProps<{ categorias: CategoriaFinanceira[] }>();

const page = usePage();
const auth = (page.props as any).auth;

function tem(permissao: string): boolean {
    return auth?.permissions?.includes(permissao);
}

const modalAberto = ref(false);
const editando = ref<CategoriaFinanceira | null>(null);

const form = useForm({
    nome: '',
    tipo: 'entrada' as 'entrada' | 'saida',
    ativa: true as boolean,
});

function abrirCriacao() {
    editando.value = null;
    form.reset();
    form.clearErrors();
    modalAberto.value = true;
}

function abrirEdicao(categoria: CategoriaFinanceira) {
    editando.value = categoria;
    form.nome = categoria.nome;
    form.tipo = categoria.tipo;
    form.ativa = categoria.ativa;
    form.clearErrors();
    modalAberto.value = true;
}

function submeter() {
    if (editando.value) {
        form.put(route('financeiro.categorias.update', editando.value.id), {
            onSuccess: () => { modalAberto.value = false; },
        });
    } else {
        form.post(route('financeiro.categorias.store'), {
            onSuccess: () => { modalAberto.value = false; },
        });
    }
}

function excluir(categoria: CategoriaFinanceira) {
    Swal.fire({
        title: `Excluir categoria "${categoria.nome}"?`,
        showCancelButton: true,
        confirmButtonText: 'Excluir',
        cancelButtonText: 'Voltar',
        customClass: swalClass('error'),
    }).then(result => {
        if (result.isConfirmed) {
            useForm({}).delete(route('financeiro.categorias.destroy', categoria.id));
        }
    });
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-base-content">Categorias Financeiras</h1>
                <AppBreadcrumb />
            </div>
            <button v-if="tem('financeiro.criar')" class="btn btn-primary btn-sm" @click="abrirCriacao">+ Nova Categoria</button>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200 overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr class="bg-base-200/50">
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="categoria in props.categorias" :key="categoria.id" class="hover">
                        <td class="text-sm">{{ categoria.nome }}</td>
                        <td>
                            <span class="badge badge-sm" :class="categoria.tipo === 'entrada' ? 'badge-success' : 'badge-error'">
                                {{ categoria.tipo === 'entrada' ? 'Entrada' : 'Saída' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-sm" :class="categoria.ativa ? 'badge-success' : 'badge-ghost'">
                                {{ categoria.ativa ? 'Ativa' : 'Inativa' }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                <button v-if="tem('financeiro.editar')" class="btn btn-ghost btn-xs" @click="abrirEdicao(categoria)">Editar</button>
                                <button v-if="tem('financeiro.excluir')" class="btn btn-ghost btn-xs text-error" @click="excluir(categoria)">Excluir</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <dialog v-if="modalAberto" class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ editando ? 'Editar Categoria' : 'Nova Categoria' }}</h3>
                <form @submit.prevent="submeter" class="space-y-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Nome *</span></label>
                        <input v-model="form.nome" type="text" class="input input-bordered" required />
                        <p v-if="form.errors.nome" class="text-error text-sm mt-1">{{ form.errors.nome }}</p>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Tipo *</span></label>
                        <select v-model="form.tipo" class="select select-bordered">
                            <option value="entrada">Entrada</option>
                            <option value="saida">Saída</option>
                        </select>
                    </div>
                    <label class="label cursor-pointer justify-start gap-2">
                        <input v-model="form.ativa" type="checkbox" class="checkbox checkbox-sm" />
                        <span class="label-text">Ativa</span>
                    </label>
                    <div class="modal-action">
                        <button type="button" class="btn" @click="modalAberto = false">Cancelar</button>
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">Salvar</button>
                    </div>
                </form>
            </div>
            <div class="modal-backdrop" @click="modalAberto = false" />
        </dialog>
    </div>
</template>

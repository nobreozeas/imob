<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import type { CategoriaFinanceira } from '@/types/categoriaFinanceira';

const props = defineProps<{
    categorias: CategoriaFinanceira[];
}>();

const emit = defineEmits<{ (e: 'fechado'): void }>();

const form = useForm({
    categoria_financeira_id: '',
    descricao: '',
    valor: '',
    data_vencimento: '',
    status: 'pendente' as 'pendente' | 'pago',
    data_pagamento: new Date().toISOString().substring(0, 10),
    forma_pagamento: 'pix',
    observacoes: '',
});

function submeter() {
    form.post(route('financeiro.lancamentos.despesas.store'), {
        onSuccess: () => emit('fechado'),
    });
}
</script>

<template>
    <dialog class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Nova Despesa</h3>
            <form @submit.prevent="submeter" class="space-y-4">
                <div class="form-control">
                    <label class="label"><span class="label-text">Categoria *</span></label>
                    <select v-model="form.categoria_financeira_id" class="select select-bordered" required>
                        <option value="" disabled>Selecione</option>
                        <option v-for="c in props.categorias" :key="c.id" :value="c.id">{{ c.nome }}</option>
                    </select>
                    <p v-if="form.errors.categoria_financeira_id" class="text-error text-sm mt-1">{{ form.errors.categoria_financeira_id }}</p>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Descrição *</span></label>
                    <input v-model="form.descricao" type="text" class="input input-bordered" required />
                    <p v-if="form.errors.descricao" class="text-error text-sm mt-1">{{ form.errors.descricao }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Valor *</span></label>
                        <input v-model="form.valor" type="number" step="0.01" min="0.01" class="input input-bordered" required />
                        <p v-if="form.errors.valor" class="text-error text-sm mt-1">{{ form.errors.valor }}</p>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Vencimento</span></label>
                        <input v-model="form.data_vencimento" type="date" class="input input-bordered" />
                    </div>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Status *</span></label>
                    <select v-model="form.status" class="select select-bordered">
                        <option value="pendente">Pendente</option>
                        <option value="pago">Já paga</option>
                    </select>
                </div>
                <div v-if="form.status === 'pago'" class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Data do pagamento *</span></label>
                        <input v-model="form.data_pagamento" type="date" class="input input-bordered" />
                        <p v-if="form.errors.data_pagamento" class="text-error text-sm mt-1">{{ form.errors.data_pagamento }}</p>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Forma de pagamento *</span></label>
                        <select v-model="form.forma_pagamento" class="select select-bordered">
                            <option value="pix">PIX</option>
                            <option value="dinheiro">Dinheiro</option>
                            <option value="cartao_credito">Cartão de Crédito</option>
                            <option value="cartao_debito">Cartão de Débito</option>
                            <option value="transferencia">Transferência</option>
                            <option value="boleto">Boleto</option>
                            <option value="cheque">Cheque</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Observações</span></label>
                    <textarea v-model="form.observacoes" class="textarea textarea-bordered" rows="2" />
                </div>
                <div class="modal-action">
                    <button type="button" class="btn" @click="emit('fechado')">Cancelar</button>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">Salvar</button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" @click="emit('fechado')" />
    </dialog>
</template>

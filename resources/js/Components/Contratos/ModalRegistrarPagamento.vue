<script setup lang="ts">
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { ParcelaAluguel } from '@/types/parcela';

const props = defineProps<{
    contratoId: string;
    parcela: ParcelaAluguel;
}>();

const emit = defineEmits<{ (e: 'fechado'): void }>();

const form = useForm({
    data_pagamento: new Date().toISOString().substring(0, 10),
    forma_pagamento: 'pix' as
        | 'pix'
        | 'dinheiro'
        | 'cartao_credito'
        | 'cartao_debito'
        | 'transferencia'
        | 'boleto'
        | 'outro',
    valor_pago: props.parcela.valor_total,
    valor_desconto: '0',
    observacoes: '',
});

function moeda(val: string | number | null | undefined): string {
    if (val === null || val === undefined || val === '') return '—';
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(val));
}

const referencia = computed(() => `${String(props.parcela.mes_referencia).padStart(2, '0')}/${props.parcela.ano_referencia}`);

function submeter() {
    form.post(route('contratos.parcelas.pagamento', [props.contratoId, props.parcela.id]), {
        onSuccess: () => emit('fechado'),
    });
}
</script>

<template>
    <dialog class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-1">Registrar Pagamento</h3>
            <p class="text-sm text-base-content/60 mb-4">Parcela de referência {{ referencia }} — valor previsto {{ moeda(parcela.valor_total) }}</p>
            <form @submit.prevent="submeter" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Data do Pagamento *</span></label>
                        <input v-model="form.data_pagamento" type="date" class="input input-bordered" required />
                        <p v-if="form.errors.data_pagamento" class="text-error text-sm mt-1">{{ form.errors.data_pagamento }}</p>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Forma de Pagamento *</span></label>
                        <select v-model="form.forma_pagamento" class="select select-bordered">
                            <option value="pix">PIX</option>
                            <option value="dinheiro">Dinheiro</option>
                            <option value="cartao_credito">Cartão de Crédito</option>
                            <option value="cartao_debito">Cartão de Débito</option>
                            <option value="transferencia">Transferência</option>
                            <option value="boleto">Boleto</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Valor Pago *</span></label>
                        <input v-model="form.valor_pago" type="number" step="0.01" min="0.01" class="input input-bordered" required />
                        <p v-if="form.errors.valor_pago" class="text-error text-sm mt-1">{{ form.errors.valor_pago }}</p>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Desconto</span></label>
                        <input v-model="form.valor_desconto" type="number" step="0.01" min="0" class="input input-bordered" />
                    </div>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Observações</span></label>
                    <textarea v-model="form.observacoes" class="textarea textarea-bordered" rows="2" />
                </div>
                <p class="text-xs text-base-content/50">Multa e juros por atraso (se configurados) são calculados automaticamente pelo sistema.</p>
                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" @click="$emit('fechado')">Cancelar</button>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner loading-sm" />
                        Confirmar Pagamento
                    </button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" @click="$emit('fechado')" />
    </dialog>
</template>

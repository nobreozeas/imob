<script setup lang="ts">
import type { InertiaForm } from '@inertiajs/vue3';
import type { FormularioContratoData } from '@/types/contrato';

defineProps<{ form: InertiaForm<FormularioContratoData> }>();
const emit = defineEmits<{ prev: []; next: [] }>();
</script>

<template>
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Tipo de Taxa de Administração</span></label>
                <select v-model="form.tipo_taxa_administracao" class="select select-bordered w-full">
                    <option value="percentual">Percentual (%)</option>
                    <option value="valor_fixo">Valor Fixo (R$)</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Taxa de Administração</span>
                    <span class="label-text-alt text-base-content/50">{{ form.tipo_taxa_administracao === 'percentual' ? '%' : 'R$' }}</span>
                </label>
                <input
                    v-model="form.valor_taxa_administracao"
                    type="number"
                    step="0.01"
                    min="0"
                    class="input input-bordered w-full"
                    placeholder="0"
                />
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Dia do Repasse</span></label>
                <input
                    v-model="form.dia_repasse"
                    type="number"
                    min="1"
                    max="31"
                    class="input input-bordered w-full"
                    placeholder="Ex: 15"
                />
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Forma de Repasse</span></label>
                <select v-model="form.forma_repasse" class="select select-bordered w-full">
                    <option value="pix">PIX</option>
                    <option value="transferencia">Transferência Bancária</option>
                    <option value="dinheiro">Dinheiro</option>
                </select>
            </div>
        </div>

        <div class="divider text-sm text-base-content/50">Dados Bancários do Proprietário</div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Banco</span></label>
                <input v-model="form.banco" type="text" class="input input-bordered w-full" placeholder="Ex: Itaú" />
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Agência</span></label>
                <input v-model="form.agencia" type="text" class="input input-bordered w-full" placeholder="0000" />
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Conta</span></label>
                <input v-model="form.conta" type="text" class="input input-bordered w-full" placeholder="00000-0" />
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Tipo de Conta</span></label>
                <select v-model="form.tipo_conta" class="select select-bordered w-full">
                    <option value="">—</option>
                    <option value="corrente">Corrente</option>
                    <option value="poupanca">Poupança</option>
                </select>
            </div>

            <div class="form-control md:col-span-2">
                <label class="label"><span class="label-text font-medium">Chave PIX</span></label>
                <input v-model="form.pix_key" type="text" class="input input-bordered w-full" placeholder="CPF, e-mail, celular ou chave aleatória" />
            </div>
        </div>

        <div class="flex justify-between pt-2">
            <button type="button" class="btn btn-ghost" @click="emit('prev')">← Anterior</button>
            <button type="button" class="btn btn-primary" @click="emit('next')">Próximo →</button>
        </div>
    </div>
</template>

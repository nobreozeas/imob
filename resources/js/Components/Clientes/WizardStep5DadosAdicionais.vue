<script setup lang="ts">
import { computed } from 'vue';
import type { FormularioClienteData } from '@/types/cliente';

const props = defineProps<{
    form: FormularioClienteData;
    errors: Partial<Record<string, string>>;
    processing?: boolean;
}>();

defineEmits<{ submit: [] }>();

const temProprietario = computed(() => props.form.papeis.includes('proprietario'));
const temInquilino = computed(() => props.form.papeis.includes('inquilino'));

const nomeExibicao = computed(() =>
    props.form.tipo_pessoa === 'fisica' ? props.form.nome : props.form.razao_social
);

function formatarData(val: string): string {
    if (!val) return '—';
    const [y, m, d] = val.split('-');
    return `${d}/${m}/${y}`;
}
</script>

<template>
    <div class="space-y-6">
        <!-- Resumo -->
        <div class="card bg-base-200/50 border border-base-300">
            <div class="card-body py-4">
                <h3 class="font-semibold text-sm text-base-content/70 uppercase tracking-wide mb-3">Resumo dos dados</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-base-content/50">Nome / Razão Social</span>
                        <p class="font-medium">{{ nomeExibicao || '—' }}</p>
                    </div>
                    <div>
                        <span class="text-base-content/50">Documento</span>
                        <p class="font-medium">{{ form.cpf || form.cnpj || '—' }}</p>
                    </div>
                    <div v-if="form.data_nascimento">
                        <span class="text-base-content/50">Nascimento</span>
                        <p class="font-medium">{{ formatarData(form.data_nascimento) }}</p>
                    </div>
                    <div v-if="form.telefone_principal">
                        <span class="text-base-content/50">Telefone</span>
                        <p class="font-medium">{{ form.telefone_principal }}</p>
                    </div>
                    <div v-if="form.email_principal">
                        <span class="text-base-content/50">E-mail</span>
                        <p class="font-medium">{{ form.email_principal }}</p>
                    </div>
                    <div v-if="form.cidade">
                        <span class="text-base-content/50">Cidade</span>
                        <p class="font-medium">{{ form.cidade }}{{ form.estado ? ` — ${form.estado}` : '' }}</p>
                    </div>
                    <div>
                        <span class="text-base-content/50">Papéis</span>
                        <p class="font-medium">
                            {{ form.papeis.map(p => p === 'proprietario' ? 'Proprietário' : 'Inquilino').join(' e ') || '—' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dados de Proprietário -->
        <div v-if="temProprietario" class="card bg-base-100 border-2 border-info/30">
            <div class="card-body space-y-4">
                <h3 class="font-semibold text-info">Dados de Proprietário <span class="text-base-content/40 text-sm font-normal">(opcional)</span></h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Banco</span></label>
                        <input type="text" class="input input-bordered w-full" v-model="form.proprietario.banco" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Agência</span></label>
                        <input type="text" class="input input-bordered w-full" v-model="form.proprietario.agencia" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Conta</span></label>
                        <input type="text" class="input input-bordered w-full" v-model="form.proprietario.conta" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Tipo de Conta</span></label>
                        <select class="select select-bordered w-full" v-model="form.proprietario.tipo_conta">
                            <option value="">Selecione</option>
                            <option value="corrente">Conta Corrente</option>
                            <option value="poupanca">Conta Poupança</option>
                            <option value="pagamento">Conta de Pagamento</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Tipo de Chave PIX</span></label>
                        <select class="select select-bordered w-full" v-model="form.proprietario.tipo_chave_pix">
                            <option value="">Selecione</option>
                            <option value="cpf">CPF</option>
                            <option value="cnpj">CNPJ</option>
                            <option value="email">E-mail</option>
                            <option value="telefone">Telefone</option>
                            <option value="aleatoria">Chave Aleatória</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Chave PIX</span></label>
                        <input type="text" class="input input-bordered w-full" v-model="form.proprietario.chave_pix" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">% de Administração</span></label>
                        <input type="number" class="input input-bordered w-full" min="0" max="100" step="0.01"
                            v-model="form.proprietario.percentual_administracao" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Preferência de Recebimento</span></label>
                        <input type="text" class="input input-bordered w-full" v-model="form.proprietario.preferencia_recebimento" />
                    </div>
                    <div class="form-control">
                        <label class="label cursor-pointer gap-2 justify-start">
                            <input type="checkbox" class="checkbox checkbox-info"
                                v-model="form.proprietario.emite_nota_fiscal" />
                            <span class="label-text font-medium">Emite Nota Fiscal / Recibo</span>
                        </label>
                    </div>
                    <div class="form-control md:col-span-2">
                        <label class="label"><span class="label-text font-medium">Observações de Repasse</span></label>
                        <textarea class="textarea textarea-bordered w-full" rows="2"
                            v-model="form.proprietario.observacoes_repasse" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Dados de Inquilino -->
        <div v-if="temInquilino" class="card bg-base-100 border-2 border-success/30">
            <div class="card-body space-y-4">
                <h3 class="font-semibold text-success">Dados de Inquilino <span class="text-base-content/40 text-sm font-normal">(opcional)</span></h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Profissão</span></label>
                        <input type="text" class="input input-bordered w-full" v-model="form.inquilino.profissao" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Renda Mensal Aproximada</span></label>
                        <input type="number" class="input input-bordered w-full" min="0" step="0.01"
                            v-model="form.inquilino.renda_mensal" />
                    </div>
                    <div class="form-control md:col-span-2">
                        <label class="label"><span class="label-text font-medium">Local de Trabalho</span></label>
                        <input type="text" class="input input-bordered w-full" v-model="form.inquilino.local_trabalho" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Telefone Comercial</span></label>
                        <input type="text" class="input input-bordered w-full" v-model="form.inquilino.telefone_comercial" />
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-medium">Contato de Emergência</span></label>
                        <input type="text" class="input input-bordered w-full" placeholder="Nome e telefone"
                            v-model="form.inquilino.contato_emergencia" />
                    </div>
                    <div class="form-control md:col-span-2">
                        <label class="label"><span class="label-text font-medium">Observações Cadastrais</span></label>
                        <textarea class="textarea textarea-bordered w-full" rows="2"
                            v-model="form.inquilino.observacoes_cadastrais" />
                    </div>
                    <div class="form-control md:col-span-2">
                        <label class="label"><span class="label-text font-medium">Restrições</span></label>
                        <textarea class="textarea textarea-bordered textarea-error w-full" rows="2"
                            v-model="form.inquilino.restricoes" />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="button" class="btn btn-success btn-lg" :disabled="processing" @click="$emit('submit')">
                <span v-if="processing" class="loading loading-spinner loading-sm" />
                Salvar Cliente
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { FormularioClienteData } from '@/types/cliente';

defineProps<{
    form: FormularioClienteData;
    errors: Partial<Record<string, string>>;
}>();

defineEmits<{ next: [] }>();
</script>

<template>
    <div class="space-y-4">
        <p class="text-base-content/60 text-sm">Todos os campos de contato são opcionais.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Telefone Principal</span></label>
                <input type="text" class="input input-bordered w-full" placeholder="(00) 00000-0000"
                    v-model="form.telefone_principal" />
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">WhatsApp</span></label>
                <input type="text" class="input input-bordered w-full" placeholder="(00) 00000-0000"
                    v-model="form.whatsapp" />
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Telefone Secundário</span></label>
                <input type="text" class="input input-bordered w-full" v-model="form.telefone_secundario" />
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">E-mail Principal</span></label>
                <input type="email" class="input input-bordered w-full"
                    :class="{ 'input-error': errors.email_principal }"
                    v-model="form.email_principal" />
                <p v-if="errors.email_principal" class="text-error text-xs mt-1">{{ errors.email_principal }}</p>
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text font-medium">E-mail Alternativo</span></label>
                <input type="email" class="input input-bordered w-full"
                    :class="{ 'input-error': errors.email_alternativo }"
                    v-model="form.email_alternativo" />
                <p v-if="errors.email_alternativo" class="text-error text-xs mt-1">{{ errors.email_alternativo }}</p>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="button" class="btn btn-primary" @click="$emit('next')">Próximo →</button>
        </div>
    </div>
</template>

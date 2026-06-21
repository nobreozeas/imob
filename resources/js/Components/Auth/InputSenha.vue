<script setup lang="ts">
import { ref } from 'vue';
import { Eye, EyeOff } from 'lucide-vue-next';

defineProps<{
    modelValue: string;
    placeholder?: string;
    label?: string;
    error?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const visivel = ref(false);
</script>

<template>
    <div class="form-control w-full">
        <label v-if="label" class="label">
            <span class="label-text font-medium">{{ label }}</span>
        </label>
        <div class="relative">
            <input
                :type="visivel ? 'text' : 'password'"
                :value="modelValue"
                :placeholder="placeholder ?? '••••••••'"
                :class="['input input-bordered w-full pr-12', error ? 'input-error' : '']"
                @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
            />
            <button
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-base-content/50 hover:text-base-content transition-colors"
                @click="visivel = !visivel"
            >
                <EyeOff v-if="visivel" class="w-5 h-5" />
                <Eye v-else class="w-5 h-5" />
            </button>
        </div>
        <label v-if="error" class="label">
            <span class="label-text-alt text-error">{{ error }}</span>
        </label>
    </div>
</template>

<script setup lang="ts">
import { useContratoStatus } from '@/composables/useContratoStatus';
import type { ContratoEncargo } from '@/types/contrato';

defineProps<{ encargos: ContratoEncargo[] }>();

const { labelTipoEncargo, labelResponsavel } = useContratoStatus();
</script>

<template>
    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <h3 class="card-title text-base">Encargos e Responsabilidades</h3>
            <div v-if="encargos.length === 0" class="text-sm text-base-content/60">
                Nenhum encargo configurado.
            </div>
            <table v-else class="table table-sm">
                <thead>
                    <tr>
                        <th>Encargo</th>
                        <th>Responsável</th>
                        <th>Observação</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="encargo in encargos" :key="encargo.id">
                        <td class="font-medium">{{ labelTipoEncargo(encargo.tipo_encargo) }}</td>
                        <td>
                            <span
                                class="badge badge-sm"
                                :class="{
                                    'badge-info': encargo.responsavel === 'proprietario',
                                    'badge-warning': encargo.responsavel === 'inquilino',
                                    'badge-ghost': encargo.responsavel === 'nao_se_aplica',
                                }"
                            >
                                {{ labelResponsavel(encargo.responsavel) }}
                            </span>
                        </td>
                        <td class="text-sm text-base-content/60">{{ encargo.observacao ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

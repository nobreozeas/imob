<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { ImovelHistoricoPaginado, TipoEventoHistoricoImovel } from '@/types/imovel';

defineProps<{ historicos: ImovelHistoricoPaginado }>();

const labels: Record<TipoEventoHistoricoImovel, string> = {
    criacao: 'Criado',
    atualizacao: 'Atualizado',
    alteracao_status: 'Status alterado',
    foto_adicionada: 'Foto adicionada',
    foto_removida: 'Foto removida',
    documento_adicionado: 'Documento adicionado',
    documento_removido: 'Documento removido',
    exclusao: 'Excluído',
    restauracao: 'Restaurado',
};

function formatarData(data: string): string {
    return new Date(data).toLocaleString('pt-BR');
}
</script>

<template>
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h3 class="font-semibold text-base-content/70 uppercase tracking-wide text-sm mb-3">Histórico</h3>

            <ul v-if="historicos.data.length > 0" class="space-y-3">
                <li v-for="evento in historicos.data" :key="evento.id" class="border-l-2 border-base-200 pl-3">
                    <div class="flex items-center gap-2">
                        <span class="badge badge-ghost badge-sm">{{ labels[evento.tipo_evento] }}</span>
                        <span class="text-xs text-base-content/40">{{ formatarData(evento.created_at) }}</span>
                    </div>
                    <p class="text-sm mt-0.5">{{ evento.descricao }}</p>
                    <p v-if="evento.usuario" class="text-xs text-base-content/40">por {{ evento.usuario.name }}</p>
                </li>
            </ul>
            <p v-else class="text-sm text-base-content/40">Nenhum evento registrado.</p>

            <div v-if="historicos.last_page > 1" class="flex justify-center gap-1 mt-4">
                <template v-for="link in historicos.links" :key="link.label">
                    <Link v-if="link.url"
                          :href="link.url"
                          preserve-scroll
                          class="btn btn-xs"
                          :class="link.active ? 'btn-primary' : 'btn-ghost'"
                          v-html="link.label" />
                    <span v-else class="btn btn-xs btn-disabled" v-html="link.label" />
                </template>
            </div>
        </div>
    </div>
</template>

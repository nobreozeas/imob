import type { TipoImovel, FinalidadeImovel, StatusImovel } from '@/types/imovel';

export function useImovelStatus() {
    function labelStatus(status: StatusImovel): string {
        const labels: Record<StatusImovel, string> = {
            disponivel:    'Disponível',
            reservado:     'Reservado',
            alugado:       'Alugado',
            em_manutencao: 'Em Manutenção',
            inativo:       'Inativo',
        };
        return labels[status] ?? status;
    }

    function corStatus(status: StatusImovel): string {
        const cores: Record<StatusImovel, string> = {
            disponivel:    'badge-success',
            reservado:     'badge-warning',
            alugado:       'badge-info',
            em_manutencao: 'badge-error',
            inativo:       'badge-ghost',
        };
        return cores[status] ?? 'badge-ghost';
    }

    function labelTipo(tipo: TipoImovel): string {
        const labels: Record<TipoImovel, string> = {
            apartamento: 'Apartamento',
            casa:        'Casa',
            comercial:   'Comercial',
            sala:        'Sala',
            galpao:      'Galpão',
            terreno:     'Terreno',
            rural:       'Rural',
            outro:       'Outro',
        };
        return labels[tipo] ?? tipo;
    }

    function labelFinalidade(finalidade: FinalidadeImovel): string {
        const labels: Record<FinalidadeImovel, string> = {
            aluguel:       'Aluguel',
            venda:         'Venda',
            aluguel_venda: 'Aluguel / Venda',
        };
        return labels[finalidade] ?? finalidade;
    }

    function corFinalidade(finalidade: FinalidadeImovel): string {
        const cores: Record<FinalidadeImovel, string> = {
            aluguel:       'badge-primary',
            venda:         'badge-secondary',
            aluguel_venda: 'badge-accent',
        };
        return cores[finalidade] ?? 'badge-ghost';
    }

    return { labelStatus, corStatus, labelTipo, labelFinalidade, corFinalidade };
}

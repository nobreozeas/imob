import type { PapelCliente, StatusCliente } from '@/types/cliente';

export function useClienteStatus() {
    function labelStatus(status: StatusCliente): string {
        return status === 'ativo' ? 'Ativo' : 'Inativo';
    }

    function corStatus(status: StatusCliente): string {
        return status === 'ativo' ? 'badge-success' : 'badge-error';
    }

    function labelPapel(papel: PapelCliente): string {
        return papel === 'proprietario' ? 'Proprietário' : 'Inquilino';
    }

    function corPapel(papel: PapelCliente): string {
        return papel === 'proprietario' ? 'badge-info' : 'badge-success';
    }

    return { labelStatus, corStatus, labelPapel, corPapel };
}

import type {
    StatusContrato,
    TipoContrato,
    IndiceReajuste,
    ResponsavelEncargo,
    TipoCaucao,
    StatusCaucao,
    TipoEventoHistorico,
    TipoEncargo,
    TipoDocumentoContrato,
} from '@/types/contrato';

export function useContratoStatus() {
    function labelStatus(status: StatusContrato): string {
        const labels: Record<StatusContrato, string> = {
            rascunho: 'Rascunho',
            aguardando_assinatura: 'Aguardando Assinatura',
            ativo: 'Ativo',
            encerrado: 'Encerrado',
            rescindido: 'Rescindido',
            cancelado: 'Cancelado',
        };
        return labels[status] ?? status;
    }

    function corStatus(status: StatusContrato): string {
        const cores: Record<StatusContrato, string> = {
            rascunho: 'badge-ghost',
            aguardando_assinatura: 'badge-warning',
            ativo: 'badge-success',
            encerrado: 'badge-info',
            rescindido: 'badge-warning',
            cancelado: 'badge-error',
        };
        return cores[status] ?? 'badge-ghost';
    }

    function labelTipoContrato(tipo: TipoContrato): string {
        const labels: Record<TipoContrato, string> = {
            residencial: 'Residencial',
            comercial: 'Comercial',
            temporada: 'Temporada',
        };
        return labels[tipo] ?? tipo;
    }

    function labelIndice(indice: IndiceReajuste): string {
        const labels: Record<IndiceReajuste, string> = {
            igpm: 'IGP-M',
            ipca: 'IPCA',
            inpc: 'INPC',
            fixo: 'Fixo',
        };
        return labels[indice] ?? indice;
    }

    function labelResponsavel(responsavel: ResponsavelEncargo): string {
        const labels: Record<ResponsavelEncargo, string> = {
            proprietario: 'Proprietário',
            inquilino: 'Inquilino',
            nao_se_aplica: 'N/A',
        };
        return labels[responsavel] ?? responsavel;
    }

    function labelTipoEncargo(tipo: TipoEncargo): string {
        const labels: Record<TipoEncargo, string> = {
            iptu: 'IPTU',
            condominio: 'Condomínio',
            agua: 'Água',
            energia: 'Energia',
            gas: 'Gás',
            internet: 'Internet',
            outros: 'Outros',
        };
        return labels[tipo] ?? tipo;
    }

    function labelTipoCaucao(tipo: TipoCaucao | null | ''): string {
        if (!tipo) return '-';
        const labels: Record<TipoCaucao, string> = {
            dinheiro: 'Dinheiro',
            imovel: 'Imóvel',
            fiador: 'Fiador',
            seguro_fianca: 'Seguro Fiança',
            deposito_bancario: 'Depósito Bancário',
        };
        return labels[tipo as TipoCaucao] ?? tipo;
    }

    function labelStatusCaucao(status: StatusCaucao): string {
        const labels: Record<StatusCaucao, string> = {
            recebida: 'Recebida',
            devolvida: 'Devolvida',
            retida_parcialmente: 'Retida Parcialmente',
            retida_totalmente: 'Retida Totalmente',
        };
        return labels[status] ?? status;
    }

    function corStatusCaucao(status: StatusCaucao): string {
        const cores: Record<StatusCaucao, string> = {
            recebida: 'badge-warning',
            devolvida: 'badge-success',
            retida_parcialmente: 'badge-warning',
            retida_totalmente: 'badge-error',
        };
        return cores[status] ?? 'badge-ghost';
    }

    function labelTipoEvento(tipo: TipoEventoHistorico): string {
        const labels: Record<TipoEventoHistorico, string> = {
            criacao: 'Criação',
            ativacao: 'Ativação',
            cancelamento: 'Cancelamento',
            encerramento: 'Encerramento',
            rescisao: 'Rescisão',
            alteracao: 'Alteração',
            documento_adicionado: 'Documento Adicionado',
            assinatura_pendente: 'Enviado para Assinatura',
        };
        return labels[tipo] ?? tipo;
    }

    function iconeEvento(tipo: TipoEventoHistorico): string {
        const icones: Record<TipoEventoHistorico, string> = {
            criacao: '📄',
            ativacao: '✅',
            cancelamento: '❌',
            encerramento: '🔒',
            rescisao: '⚠️',
            alteracao: '✏️',
            documento_adicionado: '📎',
            assinatura_pendente: '✍️',
        };
        return icones[tipo] ?? '•';
    }

    function labelTipoDocumento(tipo: TipoDocumentoContrato): string {
        const labels: Record<TipoDocumentoContrato, string> = {
            contrato_assinado: 'Contrato Assinado',
            laudo_vistoria: 'Laudo de Vistoria',
            comprovante_caucao: 'Comprovante de Caução',
            outros: 'Outros',
        };
        return labels[tipo] ?? tipo;
    }

    return {
        labelStatus,
        corStatus,
        labelTipoContrato,
        labelIndice,
        labelResponsavel,
        labelTipoEncargo,
        labelTipoCaucao,
        labelStatusCaucao,
        corStatusCaucao,
        labelTipoEvento,
        iconeEvento,
        labelTipoDocumento,
    };
}

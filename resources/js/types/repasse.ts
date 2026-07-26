export type StatusRepasseProprietario = 'pendente' | 'pago' | 'cancelado';

export interface RepasseProprietario {
    id: string;
    contrato_id: string;
    imovel_id: string;
    proprietario_id: string;
    parcela_aluguel_id: string;
    valor_bruto: string;
    valor_taxa_administracao: string;
    valor_liquido: string;
    status: StatusRepasseProprietario;
    data_pagamento: string | null;
    forma_pagamento: 'pix' | 'transferencia' | 'dinheiro' | null;
    motivo_cancelamento: string | null;
    parcela?: { mes_referencia: number; ano_referencia: number };
}

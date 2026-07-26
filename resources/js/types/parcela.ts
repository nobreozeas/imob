export type StatusParcelaAluguel = 'pendente' | 'pago' | 'vencido' | 'cancelado' | 'pago_parcial';

export type FormaPagamento =
    | 'pix'
    | 'dinheiro'
    | 'cartao_credito'
    | 'cartao_debito'
    | 'transferencia'
    | 'boleto'
    | 'outro';

export interface ParcelaAluguel {
    id: string;
    contrato_id: string;
    mes_referencia: number;
    ano_referencia: number;
    data_vencimento: string;
    valor_aluguel: string;
    valor_encargos: string;
    valor_multa_atraso: string;
    valor_juros_atraso: string;
    valor_desconto: string;
    valor_total: string;
    valor_pago: string;
    data_pagamento: string | null;
    forma_pagamento: FormaPagamento | null;
    status: StatusParcelaAluguel;
    observacoes: string | null;
}

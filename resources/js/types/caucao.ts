export type TipoMovimentacaoCaucao =
    | 'recebimento'
    | 'devolucao'
    | 'abatimento'
    | 'retencao_parcial'
    | 'retencao_integral'
    | 'ajuste';

export interface MovimentacaoCaucao {
    id: string;
    caucao_contrato_id: string;
    tipo_movimentacao: TipoMovimentacaoCaucao;
    valor: string;
    data_movimentacao: string;
    forma_movimentacao: string | null;
    descricao: string | null;
    referencia_debito: string | null;
    criado_por: string | null;
    created_at: string;
    criador?: { id: string; name: string };
}

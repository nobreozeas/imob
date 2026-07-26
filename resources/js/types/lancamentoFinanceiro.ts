import type { CategoriaFinanceira } from './categoriaFinanceira';
import type { FormaPagamento } from './parcela';

export type TipoLancamentoFinanceiro = 'entrada' | 'saida';

export type StatusLancamentoFinanceiro = 'pendente' | 'pago' | 'cancelado' | 'estornado';

export type OrigemLancamentoFinanceiro =
    | 'manual'
    | 'pagamento_aluguel'
    | 'repasse_proprietario'
    | 'caucao'
    | 'movimentacao_caucao'
    | 'despesa'
    | 'receita_diversa'
    | 'ajuste';

export interface LancamentoFinanceiro {
    id: string;
    codigo: string;
    tipo: TipoLancamentoFinanceiro;
    categoria_financeira_id: string;
    contrato_id: string | null;
    parcela_aluguel_id: string | null;
    repasse_proprietario_id: string | null;
    caucao_contrato_id: string | null;
    movimentacao_caucao_id: string | null;
    imovel_id: string | null;
    cliente_id: string | null;
    descricao: string | null;
    valor: string;
    data_vencimento: string | null;
    data_pagamento: string | null;
    forma_pagamento: FormaPagamento | null;
    status: StatusLancamentoFinanceiro;
    origem: OrigemLancamentoFinanceiro;
    observacoes: string | null;
    motivo_cancelamento: string | null;
    motivo_estorno: string | null;
    criado_por: string | null;
    pago_por: string | null;
    cancelado_por: string | null;
    estornado_por: string | null;
    created_at: string;
    categoria?: CategoriaFinanceira;
    contrato?: { id: string; numero: string };
    imovel?: { id: string; codigo: string; titulo: string };
    cliente?: { id: string; nome: string | null; razao_social: string | null };
}

export interface LancamentoFinanceiroFiltros {
    busca?: string;
    tipo?: TipoLancamentoFinanceiro | '';
    categoria_financeira_id?: string;
    status?: StatusLancamentoFinanceiro | '';
    forma_pagamento?: string;
    origem?: OrigemLancamentoFinanceiro | '';
    contrato_id?: string;
    imovel_id?: string;
    cliente_id?: string;
    data_vencimento_de?: string;
    data_vencimento_ate?: string;
    data_pagamento_de?: string;
    data_pagamento_ate?: string;
}

export interface LancamentoFinanceiroPaginado {
    data: LancamentoFinanceiro[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: { url: string | null; label: string; active: boolean }[];
}

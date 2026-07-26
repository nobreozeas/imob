export type TipoCategoriaFinanceira = 'entrada' | 'saida';

export interface CategoriaFinanceira {
    id: string;
    nome: string;
    tipo: TipoCategoriaFinanceira;
    slug: string;
    descricao: string | null;
    ativa: boolean;
}

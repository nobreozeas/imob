export type TipoPessoa = 'fisica' | 'juridica';
export type StatusCliente = 'ativo' | 'inativo';
export type PapelCliente = 'proprietario' | 'inquilino';
export type TipoChavePix = 'cpf' | 'cnpj' | 'email' | 'telefone' | 'aleatoria';

export interface ClientePapel {
    id: string;
    cliente_id: string;
    papel: PapelCliente;
}

export interface ClienteDadosProprietario {
    id: string;
    cliente_id: string;
    banco: string | null;
    agencia: string | null;
    conta: string | null;
    tipo_conta: string | null;
    chave_pix: string | null;
    tipo_chave_pix: TipoChavePix | null;
    percentual_administracao: string | null;
    emite_nota_fiscal: boolean;
    preferencia_recebimento: string | null;
    observacoes_repasse: string | null;
}

export interface ClienteDadosInquilino {
    id: string;
    cliente_id: string;
    profissao: string | null;
    renda_mensal: string | null;
    local_trabalho: string | null;
    telefone_comercial: string | null;
    contato_emergencia: string | null;
    observacoes_cadastrais: string | null;
    restricoes: string | null;
}

export interface Cliente {
    id: string;
    tipo_pessoa: TipoPessoa;
    nome: string | null;
    razao_social: string | null;
    nome_fantasia: string | null;
    cpf: string | null;
    cnpj: string | null;
    rg: string | null;
    data_nascimento: string | null;
    telefone_principal: string | null;
    whatsapp: string | null;
    telefone_secundario: string | null;
    email_principal: string | null;
    email_alternativo: string | null;
    cep: string | null;
    logradouro: string | null;
    numero: string | null;
    complemento: string | null;
    bairro: string | null;
    cidade: string | null;
    estado: string | null;
    ponto_referencia: string | null;
    observacoes: string | null;
    status: StatusCliente;
    nome_exibicao: string;
    documento: string | null;
    papeis: ClientePapel[];
    dados_proprietario: ClienteDadosProprietario | null;
    dados_inquilino: ClienteDadosInquilino | null;
    created_at: string;
    updated_at: string;
}

export interface ClienteFiltros {
    busca?: string;
    tipo_pessoa?: TipoPessoa | '';
    papel?: PapelCliente | '';
    status?: StatusCliente | '';
    cidade?: string;
    ordenar_por?: string;
    direcao?: 'asc' | 'desc';
}

export interface ClientePaginado {
    data: Cliente[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

export interface FormularioClienteData {
    tipo_pessoa: TipoPessoa;
    nome: string;
    razao_social: string;
    nome_fantasia: string;
    cpf: string;
    cnpj: string;
    rg: string;
    data_nascimento: string;
    telefone_principal: string;
    whatsapp: string;
    telefone_secundario: string;
    email_principal: string;
    email_alternativo: string;
    cep: string;
    logradouro: string;
    numero: string;
    complemento: string;
    bairro: string;
    cidade: string;
    estado: string;
    ponto_referencia: string;
    observacoes: string;
    papeis: PapelCliente[];
    proprietario: {
        banco: string;
        agencia: string;
        conta: string;
        tipo_conta: string;
        chave_pix: string;
        tipo_chave_pix: TipoChavePix | '';
        percentual_administracao: string;
        emite_nota_fiscal: boolean;
        preferencia_recebimento: string;
        observacoes_repasse: string;
    };
    inquilino: {
        profissao: string;
        renda_mensal: string;
        local_trabalho: string;
        telefone_comercial: string;
        contato_emergencia: string;
        observacoes_cadastrais: string;
        restricoes: string;
    };
}

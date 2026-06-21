export type TipoImovel = 'apartamento' | 'casa' | 'comercial' | 'sala' | 'galpao' | 'terreno' | 'rural' | 'outro';
export type FinalidadeImovel = 'aluguel' | 'venda' | 'aluguel_venda';
export type StatusImovel = 'disponivel' | 'reservado' | 'alugado' | 'em_manutencao' | 'inativo';
export type ResponsavelCusto = 'proprietario' | 'inquilino';

export interface ImovelCaracteristicas {
    id: string;
    imovel_id: string;
    area_total: string | null;
    area_construida: string | null;
    quartos: number;
    suites: number;
    banheiros: number;
    vagas_garagem: number;
    mobiliado: boolean;
    aceita_pet: boolean;
    possui_piscina: boolean;
    possui_quintal: boolean;
    possui_varanda: boolean;
    outras_caracteristicas: string | null;
}

export interface ImovelDadosComerciais {
    id: string;
    imovel_id: string;
    valor_aluguel: string | null;
    valor_venda: string | null;
    valor_condominio: string | null;
    valor_iptu: string | null;
    condominio_incluso: boolean;
    responsavel_iptu: ResponsavelCusto | null;
    responsavel_agua: ResponsavelCusto | null;
    responsavel_energia: ResponsavelCusto | null;
    responsavel_condominio: ResponsavelCusto | null;
    valor_caucao_sugerido: string | null;
    observacoes_comerciais: string | null;
}

export interface ImovelFoto {
    id: string;
    imovel_id: string;
    caminho: string;
    nome_original: string;
    is_principal: boolean;
    ordem: number;
    url: string;
}

export interface ImovelDocumento {
    id: string;
    imovel_id: string;
    caminho: string;
    nome_original: string;
    tipo: string | null;
    url: string;
}

export interface ProprietarioOpcao {
    id: string;
    nome: string | null;
    razao_social: string | null;
    tipo_pessoa: 'fisica' | 'juridica';
}

export interface CorretorOpcao {
    id: string;
    name: string;
}

export interface Imovel {
    id: string;
    codigo: string;
    titulo: string;
    tipo: TipoImovel;
    finalidade: FinalidadeImovel;
    status: StatusImovel;
    proprietario_id: string;
    corretor_id: string | null;
    descricao: string | null;
    cep: string | null;
    logradouro: string | null;
    numero: string | null;
    complemento: string | null;
    bairro: string | null;
    cidade: string | null;
    estado: string | null;
    ponto_referencia: string | null;
    proprietario: ProprietarioOpcao | null;
    corretor: CorretorOpcao | null;
    caracteristicas: ImovelCaracteristicas | null;
    dados_comerciais: ImovelDadosComerciais | null;
    fotos: ImovelFoto[];
    documentos: ImovelDocumento[];
    foto_principal: ImovelFoto | null;
    created_at: string;
    updated_at: string;
}

export interface ImovelFiltros {
    busca?: string;
    tipo?: TipoImovel | '';
    finalidade?: FinalidadeImovel | '';
    status?: StatusImovel | '';
    cidade?: string;
    bairro?: string;
    proprietario_id?: string;
    ordenar_por?: string;
    direcao?: 'asc' | 'desc';
}

export interface ImovelPaginado {
    data: Imovel[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

export interface FormularioImovelData {
    // Step 1
    titulo: string;
    codigo: string;
    tipo: TipoImovel;
    finalidade: FinalidadeImovel;
    status: StatusImovel;
    proprietario_id: string;
    corretor_id: string;
    descricao: string;
    // Step 2
    cep: string;
    logradouro: string;
    numero: string;
    complemento: string;
    bairro: string;
    cidade: string;
    estado: string;
    ponto_referencia: string;
    // Step 3
    caracteristicas: {
        area_total: string;
        area_construida: string;
        quartos: number;
        suites: number;
        banheiros: number;
        vagas_garagem: number;
        mobiliado: boolean;
        aceita_pet: boolean;
        possui_piscina: boolean;
        possui_quintal: boolean;
        possui_varanda: boolean;
        outras_caracteristicas: string;
    };
    // Step 4
    dados_comerciais: {
        valor_aluguel: string;
        valor_venda: string;
        valor_condominio: string;
        valor_iptu: string;
        condominio_incluso: boolean;
        responsavel_iptu: ResponsavelCusto | '';
        responsavel_agua: ResponsavelCusto | '';
        responsavel_energia: ResponsavelCusto | '';
        responsavel_condominio: ResponsavelCusto | '';
        valor_caucao_sugerido: string;
        observacoes_comerciais: string;
    };
    // Step 5
    fotos_novas: File[];
    foto_principal_id: string;
    fotos_remover: string[];
    documentos_novos: File[];
    tipos_documentos: string[];
    documentos_remover: string[];
}

export type StatusContrato =
    | 'rascunho'
    | 'aguardando_assinatura'
    | 'ativo'
    | 'vencido'
    | 'encerrado'
    | 'rescindido'
    | 'cancelado';

export type TipoContrato = 'residencial' | 'comercial' | 'temporada';
export type ObjetivoContrato = 'aluguel' | 'temporada';
export type IndiceReajuste = 'igpm' | 'ipca' | 'inpc' | 'fixo';
export type FormaRepasse = 'pix' | 'transferencia' | 'dinheiro';
export type TipoTaxaAdministracao = 'percentual' | 'valor_fixo';
export type TipoConta = 'corrente' | 'poupanca';

export type TipoEncargo = 'iptu' | 'condominio' | 'agua' | 'energia' | 'gas' | 'internet' | 'outros';
export type ResponsavelEncargo = 'proprietario' | 'inquilino' | 'nao_se_aplica';

export type TipoCaucao = 'dinheiro' | 'imovel' | 'fiador' | 'seguro_fianca' | 'deposito_bancario';
export type StatusCaucao = 'recebida' | 'devolvida' | 'retida_parcialmente' | 'retida_totalmente';
export type BaseCalculoRescisao = 'alugueis_restantes' | 'valor_fixo';

export type TipoDocumentoContrato = 'contrato_assinado' | 'laudo_vistoria' | 'comprovante_caucao' | 'outros';
export type SolicitadoPorRescisao = 'locatario' | 'locador' | 'imobiliaria' | 'acordo';
export type DestinoImovelRescisao = 'disponivel' | 'inativo';
export type AcaoParcelasFuturas = 'cancelar_parcelas_futuras' | 'manter_parcelas_futuras';

export type TipoEventoHistorico =
    | 'criacao'
    | 'ativacao'
    | 'cancelamento'
    | 'encerramento'
    | 'rescisao'
    | 'alteracao'
    | 'documento_adicionado'
    | 'assinatura_pendente'
    | 'renovado_para'
    | 'criacao_por_renovacao'
    | 'repasse_cancelado';

export interface ContratoEncargo {
    id: string;
    contrato_id: string;
    tipo_encargo: TipoEncargo;
    responsavel: ResponsavelEncargo;
    valor_estimado: string | null;
    cobrar_junto_aluguel: boolean;
    observacao: string | null;
}

export interface ContratoCaucao {
    id: string;
    contrato_id: string;
    possui_caucao: boolean;
    tipo_caucao: TipoCaucao | null;
    valor_caucao: string | null;
    saldo_atual: string;
    data_recebimento_caucao: string | null;
    status_caucao: StatusCaucao | null;
    valor_devolvido: string | null;
    data_devolucao_caucao: string | null;
    observacao_caucao: string | null;
    movimentacoes?: import('./caucao').MovimentacaoCaucao[];
}

export interface ContratoMultas {
    id: string;
    contrato_id: string;
    possui_multa_atraso: boolean;
    percentual_multa_atraso: string | null;
    valor_juros_dia: string | null;
    dias_tolerancia_atraso: number | null;
    possui_multa_rescisao: boolean;
    percentual_multa_rescisao: string | null;
    base_calculo_rescisao: BaseCalculoRescisao | null;
}

export interface ContratoRescisao {
    id: string;
    contrato_id: string;
    data_rescisao: string;
    motivo: string;
    solicitado_por: SolicitadoPorRescisao;
    meses_restantes: number | null;
    valor_multa_rescisao: string | null;
    valor_desconto: string | null;
    valor_final_multa: string | null;
    debitos_em_aberto: string;
    valor_caucao_retida: string | null;
    valor_caucao_abatida: string | null;
    valor_caucao_devolvida: string | null;
    destino_imovel: DestinoImovelRescisao;
    acao_parcelas_futuras: AcaoParcelasFuturas;
    observacoes: string | null;
}

export interface ContratoRenovacao {
    id: string;
    contrato_original_id: string;
    novo_contrato_id: string;
    data_renovacao: string;
    valor_aluguel_anterior: string;
    valor_aluguel_novo: string;
    manter_encargos: boolean;
    manter_regras_multa: boolean;
    gerar_novas_parcelas: boolean;
    caucao_acao: 'manter' | 'devolver' | 'complementar' | null;
    observacoes: string | null;
}

export interface ContratoDocumento {
    id: string;
    contrato_id: string;
    caminho: string;
    nome_original: string;
    tipo: TipoDocumentoContrato;
    criado_por: string | null;
    url: string;
    created_at: string;
    criador?: { id: string; name: string };
}

export interface ContratoHistorico {
    id: string;
    contrato_id: string;
    tipo_evento: TipoEventoHistorico;
    descricao: string;
    dados_anteriores: Record<string, unknown> | null;
    dados_novos: Record<string, unknown> | null;
    usuario_id: string | null;
    created_at: string;
    usuario?: { id: string; name: string };
}

export interface ImovelOpcao {
    id: string;
    codigo: string;
    titulo: string;
    proprietario_id: string;
    proprietario_nome: string | null;
}

export interface InquilinoOpcao {
    id: string;
    nome: string | null;
    razao_social: string | null;
    tipo_pessoa: 'fisica' | 'juridica';
    cpf: string | null;
    cnpj: string | null;
}

export interface CorretorOpcao {
    id: string;
    name: string;
}

export interface ContratoLocacao {
    id: string;
    numero: string;
    status: StatusContrato;
    tipo_contrato: TipoContrato;
    objetivo_contrato: ObjetivoContrato;
    imovel_id: string;
    proprietario_id: string;
    inquilino_id: string;
    corretor_id: string | null;
    contrato_anterior_id: string | null;
    criado_por: string;
    data_inicio: string;
    data_fim: string | null;
    dia_vencimento: number;
    duracao_meses: number | null;
    valor_aluguel: string;
    indice_reajuste: IndiceReajuste;
    periodicidade_reajuste: number;
    data_primeiro_reajuste: string | null;
    tipo_taxa_administracao: TipoTaxaAdministracao;
    valor_taxa_administracao: string;
    gerar_parcelas_automaticamente: boolean;
    quantidade_parcelas: number | null;
    dia_repasse: number | null;
    forma_repasse: FormaRepasse;
    banco: string | null;
    agencia: string | null;
    conta: string | null;
    tipo_conta: TipoConta | null;
    pix_key: string | null;
    data_encerramento: string | null;
    motivo_encerramento: string | null;
    observacoes: string | null;
    permite_edicao_completa: boolean;
    created_at: string;
    updated_at: string;
    // relationships
    imovel?: { id: string; codigo: string; titulo: string };
    proprietario?: { id: string; nome: string | null; razao_social: string | null; tipo_pessoa: string };
    inquilino?: { id: string; nome: string | null; razao_social: string | null; tipo_pessoa: string };
    corretor?: { id: string; name: string };
    encargos?: ContratoEncargo[];
    caucao?: ContratoCaucao;
    multas?: ContratoMultas;
    documentos?: ContratoDocumento[];
    historicos?: ContratoHistorico[];
    parcelas?: import('./parcela').ParcelaAluguel[];
    repasses?: import('./repasse').RepasseProprietario[];
    rescisao?: ContratoRescisao;
}

export interface ContratoFiltros {
    busca?: string;
    status?: StatusContrato;
    imovel_id?: string;
    proprietario_id?: string;
    inquilino_id?: string;
    data_inicio_de?: string;
    data_inicio_ate?: string;
}

export interface ContratoPaginado {
    data: ContratoLocacao[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

export interface FormularioContratoData {
    numero: string;
    tipo_contrato: TipoContrato;
    objetivo_contrato: ObjetivoContrato;
    acao: 'rascunho' | 'ativar';
    // Partes
    imovel_id: string;
    proprietario_id: string;
    inquilino_id: string;
    corretor_id: string;
    // Dados da locação
    data_inicio: string;
    data_fim: string;
    dia_vencimento: string;
    duracao_meses: string;
    // Valores
    valor_aluguel: string;
    indice_reajuste: IndiceReajuste;
    periodicidade_reajuste: string;
    data_primeiro_reajuste: string;
    // Repasse
    tipo_taxa_administracao: TipoTaxaAdministracao;
    valor_taxa_administracao: string;
    dia_repasse: string;
    forma_repasse: FormaRepasse;
    banco: string;
    agencia: string;
    conta: string;
    tipo_conta: TipoConta | '';
    pix_key: string;
    // Observações
    observacoes: string;
    // Parcelas
    gerar_parcelas_automaticamente: boolean;
    quantidade_parcelas: string;
    // Encargos
    encargos: {
        tipo_encargo: TipoEncargo;
        responsavel: ResponsavelEncargo;
        valor_estimado: string;
        cobrar_junto_aluguel: boolean;
        observacao: string;
    }[];
    // Caução
    caucao: {
        possui_caucao: boolean;
        tipo_caucao: TipoCaucao | '';
        valor_caucao: string;
        data_recebimento_caucao: string;
    };
    // Multas
    multas: {
        possui_multa_atraso: boolean;
        percentual_multa_atraso: string;
        valor_juros_dia: string;
        dias_tolerancia_atraso: string;
        possui_multa_rescisao: boolean;
        percentual_multa_rescisao: string;
        base_calculo_rescisao: BaseCalculoRescisao | '';
    };
    // Documentos
    documentos_novos: File[];
    tipos_documentos: string[];
}

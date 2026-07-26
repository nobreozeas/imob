export type StatusUsuario = 'ativo' | 'inativo' | 'bloqueado';

export interface Perfil {
    id: string;
    name: string;
}

export interface Usuario {
    id: string;
    name: string;
    email: string;
    status: StatusUsuario;
    deve_alterar_senha: boolean;
    ultimo_acesso_em: string | null;
    criado_por: string | null;
    created_at: string;
    roles: Perfil[];
}

export interface UsuarioPaginado {
    data: Usuario[];
    links: { url: string | null; label: string; active: boolean }[];
    from: number;
    to: number;
    total: number;
    last_page: number;
}

export interface UsuarioForm {
    name: string;
    email: string;
    role: string;
    status: StatusUsuario;
}

export interface UsuarioFiltros {
    busca?: string;
    role?: string;
    status?: string;
    primeiro_acesso_pendente?: boolean | string;
}

export interface Permissao {
    id: string;
    name: string;
}

export interface Perfil {
    id: string;
    name: string;
    guard_name: string;
    users_count?: number;
    permissions: Permissao[];
}

export interface User {
    id: number;
    name: string;
    email: string;
    status: 'ativo' | 'inativo' | 'bloqueado' | 'excluido';
    deve_alterar_senha: boolean;
}

declare module '@inertiajs/core' {
    interface PageProps {
        auth: {
            user: User | null;
        };
        flash: {
            status?: string;
            error?: string;
        };
    }
}

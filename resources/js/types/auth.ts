export interface LoginForm {
    email: string;
    password: string;
    remember: boolean;
}

export interface PrimeiroAcessoForm {
    password: string;
    password_confirmation: string;
}

export interface RecuperacaoSenhaForm {
    email: string;
}

export interface RedefinirSenhaForm {
    token: string;
    email: string;
    password: string;
    password_confirmation: string;
}

export interface AlteracaoSenhaForm {
    current_password: string;
    password: string;
    password_confirmation: string;
}

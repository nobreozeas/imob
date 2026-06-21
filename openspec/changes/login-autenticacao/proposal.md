## Why

O sistema precisa de um módulo de autenticação seguro para controlar quem acessa a plataforma de gestão imobiliária, garantindo que apenas usuários cadastrados e autorizados possam utilizar os recursos internos. Este é o módulo base sobre o qual todos os demais módulos dependem.

## What Changes

- Implementação da tela de login com email e senha
- Implementação do fluxo de logout com invalidação de sessão
- Fluxo de primeiro acesso com troca obrigatória de senha temporária
- Geração automática de senha temporária ao criar usuário
- Envio de email de primeiro acesso com instruções e credenciais temporárias
- Fluxo de recuperação de senha via link enviado por email
- Tela de alteração de senha para usuário autenticado
- Middleware de proteção de rotas internas (bloqueio sem autenticação)
- Bloqueio de acesso para usuários inativos ou excluídos
- Controle do campo `deve_alterar_senha` para forçar troca de senha
- Registro de `ultimo_acesso_em` e `primeiro_acesso_em`

## Capabilities

### New Capabilities

- `autenticacao-login`: Tela de login com email e senha, validação de credenciais, verificação de status do usuário, redirecionamento pós-login
- `autenticacao-logout`: Encerramento de sessão, invalidação de token, redirecionamento para login
- `autenticacao-primeiro-acesso`: Fluxo de troca obrigatória de senha no primeiro login com senha temporária
- `autenticacao-recuperacao-senha`: Solicitação de redefinição de senha por email, link com expiração, definição de nova senha
- `autenticacao-alteracao-senha`: Alteração de senha pelo usuário autenticado informando a senha atual
- `autenticacao-protecao-rotas`: Middleware que bloqueia acesso não autenticado e redireciona para login

### Modified Capabilities

<!-- Nenhuma spec existente a ser modificada -->

## Impact

- **Backend**: Criação de controllers (`AuthController`), middleware de autenticação, Form Requests de validação, Service de autenticação (`AuthService`), notificação de email para primeiro acesso e recuperação de senha
- **Frontend**: Páginas Vue/Inertia para login, troca de senha obrigatória, recuperação de senha e alteração de senha; layout sem sidebar para páginas de autenticação
- **Banco de dados**: Campos adicionais na tabela `users`: `deve_alterar_senha`, `ultimo_acesso_em`, `primeiro_acesso_em`, `convite_enviado_em`, `criado_por`; uso da tabela nativa `password_reset_tokens` do Laravel
- **Rotas**: Rotas públicas (`/login`, `/esqueci-senha`, `/redefinir-senha`) e proteção de todas as rotas internas com middleware `auth`
- **Email**: Configuração de Mailables para email de primeiro acesso e recuperação de senha

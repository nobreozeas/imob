## ADDED Requirements

### Requirement: Usuário pode encerrar a sessão
O sistema SHALL permitir que o usuário autenticado encerre sua sessão de forma segura pelo menu de perfil.

#### Scenario: Logout com sucesso
- **WHEN** o usuário clica em "Sair" no menu de perfil
- **THEN** o sistema invalida a sessão atual, remove os dados de autenticação e redireciona para a tela de login

#### Scenario: Acesso a páginas internas após logout
- **WHEN** o usuário tenta acessar uma rota interna após ter feito logout (ex: usando o botão "voltar" do navegador)
- **THEN** o sistema redireciona para a tela de login sem exibir conteúdo interno

### Requirement: Logout invalida completamente a sessão
O sistema SHALL garantir que a sessão seja completamente destruída no logout, prevenindo reuso de dados de sessão.

#### Scenario: Token CSRF regenerado após logout
- **WHEN** o usuário realiza logout
- **THEN** o sistema SHALL regenerar o token CSRF e destruir todos os dados da sessão atual

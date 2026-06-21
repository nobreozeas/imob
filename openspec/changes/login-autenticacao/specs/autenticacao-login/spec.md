## ADDED Requirements

### Requirement: Usuário pode fazer login com email e senha
O sistema SHALL permitir que um usuário cadastrado acesse o sistema informando seu email e senha na tela de login.

#### Scenario: Login com credenciais válidas e usuário ativo
- **WHEN** o usuário informa email e senha corretos de um usuário com status `ativo` e `deve_alterar_senha = false`
- **THEN** o sistema autentica o usuário, registra `ultimo_acesso_em`, e redireciona para o dashboard

#### Scenario: Login com credenciais inválidas
- **WHEN** o usuário informa email ou senha incorretos
- **THEN** o sistema exibe a mensagem "Email ou senha inválidos." e mantém o usuário na tela de login

#### Scenario: Login com usuário inativo
- **WHEN** o usuário informa credenciais válidas de um usuário com status `inativo`
- **THEN** o sistema exibe a mensagem "Seu acesso está inativo. Entre em contato com o administrador." e não autentica

#### Scenario: Login com usuário excluído (soft delete)
- **WHEN** o usuário informa credenciais válidas de um usuário com `deleted_at` preenchido
- **THEN** o sistema exibe a mensagem "Email ou senha inválidos." (não revela que o usuário foi excluído)

#### Scenario: Login com deve_alterar_senha = true
- **WHEN** o usuário informa credenciais válidas e `deve_alterar_senha = true`
- **THEN** o sistema autentica o usuário e redireciona para a tela obrigatória de troca de senha

### Requirement: Tela de login segue o design estabelecido
O sistema SHALL exibir a tela de login com layout split-screen: painel de marketing à esquerda (logo, nome do sistema, benefícios) e formulário à direita (campos de email, senha, "Lembrar acesso", botão "Entrar", link "Esqueci minha senha").

#### Scenario: Tela de login não exibe opções de cadastro público
- **WHEN** o usuário acessa a tela de login
- **THEN** a tela NÃO SHALL exibir links ou botões para "Criar conta", "Cadastro" ou "Registrar"

#### Scenario: Tela de login exibe campos corretos
- **WHEN** o usuário acessa a rota `/login`
- **THEN** o sistema SHALL exibir os campos: email, senha (com toggle de visibilidade), checkbox "Lembrar acesso", botão "Entrar" e link "Esqueci minha senha"

### Requirement: Usuário autenticado é redirecionado ao tentar acessar login
O sistema SHALL redirecionar o usuário já autenticado para o dashboard se tentar acessar a tela de login.

#### Scenario: Usuário autenticado acessa /login
- **WHEN** um usuário autenticado acessa a rota `/login`
- **THEN** o sistema redireciona para o dashboard sem exibir a tela de login

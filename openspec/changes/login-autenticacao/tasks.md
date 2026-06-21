## 1. Banco de Dados e Models

- [x] 1.1 Criar migration para adicionar campos na tabela `users`: `status` (enum: ativo, inativo, bloqueado, excluido), `deve_alterar_senha` (boolean, default true), `ultimo_acesso_em` (timestamp nullable), `primeiro_acesso_em` (timestamp nullable), `criado_por` (uuid nullable, FK users)
- [x] 1.2 Atualizar o model `User` com os novos campos em `$fillable`, cast corretos (`deve_alterar_senha` como boolean, timestamps), constantes de status e scope `ativo()`
- [x] 1.3 Criar seeder `AdminUserSeeder` para criar o usuário administrador inicial com `deve_alterar_senha = true`

## 2. Infraestrutura de Autenticação (Backend)

- [x] 2.1 Criar `AuthService` em `app/Services/Auth/` com métodos: `tentarLogin()`, `logout()`, `gerarSenhaTemporaria()`, `registrarAcesso()`
- [x] 2.2 Criar middleware `MustChangePassword` que redireciona para `/primeiro-acesso` se `deve_alterar_senha = true`
- [x] 2.3 Registrar o middleware `MustChangePassword` no `bootstrap/app.php` com alias `must.change.password`
- [x] 2.4 Configurar rotas em `routes/auth.php`: rotas públicas (`/login`, `/esqueci-senha`, `/redefinir-senha/{token}`, `/primeiro-acesso`) e rotas de autenticação com middleware `auth` + `must.change.password`
- [x] 2.5 Incluir `routes/auth.php` no `bootstrap/app.php`

## 3. Controllers de Autenticação (Backend)

- [x] 3.1 Criar `LoginController` com métodos `create()` (exibir tela) e `store()` (processar login): validar credenciais, verificar status ativo, registrar `ultimo_acesso_em`, redirecionar conforme `deve_alterar_senha`
- [x] 3.2 Criar `LogoutController` com método `destroy()`: invalidar sessão, regenerar token CSRF, redirecionar para `/login`
- [x] 3.3 Criar `PrimeiroAcessoController` com métodos `create()` e `store()`: validar nova senha (diferente da temporária, critérios mínimos), atualizar senha, marcar `deve_alterar_senha = false`, registrar `primeiro_acesso_em`
- [x] 3.4 Criar `RecuperacaoSenhaController` com métodos `create()` e `store()`: enviar email com link (mensagem genérica independente do email existir), usar broker nativo do Laravel
- [x] 3.5 Criar `RedefinirSenhaController` com métodos `create()` (formulário) e `store()` (processar): validar token, atualizar senha, invalidar token, redirecionar para login
- [x] 3.6 Criar `AlteracaoSenhaController` com método `update()`: verificar senha atual, validar nova senha (diferente da atual, critérios mínimos), atualizar

## 4. Form Requests (Backend)

- [x] 4.1 Criar `LoginRequest` com regras de validação para email e senha
- [x] 4.2 Criar `PrimeiroAcessoRequest` com regras: nova senha (min 8 chars, regex letra+número, diferente da atual)
- [x] 4.3 Criar `RecuperacaoSenhaRequest` com regra de email válido
- [x] 4.4 Criar `RedefinirSenhaRequest` com regras de nova senha e confirmação
- [x] 4.5 Criar `AlteracaoSenhaRequest` com regras de senha atual, nova senha e confirmação

## 5. Notificações e Emails (Backend)

- [x] 5.1 Criar `PrimeiroAcessoNotification` (Mailable) com template em português: nome do usuário, email, senha temporária, link do sistema
- [x] 5.2 Criar `RecuperacaoSenhaNotification` (Mailable/override do nativo) em português com link de recuperação e prazo de validade
- [x] 5.3 Configurar `AppServiceProvider` para usar as notificações customizadas de reset de senha
- [x] 5.4 Verificar configuração de MAIL_* no `.env.example` e documentar variáveis necessárias

## 6. Layout e Componentes Vue (Frontend)

- [x] 6.1 Criar layout `AuthLayout.vue` em `resources/js/Layouts/`: split-screen com painel esquerdo de marketing (logo ImobGestor, tagline, lista de benefícios com ícones Lucide) e área direita para slot de formulário
- [x] 6.2 Criar componente `InputSenha.vue` reutilizável com campo de senha e botão de toggle de visibilidade (ícone olho aberto/fechado via Lucide)
- [x] 6.3 Criar componente `FormErro.vue` para exibir mensagens de erro inline nos formulários de autenticação

## 7. Páginas Vue/Inertia (Frontend)

- [x] 7.1 Criar página `resources/js/Pages/Auth/Login.vue`: formulário com email, `InputSenha`, checkbox "Lembrar acesso", botão "Entrar", link "Esqueci minha senha"; usar `AuthLayout`; exibir erros do Inertia
- [x] 7.2 Criar página `resources/js/Pages/Auth/PrimeiroAcesso.vue`: formulário com dois campos `InputSenha` (nova senha + confirmação), botão "Definir senha"; mensagem explicativa sobre o primeiro acesso; usar `AuthLayout`
- [x] 7.3 Criar página `resources/js/Pages/Auth/RecuperacaoSenha.vue`: campo de email, botão "Enviar instruções", exibição de mensagem de status genérica; usar `AuthLayout`
- [x] 7.4 Criar página `resources/js/Pages/Auth/RedefinirSenha.vue`: dois campos `InputSenha` (nova senha + confirmação), campo oculto com token, botão "Redefinir senha"; usar `AuthLayout`
- [x] 7.5 Criar página `resources/js/Pages/Auth/AlteracaoSenha.vue`: três campos `InputSenha` (atual, nova, confirmação), botão "Alterar senha"; acessível via `Minha Conta` no layout principal

## 8. Tipagem TypeScript (Frontend)

- [x] 8.1 Criar types em `resources/js/types/auth.ts`: `LoginForm`, `PrimeiroAcessoForm`, `RecuperacaoSenhaForm`, `RedefinirSenhaForm`, `AlteracaoSenhaForm`
- [x] 8.2 Adicionar tipo `User` com os campos necessários para autenticação: `id`, `nome`, `email`, `status`, `deve_alterar_senha`
- [x] 8.3 Atualizar `resources/js/types/index.d.ts` com o tipo `User` no objeto `auth` do Inertia shared data

## 9. Integração e Testes Manuais

- [x] 9.1 Testar fluxo completo de primeiro acesso: criar usuário via seeder, fazer login com senha temporária, trocar senha, acessar dashboard
- [x] 9.2 Testar fluxo de recuperação de senha: solicitar via email, acessar link, redefinir senha, fazer login
- [x] 9.3 Testar bloqueio de usuário inativo: inativar usuário logado via banco, tentar acessar rota interna
- [x] 9.4 Testar redirecionamento de rota protegida: acessar `/imoveis` sem login, fazer login, verificar redirecionamento de volta para `/imoveis`
- [x] 9.5 Testar responsividade das páginas de autenticação em mobile (layout split-screen colapsa para coluna única)

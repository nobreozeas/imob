# Funcionalidade 01 — Autenticação e Acesso Inicial

**Produto:** Sistema de Gestão Imobiliária  
**Módulo:** Autenticação, Usuários, Perfis e Permissões  
**Versão:** 1.0 — MVP  
**Data:** 29/06/2026  
**Status:** Especificação funcional e técnica inicial  

---

## 1. Objetivo da funcionalidade

Permitir que apenas usuários internos previamente cadastrados pela imobiliária acessem o sistema de forma segura.

A funcionalidade deve contemplar login, logout, recuperação de senha, primeiro acesso obrigatório com troca de senha, bloqueio de usuários inativos e estrutura inicial para perfis e permissões.

Esta é a primeira funcionalidade recomendada porque todo o restante do sistema dependerá de usuários autenticados, controle de acesso e identificação do usuário responsável pelas ações executadas na plataforma.

---

## 2. Contexto no MVP

O sistema não terá autocadastro. Todo usuário deverá ser criado por um administrador ou por outro usuário com permissão para gerenciar usuários.

Ao cadastrar um usuário, o sistema deverá gerar um acesso inicial seguro e enviar por email. No primeiro login, o usuário será obrigado a definir uma nova senha antes de acessar qualquer módulo do sistema.

A autenticação será usada como base para:

- Controle de acesso ao dashboard.
- Controle de acesso aos cadastros.
- Registro de usuário responsável por ações críticas.
- Proteção das telas financeiras.
- Proteção das ações de contrato, pagamentos, repasses e caução.
- Aplicação futura de auditoria.

---

## 3. Escopo da primeira entrega

### 3.1 Incluído

- Tela de login.
- Logout.
- Criação inicial da estrutura de usuários.
- Criação inicial da estrutura de perfis.
- Criação inicial da estrutura de permissões.
- Perfis padrão do sistema.
- Cadastro de usuário interno.
- Geração de acesso inicial aleatório.
- Envio de email de primeiro acesso.
- Obrigatoriedade de troca de senha no primeiro acesso.
- Recuperação de senha por email.
- Alteração de senha do usuário autenticado.
- Bloqueio de acesso para usuários inativos.
- Middleware para impedir acesso sem autenticação.
- Middleware para impedir acesso antes da troca de senha.
- Redirecionamento pós-login conforme estado do usuário.

### 3.2 Não incluído nesta primeira entrega

- Cadastro público de usuários.
- Login social.
- Autenticação em duas etapas.
- Single Sign-On.
- Multiempresa.
- Auditoria completa de todas as entidades.
- Tela avançada de matriz visual de permissões.
- Histórico detalhado de sessões.
- Controle de dispositivos conectados.

---

## 4. Usuários envolvidos

### 4.1 Administrador

Usuário responsável por criar usuários, vincular perfis, ativar, inativar e reenviar acesso inicial.

### 4.2 Usuário interno

Usuário operacional do sistema, podendo ser financeiro, atendente ou corretor. Recebe o acesso por email e deve trocar a senha no primeiro login.

### 4.3 Sistema

Responsável por gerar token ou senha temporária, enviar email, validar credenciais, aplicar regras de primeiro acesso e bloquear usuários sem permissão.

---

## 5. Perfis padrão

O MVP deve iniciar com quatro perfis principais:

| Perfil | Descrição |
|---|---|
| Administrador | Acesso total ao sistema. |
| Financeiro | Acesso aos módulos financeiros, pagamentos, repasses, caução e relatórios financeiros. |
| Atendente | Acesso operacional a cadastros e contratos, sem acesso financeiro sensível. |
| Corretor | Acesso limitado a imóveis e contratos vinculados. |

---

## 6. Permissões iniciais

As permissões devem ser granulares e organizadas por módulo.

### 6.1 Ações padrão

- visualizar
- criar
- editar
- excluir
- restaurar
- exportar

### 6.2 Ações específicas futuras

- registrar_pagamento
- marcar_repasse_como_pago
- movimentar_caucao
- rescindir_contrato
- renovar_contrato

### 6.3 Módulos iniciais para permissões

- dashboard
- usuarios
- perfis
- imoveis
- proprietarios
- inquilinos
- corretores
- contratos
- pagamentos
- repasses
- caucao
- financeiro
- relatorios

---

## 7. Regras de negócio

### 7.1 Cadastro de usuário

1. Apenas usuários autorizados podem cadastrar novos usuários.
2. Não deve existir autocadastro.
3. O email do usuário deve ser único.
4. Todo usuário deve estar vinculado a um perfil.
5. Ao criar um usuário, o sistema deve gerar um acesso inicial seguro.
6. O usuário recém-criado deve iniciar com `primeiro_acesso_pendente = true`.
7. O usuário deve receber email com instruções de primeiro acesso.
8. O usuário deve iniciar com status `ativo`, salvo se o administrador escolher criar como inativo.
9. Senhas nunca devem ser armazenadas em texto puro.
10. O sistema deve armazenar apenas hash seguro da senha ou token temporário criptografado/hasheado.

### 7.2 Primeiro acesso

1. O usuário recebe o acesso inicial por email.
2. O usuário acessa o sistema usando o link ou credenciais temporárias.
3. Após autenticar, se `primeiro_acesso_pendente = true`, o sistema deve redirecionar para a tela de troca de senha.
4. O usuário não pode acessar dashboard, cadastros ou qualquer módulo antes de trocar a senha.
5. A nova senha deve atender aos critérios mínimos de segurança.
6. Após trocar a senha, o sistema deve alterar `primeiro_acesso_pendente = false`.
7. Após a troca, o usuário pode acessar o sistema conforme seu perfil.

### 7.3 Login

1. Apenas usuários ativos podem acessar o sistema.
2. Email e senha são obrigatórios.
3. Credenciais inválidas devem retornar mensagem genérica.
4. Usuário inativo deve receber mensagem de acesso bloqueado.
5. Usuário com primeiro acesso pendente deve ser redirecionado para troca de senha.
6. Usuário autenticado e sem pendência deve ser redirecionado para o dashboard.
7. Após login bem-sucedido, registrar `ultimo_login_em`.

### 7.4 Logout

1. O usuário autenticado pode sair do sistema.
2. A sessão deve ser encerrada.
3. O usuário deve ser redirecionado para a tela de login.

### 7.5 Recuperação de senha

1. Usuário informa email cadastrado.
2. O sistema envia link de redefinição se o email existir.
3. A resposta da tela deve ser genérica para evitar enumeração de usuários.
4. O link deve possuir validade limitada.
5. A nova senha deve ser validada conforme política de senha.
6. Após redefinir a senha, o usuário poderá acessar normalmente.

### 7.6 Usuário inativo

1. Usuário inativo não pode acessar o sistema.
2. Usuário inativo não deve ser excluído fisicamente.
3. O registro deve permanecer para histórico e rastreabilidade.
4. Um administrador pode reativar o usuário.

### 7.7 Permissões

1. Cada usuário pertence a um perfil.
2. Cada perfil possui permissões associadas.
3. Rotas protegidas devem validar autenticação e autorização.
4. A interface deve ocultar ações que o usuário não tem permissão para executar.
5. O backend deve sempre validar permissões, mesmo que a ação esteja oculta no frontend.

---

## 8. Fluxos principais

## 8.1 Fluxo de cadastro de usuário

```text
Administrador acessa Usuários
   ↓
Clica em Novo usuário
   ↓
Informa nome, email, perfil e status
   ↓
Sistema valida dados
   ↓
Sistema cria usuário
   ↓
Sistema gera acesso inicial
   ↓
Sistema envia email de primeiro acesso
   ↓
Usuário fica com primeiro acesso pendente
```

## 8.2 Fluxo de primeiro acesso

```text
Usuário recebe email de primeiro acesso
   ↓
Acessa o link ou tela de login
   ↓
Informa credenciais temporárias
   ↓
Sistema autentica usuário
   ↓
Sistema identifica primeiro acesso pendente
   ↓
Redireciona para troca obrigatória de senha
   ↓
Usuário define nova senha
   ↓
Sistema salva senha com hash seguro
   ↓
Sistema remove pendência de primeiro acesso
   ↓
Usuário acessa dashboard
```

## 8.3 Fluxo de login comum

```text
Usuário acessa login
   ↓
Informa email e senha
   ↓
Sistema valida credenciais
   ↓
Sistema verifica status do usuário
   ↓
Sistema verifica pendência de primeiro acesso
   ↓
Sistema redireciona para dashboard
```

## 8.4 Fluxo de recuperação de senha

```text
Usuário acessa Esqueci minha senha
   ↓
Informa email
   ↓
Sistema gera link temporário
   ↓
Sistema envia email de recuperação
   ↓
Usuário acessa link
   ↓
Define nova senha
   ↓
Sistema atualiza senha
   ↓
Usuário pode fazer login
```

---

## 9. Telas necessárias

## 9.1 Tela de login

### Objetivo

Permitir acesso ao sistema por usuários cadastrados.

### Campos

- Email.
- Senha.
- Lembrar-me, opcional.

### Ações

- Entrar.
- Esqueci minha senha.

### Comportamentos

- Exibir erro genérico para credenciais inválidas.
- Redirecionar usuário autenticado para dashboard.
- Redirecionar usuário com primeiro acesso pendente para troca de senha.
- Bloquear usuário inativo.

---

## 9.2 Tela de recuperação de senha

### Objetivo

Permitir que usuário solicite redefinição de senha por email.

### Campos

- Email.

### Ações

- Enviar link de recuperação.
- Voltar para login.

### Comportamentos

- Mensagem de sucesso genérica.
- Não informar se o email existe ou não.

---

## 9.3 Tela de redefinição de senha

### Objetivo

Permitir que o usuário defina uma nova senha através de link temporário.

### Campos

- Nova senha.
- Confirmação da nova senha.

### Ações

- Redefinir senha.

### Validações

- Senha obrigatória.
- Confirmação obrigatória.
- Senhas devem coincidir.
- Senha deve cumprir política mínima.

---

## 9.4 Tela de troca obrigatória de senha

### Objetivo

Obrigar novo usuário a definir uma senha própria no primeiro acesso.

### Campos

- Nova senha.
- Confirmação da nova senha.

### Ações

- Salvar nova senha.
- Sair.

### Comportamentos

- Não exibir menu lateral completo.
- Não permitir acesso a outras rotas antes da troca.
- Após sucesso, redirecionar para dashboard.

---

## 9.5 Listagem de usuários

### Objetivo

Permitir que administradores visualizem e gerenciem usuários internos.

### Colunas

- Nome.
- Email.
- Perfil.
- Status.
- Primeiro acesso pendente.
- Último login.
- Ações.

### Filtros

- Busca geral.
- Perfil.
- Status.
- Primeiro acesso pendente.

### Ações

- Novo usuário.
- Editar.
- Ativar.
- Inativar.
- Reenviar acesso inicial.
- Excluir logicamente.

---

## 9.6 Formulário de usuário

### Objetivo

Cadastrar ou editar usuário interno.

### Campos

- Nome.
- Email.
- Perfil.
- Status.

### Comportamentos

- Senha não deve ser definida manualmente no cadastro comum.
- O sistema deve gerar acesso inicial automaticamente.
- Em edição, email deve continuar único.
- Em edição, permitir alteração de perfil e status.

---

## 10. Modelo de dados

## 10.1 Tabela `usuarios`

```text
id
uuid
nome
email
senha
perfil_id
status
primeiro_acesso_pendente
ultimo_login_em
email_verificado_em
remember_token
created_at
updated_at
deleted_at
```

### Observações

- `senha` deve armazenar hash seguro.
- `status` deve usar enum.
- `primeiro_acesso_pendente` deve iniciar como verdadeiro em novos usuários.
- `deleted_at` deve ser usado para Soft Delete.

---

## 10.2 Tabela `perfis`

```text
id
uuid
nome
descricao
status
created_at
updated_at
deleted_at
```

### Perfis iniciais

```text
administrador
financeiro
atendente
corretor
```

---

## 10.3 Tabela `permissoes`

```text
id
uuid
modulo
acao
nome
descricao
created_at
updated_at
```

---

## 10.4 Tabela `perfil_permissao`

```text
id
perfil_id
permissao_id
created_at
updated_at
```

---

## 10.5 Campos auxiliares opcionais para primeiro acesso

Caso a estratégia escolhida use token de primeiro acesso em vez de senha temporária, criar tabela própria:

## `tokens_primeiro_acesso`

```text
id
uuid
usuario_id
token
expira_em
utilizado_em
created_at
updated_at
```

### Recomendação

Para maior segurança, recomenda-se usar token de primeiro acesso com validade limitada, em vez de enviar senha temporária por email.

O email deve conter um link seguro para definição da primeira senha.

---

## 11. Enums sugeridos

## 11.1 Status do usuário

```text
ativo
inativo
```

## 11.2 Status do perfil

```text
ativo
inativo
```

## 11.3 Módulos de permissão

```text
dashboard
usuarios
perfis
imoveis
proprietarios
inquilinos
corretores
contratos
pagamentos
repasses
caucao
financeiro
relatorios
```

## 11.4 Ações de permissão

```text
visualizar
criar
editar
excluir
restaurar
exportar
registrar_pagamento
marcar_repasse_como_pago
movimentar_caucao
rescindir_contrato
renovar_contrato
```

---

## 12. Validações

## 12.1 Cadastro de usuário

| Campo | Regra |
|---|---|
| nome | obrigatório, texto, mínimo 3 caracteres |
| email | obrigatório, email válido, único em usuários não excluídos |
| perfil_id | obrigatório, deve existir em perfis ativos |
| status | obrigatório, ativo ou inativo |

## 12.2 Login

| Campo | Regra |
|---|---|
| email | obrigatório, email válido |
| senha | obrigatória |

## 12.3 Troca de senha

| Campo | Regra |
|---|---|
| senha | obrigatória, confirmada, política mínima |
| senha_confirmation | obrigatória, deve coincidir |

## 12.4 Política mínima de senha

Para o MVP:

```text
mínimo 8 caracteres
pelo menos uma letra
pelo menos um número
```

Recomendação futura:

```text
mínimo 10 caracteres
letra maiúscula
letra minúscula
número
caractere especial
histórico de senhas
expiração opcional
```

---

## 13. Rotas sugeridas Laravel

```php
// Autenticação
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

// Recuperação de senha
Route::get('/senha/recuperar', [PasswordResetController::class, 'create'])->name('password.request');
Route::post('/senha/email', [PasswordResetController::class, 'store'])->name('password.email');
Route::get('/senha/redefinir/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
Route::post('/senha/redefinir', [PasswordResetController::class, 'update'])->name('password.update');

// Primeiro acesso
Route::get('/primeiro-acesso', [PrimeiroAcessoController::class, 'edit'])
    ->middleware(['auth'])
    ->name('primeiro-acesso.edit');

Route::post('/primeiro-acesso', [PrimeiroAcessoController::class, 'update'])
    ->middleware(['auth'])
    ->name('primeiro-acesso.update');

// Usuários
Route::resource('usuarios', UsuarioController::class)
    ->middleware(['auth', 'primeiro.acesso.concluido']);

Route::post('usuarios/{usuario}/reenviar-acesso', [UsuarioAcessoController::class, 'reenviar'])
    ->middleware(['auth', 'primeiro.acesso.concluido'])
    ->name('usuarios.reenviar-acesso');

Route::post('usuarios/{usuario}/ativar', [UsuarioStatusController::class, 'ativar'])
    ->middleware(['auth', 'primeiro.acesso.concluido'])
    ->name('usuarios.ativar');

Route::post('usuarios/{usuario}/inativar', [UsuarioStatusController::class, 'inativar'])
    ->middleware(['auth', 'primeiro.acesso.concluido'])
    ->name('usuarios.inativar');
```

---

## 14. Middlewares necessários

## 14.1 `auth`

Protege rotas que exigem usuário autenticado.

## 14.2 `primeiro.acesso.concluido`

Impede que usuários com `primeiro_acesso_pendente = true` acessem qualquer rota interna, exceto:

- Tela de troca de senha.
- Ação de salvar nova senha.
- Logout.

## 14.3 `usuario.ativo`

Pode ser aplicado no processo de autenticação ou em middleware próprio para bloquear usuários inativos.

---

## 15. Services sugeridos

## 15.1 `CriarUsuarioService`

Responsável por criar usuário, vincular perfil, gerar token de primeiro acesso e disparar notificação.

### Responsabilidades

- Validar regra de email único.
- Criar usuário com status definido.
- Marcar primeiro acesso como pendente.
- Gerar token/link de primeiro acesso.
- Enviar notificação por email.

---

## 15.2 `GerarPrimeiroAcessoService`

Responsável por gerar token seguro de primeiro acesso.

### Responsabilidades

- Invalidar tokens anteriores não utilizados.
- Criar novo token.
- Definir validade.
- Retornar link seguro para notificação.

---

## 15.3 `ConcluirPrimeiroAcessoService`

Responsável por salvar nova senha e liberar usuário para uso normal do sistema.

### Responsabilidades

- Validar senha.
- Atualizar hash da senha.
- Marcar primeiro acesso como concluído.
- Registrar data da conclusão, se campo existir.

---

## 15.4 `ReenviarAcessoInicialService`

Responsável por reenviar acesso para usuário que ainda não concluiu o primeiro acesso.

### Responsabilidades

- Verificar se usuário existe.
- Verificar se usuário está ativo.
- Verificar se primeiro acesso ainda está pendente.
- Gerar novo token.
- Enviar novo email.

---

## 16. Notifications sugeridas

## 16.1 `PrimeiroAcessoUsuarioNotification`

Email enviado quando o usuário é criado.

### Conteúdo

- Saudação com nome do usuário.
- Nome do sistema.
- Informação de que o acesso foi criado pela imobiliária.
- Link para definir senha.
- Aviso de validade do link.
- Orientação para ignorar caso não reconheça o convite.

## 16.2 `RecuperacaoSenhaNotification`

Email enviado quando o usuário solicita recuperação de senha.

---

## 17. Estrutura frontend sugerida

## 17.1 Páginas Vue/Inertia

```text
resources/js/Pages/Auth/Login.vue
resources/js/Pages/Auth/ForgotPassword.vue
resources/js/Pages/Auth/ResetPassword.vue
resources/js/Pages/Auth/FirstAccess.vue
resources/js/Pages/Usuarios/Index.vue
resources/js/Pages/Usuarios/Create.vue
resources/js/Pages/Usuarios/Edit.vue
```

## 17.2 Componentes

```text
resources/js/Components/Auth/AuthCard.vue
resources/js/Components/Auth/PasswordInput.vue
resources/js/Components/Auth/PasswordStrengthHint.vue
resources/js/Components/Usuarios/UsuarioForm.vue
resources/js/Components/Usuarios/UsuarioStatusBadge.vue
resources/js/Components/Usuarios/PerfilBadge.vue
resources/js/Components/Shared/ConfirmActionModal.vue
```

## 17.3 Composables

```text
resources/js/Composables/useAuthForm.ts
resources/js/Composables/usePasswordRules.ts
resources/js/Composables/useConfirmAction.ts
```

---

## 18. Layout e experiência visual

## 18.1 Tela de login

A tela deve ser simples e centralizada.

### Estrutura sugerida

```text
Logo do sistema
Nome do sistema
Card de login
Campo email
Campo senha
Botão Entrar
Link Esqueci minha senha
```

## 18.2 Tela de primeiro acesso

Deve deixar claro que a troca de senha é obrigatória.

### Mensagem sugerida

```text
Antes de acessar o sistema, defina sua senha de acesso.
Essa etapa é obrigatória para proteger sua conta.
```

## 18.3 Listagem de usuários

Deve seguir padrão do PRD:

- Tabela.
- Paginação.
- Pesquisa.
- Filtros.
- Status coloridos.
- Ações por linha.

---

## 19. Estados e mensagens

## 19.1 Login inválido

```text
As credenciais informadas não conferem.
```

## 19.2 Usuário inativo

```text
Seu acesso está inativo. Entre em contato com o administrador da imobiliária.
```

## 19.3 Primeiro acesso pendente

```text
Para continuar, defina sua senha de acesso.
```

## 19.4 Recuperação enviada

```text
Se o email informado estiver cadastrado, enviaremos as instruções de recuperação.
```

## 19.5 Usuário criado

```text
Usuário criado com sucesso. O acesso inicial foi enviado por email.
```

## 19.6 Acesso reenviado

```text
Acesso inicial reenviado com sucesso.
```

---

## 20. Critérios de aceite

## 20.1 Login

- Dado um usuário ativo, quando informar email e senha válidos, então deve acessar o sistema.
- Dado um usuário ativo, quando informar senha inválida, então deve receber mensagem de erro genérica.
- Dado um usuário inativo, quando tentar login, então o acesso deve ser negado.
- Dado um usuário com primeiro acesso pendente, quando fizer login, então deve ser redirecionado para troca de senha.

## 20.2 Primeiro acesso

- Dado um usuário recém-criado, quando acessar o sistema pela primeira vez, então deve ser obrigado a trocar a senha.
- Dado um usuário com primeiro acesso pendente, quando tentar acessar o dashboard, então deve ser redirecionado para a tela de troca de senha.
- Dado um usuário que definiu nova senha válida, quando salvar, então a pendência de primeiro acesso deve ser removida.
- Dado um usuário que concluiu o primeiro acesso, quando fizer login novamente, então deve ir para o dashboard.

## 20.3 Cadastro de usuário

- Dado um administrador, quando criar usuário com dados válidos, então o sistema deve cadastrar o usuário.
- Dado um email já cadastrado, quando tentar criar outro usuário, então o sistema deve bloquear.
- Dado um novo usuário criado, então o sistema deve enviar email de primeiro acesso.
- Dado um usuário criado, então ele deve estar vinculado a um perfil.

## 20.4 Permissões

- Dado um usuário sem permissão de usuários, quando tentar acessar a listagem de usuários, então o acesso deve ser negado.
- Dado um administrador, quando acessar usuários, então deve conseguir listar, criar, editar, ativar e inativar usuários.
- Dado um usuário sem permissão, então ações protegidas não devem aparecer na interface.
- Dado uma chamada direta para rota protegida, então o backend deve bloquear se não houver permissão.

## 20.5 Recuperação de senha

- Dado um email cadastrado, quando solicitar recuperação, então o sistema deve enviar link por email.
- Dado um email inexistente, quando solicitar recuperação, então a mensagem exibida deve ser genérica.
- Dado um token expirado, quando tentar redefinir senha, então o sistema deve bloquear.
- Dado uma nova senha válida, quando redefinir, então o usuário deve conseguir fazer login.

---

## 21. Testes recomendados

## 21.1 Testes de feature backend

- Deve autenticar usuário ativo com credenciais válidas.
- Não deve autenticar usuário com senha inválida.
- Não deve autenticar usuário inativo.
- Deve redirecionar usuário com primeiro acesso pendente.
- Deve permitir troca de senha no primeiro acesso.
- Deve impedir acesso ao dashboard antes da troca de senha.
- Deve criar usuário com primeiro acesso pendente.
- Deve enviar notificação de primeiro acesso.
- Deve impedir criação de usuário com email duplicado.
- Deve aplicar Soft Delete em usuário.
- Deve bloquear rota sem permissão.

## 21.2 Testes de frontend

- Formulário de login deve validar campos obrigatórios.
- Formulário de primeiro acesso deve validar confirmação de senha.
- Listagem de usuários deve exibir status corretamente.
- Botão de novo usuário deve aparecer apenas para perfil autorizado.
- Ação de reenviar acesso deve pedir confirmação.

---

## 22. Seeds iniciais

## 22.1 Perfis

```text
Administrador
Financeiro
Atendente
Corretor
```

## 22.2 Permissões para Administrador

Administrador deve receber todas as permissões.

## 22.3 Permissões para Financeiro

```text
dashboard.visualizar
pagamentos.visualizar
pagamentos.registrar_pagamento
repasses.visualizar
repasses.marcar_repasse_como_pago
caucao.visualizar
caucao.movimentar_caucao
financeiro.visualizar
financeiro.criar
financeiro.editar
relatorios.visualizar
```

## 22.4 Permissões para Atendente

```text
dashboard.visualizar
imoveis.visualizar
imoveis.criar
imoveis.editar
proprietarios.visualizar
proprietarios.criar
proprietarios.editar
inquilinos.visualizar
inquilinos.criar
inquilinos.editar
corretores.visualizar
contratos.visualizar
contratos.criar
contratos.editar
```

## 22.5 Permissões para Corretor

```text
dashboard.visualizar
imoveis.visualizar
contratos.visualizar
relatorios.visualizar
```

---

## 23. Ordem recomendada de implementação

1. Configurar autenticação base no Laravel.
2. Criar migrations de `usuarios`, `perfis`, `permissoes` e `perfil_permissao`.
3. Criar models e relacionamentos.
4. Criar enums de status e permissões.
5. Criar seeders de perfis e permissões.
6. Criar usuário administrador inicial via seeder ou command.
7. Criar tela de login.
8. Criar fluxo de logout.
9. Criar cadastro/listagem/edição de usuários.
10. Criar service de primeiro acesso.
11. Criar notificação de primeiro acesso por email.
12. Criar tela de troca obrigatória de senha.
13. Criar middleware de primeiro acesso concluído.
14. Criar recuperação de senha.
15. Aplicar policies nas rotas de usuários e perfis.
16. Criar testes principais.

---

## 24. Checklist de pronto

- [ ] Login funcionando.
- [ ] Logout funcionando.
- [ ] Usuário inativo bloqueado.
- [ ] Usuário novo recebe acesso inicial por email.
- [ ] Usuário novo é obrigado a trocar senha no primeiro acesso.
- [ ] Usuário sem troca de senha não acessa dashboard.
- [ ] Senha é armazenada com hash seguro.
- [ ] Recuperação de senha funcionando.
- [ ] CRUD inicial de usuários funcionando.
- [ ] Perfis padrão criados.
- [ ] Permissões iniciais criadas.
- [ ] Policies aplicadas nas rotas sensíveis.
- [ ] Interface oculta ações sem permissão.
- [ ] Testes principais passando.

---

## 25. Decisões recomendadas para o MVP

### 25.1 Primeiro acesso por token

Embora a regra aceite senha aleatória, a melhor decisão para o MVP é gerar um token temporário e enviar um link para definição da senha.

Motivo:

- Evita envio de senha por email.
- Melhora a segurança.
- Mantém a experiência simples.
- Continua atendendo à regra de acesso inicial aleatório.

### 25.2 Tela de usuários simples

No MVP, a tela de usuários deve ser objetiva, sem matriz complexa de permissões. O administrador seleciona apenas o perfil. As permissões ficam pré-configuradas por perfil.

### 25.3 Permissões completas no backend

Mesmo que a interface seja simples, o backend já deve nascer com estrutura granular de permissões para evitar retrabalho nos módulos futuros.

---

## 26. Resultado esperado da funcionalidade

Ao final desta entrega, o sistema terá uma base segura para acesso interno:

- Administrador consegue criar usuários.
- Usuário criado recebe email de primeiro acesso.
- Usuário define senha no primeiro acesso.
- Usuário autenticado acessa o dashboard.
- Usuário inativo não acessa o sistema.
- Permissões iniciais protegem rotas e ações.
- A estrutura fica pronta para proteger os próximos módulos do MVP.


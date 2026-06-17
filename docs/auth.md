# Módulo de Autenticação — Sistema de Gestão Imobiliária

## 1. Objetivo do módulo

O módulo de **Autenticação** tem como objetivo controlar o acesso ao Sistema de Gestão Imobiliária, garantindo que apenas usuários previamente cadastrados e autorizados possam utilizar a plataforma.

Como o sistema manipula informações sensíveis, como imóveis, proprietários, inquilinos, contratos, recebimentos, repasses e dados financeiros, o acesso deve ser restrito, seguro e vinculado a perfis de usuário.

No MVP, a autenticação deverá contemplar:

```text
Login
Logout
Recuperação de senha
Alteração de senha
Primeiro acesso com troca obrigatória de senha
Controle de sessão
Bloqueio de acesso para usuários não autenticados
Bloqueio de acesso para usuários inativos
```

---

## 2. Regra principal

```text
O sistema não terá cadastro público de usuários.
```

Nenhum usuário poderá criar conta sozinho pela tela inicial.

O acesso será criado internamente por um usuário autorizado, geralmente um administrador, dentro do módulo de **Usuários**.

### Regras

```text
1. Apenas usuários cadastrados podem acessar o sistema.
2. Não haverá botão "Criar conta" na tela de login.
3. O primeiro usuário administrador deve ser criado previamente.
4. Usuários inativos não podem acessar o sistema.
5. Usuários excluídos ou desativados devem perder acesso imediatamente.
6. Cada usuário deve acessar o sistema com email e senha.
7. Após login válido, o usuário é redirecionado para o dashboard.
8. Após logout, o usuário retorna para a tela de login.
```

---

## 3. Cadastro de usuário com senha temporária

Ao cadastrar um novo usuário, o administrador **não deverá definir manualmente uma senha fixa**.

O sistema deverá gerar automaticamente uma **senha aleatória temporária** e enviar as instruções de primeiro acesso para o email do usuário cadastrado.

### Regras

```text
1. A senha inicial do usuário deve ser sempre gerada automaticamente pelo sistema.
2. O administrador não deve visualizar nem definir a senha manualmente.
3. O usuário deve receber no email as instruções de primeiro acesso.
4. A senha gerada deve ser temporária.
5. No primeiro login, o sistema deve obrigar a alteração da senha.
6. Enquanto a senha não for alterada, o usuário não deve acessar os módulos internos do sistema.
7. Após alterar a senha, o usuário é redirecionado para o dashboard.
8. A senha temporária não deve poder ser reutilizada depois da troca.
9. Caso o usuário não receba o email, o administrador poderá reenviar o convite de acesso.
10. Usuários inativos não devem conseguir acessar, mesmo com senha temporária válida.
```

---

## 4. Fluxo de cadastro de usuário

```text
Administrador acessa o módulo Usuários
   ↓
Clica em Novo Usuário
   ↓
Informa nome, email e perfil
   ↓
Sistema gera uma senha aleatória temporária
   ↓
Sistema envia email com os dados de primeiro acesso
   ↓
Usuário acessa o sistema pela primeira vez
   ↓
Sistema identifica que é o primeiro acesso
   ↓
Sistema obriga a troca da senha
   ↓
Usuário define uma nova senha
   ↓
Sistema libera o acesso normal
```

---

## 5. Usuários envolvidos

### Administrador

Responsável por:

```text
Cadastrar usuários
Definir perfil do usuário
Ativar usuários
Inativar usuários
Reenviar acesso
Forçar troca de senha
Gerenciar permissões
```

### Usuário interno

Pode representar diferentes papéis dentro da imobiliária, como:

```text
Atendente
Corretor
Financeiro
Administrador
```

Cada usuário acessará apenas as funcionalidades permitidas para seu perfil.

---

## 6. Fluxo geral de autenticação

```text
Usuário acessa o sistema
   ↓
Sistema exibe tela de login
   ↓
Usuário informa email e senha
   ↓
Sistema valida as credenciais
   ↓
Sistema verifica se o usuário está ativo
   ↓
Sistema verifica se deve alterar a senha
   ↓
Sistema carrega perfil e permissões
   ↓
Sistema redireciona para o dashboard
```

### Fluxo com credenciais inválidas

```text
Usuário informa email e senha
   ↓
Sistema não encontra combinação válida
   ↓
Sistema exibe mensagem de erro
   ↓
Usuário permanece na tela de login
```

### Fluxo com primeiro acesso

```text
Usuário informa email e senha temporária
   ↓
Sistema valida os dados
   ↓
Sistema identifica que deve_alterar_senha = verdadeiro
   ↓
Sistema redireciona para tela obrigatória de alteração de senha
   ↓
Usuário informa nova senha
   ↓
Sistema atualiza a senha
   ↓
Sistema marca deve_alterar_senha = falso
   ↓
Sistema registra primeiro_acesso_em
   ↓
Sistema libera acesso ao dashboard
```

---

## 7. Tela de Login

### Objetivo

Permitir que o usuário autorizado entre no sistema de forma simples e segura.

### Campos

```text
Email
Senha
Lembrar acesso
```

### Ações

```text
Entrar
Esqueci minha senha
```

### Elementos visuais recomendados

```text
Logo do sistema
Nome do sistema
Texto de apoio
Card centralizado
Mensagem de erro amigável
Link para recuperação de senha
```

### Não deve ter

```text
Criar conta
Cadastro público
Área de registro
Login social
```

### Mensagens sugeridas

Quando email ou senha estiverem incorretos:

```text
Email ou senha inválidos.
```

Quando o usuário estiver inativo:

```text
Seu acesso está inativo. Entre em contato com o administrador.
```

Quando o login for realizado com sucesso:

```text
Bem-vindo ao sistema.
```

---

## 8. Tela de cadastro de usuário

### Objetivo

Permitir que um administrador cadastre usuários internos que poderão acessar o sistema.

### Campos

```text
Nome
Email
Perfil
Status
```

### Não deve ter

```text
Campo senha
Campo confirmar senha
```

### Ações

```text
Salvar usuário
Salvar e enviar acesso
Cancelar
```

### Mensagem após cadastro

```text
Usuário cadastrado com sucesso. As instruções de primeiro acesso foram enviadas para o email informado.
```

---

## 9. Email de primeiro acesso

### Objetivo

Enviar ao usuário cadastrado as instruções necessárias para acessar o sistema pela primeira vez.

### Assunto sugerido

```text
Seu acesso ao Sistema de Gestão Imobiliária
```

### Conteúdo sugerido

```text
Olá, [Nome do usuário].

Seu acesso ao Sistema de Gestão Imobiliária foi criado.

Para entrar no sistema, utilize os dados abaixo:

Email: [email do usuário]
Senha temporária: [senha gerada]

No primeiro acesso, será obrigatório cadastrar uma nova senha.

Acesse o sistema pelo link:
[link do sistema]

Caso você não reconheça este convite, ignore esta mensagem.
```

---

## 10. Tela obrigatória de alteração de senha

### Objetivo

Obrigar o usuário a definir uma senha definitiva no primeiro acesso ou quando o administrador solicitar uma troca obrigatória.

### Campos

```text
Nova senha
Confirmar nova senha
```

Neste fluxo, não é necessário pedir a senha atual, pois o usuário já validou a senha temporária no login.

### Regras

```text
1. A nova senha deve ser diferente da senha temporária.
2. A nova senha deve atender aos critérios mínimos de segurança.
3. A confirmação deve ser igual à nova senha.
4. O usuário não pode sair dessa tela para acessar outros módulos.
5. Após a troca, o usuário será redirecionado ao dashboard.
```

---

## 11. Recuperação de senha

### Objetivo

Permitir que um usuário cadastrado solicite a redefinição de sua senha caso tenha esquecido.

### Fluxo

```text
Usuário clica em "Esqueci minha senha"
   ↓
Informa o email cadastrado
   ↓
Sistema verifica se o email existe
   ↓
Sistema envia link de recuperação por email
   ↓
Usuário acessa o link
   ↓
Define uma nova senha
   ↓
Sistema confirma a alteração
   ↓
Usuário retorna para o login
```

### Campos da tela de solicitação

```text
Email
```

### Campos da tela de nova senha

```text
Nova senha
Confirmar nova senha
```

### Regras

```text
1. Apenas emails cadastrados podem receber recuperação de senha.
2. A mensagem exibida deve ser genérica para evitar exposição de dados.
3. O link de recuperação deve ter validade limitada.
4. Após redefinir a senha, o link não deve poder ser reutilizado.
5. A nova senha deve seguir critérios mínimos de segurança.
```

### Mensagem recomendada

```text
Se este email estiver cadastrado, enviaremos as instruções para redefinição de senha.
```

---

## 12. Alteração de senha pelo usuário logado

### Objetivo

Permitir que o usuário autenticado altere sua própria senha dentro do sistema.

### Local sugerido

```text
Menu do usuário > Minha conta > Alterar senha
```

### Campos

```text
Senha atual
Nova senha
Confirmar nova senha
```

### Regras

```text
1. O usuário deve informar a senha atual.
2. A nova senha deve ser diferente da senha atual.
3. A confirmação deve ser igual à nova senha.
4. Após alterar a senha, o sistema deve informar sucesso.
5. Opcionalmente, o sistema pode encerrar outras sessões abertas.
```

### Mensagens sugeridas

```text
Senha alterada com sucesso.
```

```text
A senha atual informada está incorreta.
```

---

## 13. Logout

### Objetivo

Permitir que o usuário encerre sua sessão com segurança.

### Fluxo

```text
Usuário clica no menu de perfil
   ↓
Seleciona "Sair"
   ↓
Sistema encerra a sessão
   ↓
Sistema redireciona para o login
```

### Regras

```text
1. Após sair, o usuário não pode acessar páginas internas pelo botão voltar do navegador.
2. O sistema deve invalidar a sessão atual.
3. O usuário deve ser redirecionado para a tela de login.
```

---

## 14. Controle de sessão

### Objetivo

Garantir que apenas usuários autenticados acessem as áreas internas.

### Regras

```text
1. Usuário não autenticado não acessa dashboard.
2. Usuário não autenticado não acessa imóveis.
3. Usuário não autenticado não acessa contratos.
4. Usuário não autenticado não acessa financeiro.
5. Sessão expirada deve redirecionar para login.
6. Após login, o usuário pode ser redirecionado para a página que tentou acessar.
```

### Exemplo

```text
Usuário tenta acessar /contratos-locacao sem estar logado
   ↓
Sistema redireciona para /login
   ↓
Usuário faz login
   ↓
Sistema redireciona para /contratos-locacao
```

---

## 15. Relação com perfis e permissões

A autenticação confirma **quem é o usuário**.

As permissões definem **o que esse usuário pode fazer**.

### Exemplo

```text
Usuário autenticado: João
Perfil: Financeiro
Pode acessar: Dashboard, Financeiro, Repasses
Não pode acessar: Usuários, Perfis, Permissões
```

---

## 16. Perfis iniciais sugeridos

```text
Administrador
Financeiro
Corretor
Atendente
```

### Administrador

```text
Acessa todos os módulos.
Gerencia usuários, perfis e permissões.
Visualiza dashboard completo.
Pode criar, editar, excluir e restaurar registros.
```

### Financeiro

```text
Acessa dashboard financeiro.
Acessa recebimentos.
Acessa despesas.
Acessa repasses.
Pode registrar pagamentos.
Não gerencia usuários e permissões.
```

### Corretor

```text
Acessa imóveis.
Acessa proprietários.
Acessa inquilinos.
Acessa contratos relacionados.
Pode visualizar dados comerciais.
Não acessa configurações administrativas.
```

### Atendente

```text
Acessa cadastros básicos.
Pode consultar imóveis, proprietários e inquilinos.
Pode auxiliar em contratos.
Não acessa permissões nem configurações sensíveis.
```

---

## 17. Estados possíveis do usuário

```text
ativo
inativo
bloqueado
pendente_primeiro_acesso
excluido
```

### Ativo

```text
Pode acessar o sistema normalmente.
```

### Inativo

```text
Não pode acessar o sistema.
Pode ser reativado por um administrador.
```

### Bloqueado

```text
Não pode acessar temporariamente.
Pode ser usado futuramente para bloqueio por tentativas inválidas.
```

### Pendente primeiro acesso

```text
Usuário cadastrado, mas ainda não realizou a primeira troca de senha.
Pode acessar apenas a tela obrigatória de alteração de senha.
```

### Excluído

```text
Não aparece nas operações normais.
Deve manter histórico por segurança e auditoria.
```

---

## 18. Campos conceituais da entidade usuario

```text
id
uuid
nome
email
senha
perfil_id
status
deve_alterar_senha
ultimo_acesso_em
email_verificado_em
convite_enviado_em
primeiro_acesso_em
criado_por
created_at
updated_at
deleted_at
```

### Campo importante

```text
deve_alterar_senha
```

Indica se o usuário precisa obrigatoriamente trocar a senha antes de usar o sistema.

---

## 19. Ações administrativas sobre usuários

Na listagem de usuários, o administrador poderá executar ações como:

```text
Visualizar
Editar
Inativar
Reativar
Reenviar acesso
Forçar troca de senha
Excluir
```

### Reenviar acesso

Usado quando o usuário ainda não fez o primeiro acesso ou perdeu o email inicial.

```text
Administrador clica em Reenviar acesso
   ↓
Sistema gera nova senha temporária
   ↓
Sistema atualiza deve_alterar_senha = verdadeiro
   ↓
Sistema envia novo email ao usuário
```

### Forçar troca de senha

Usado quando o administrador quer obrigar o usuário a alterar a senha no próximo login.

```text
Administrador clica em Forçar troca de senha
   ↓
Sistema marca deve_alterar_senha = verdadeiro
   ↓
No próximo login, usuário será enviado para a tela de alteração de senha
```

---

## 20. Critérios mínimos de senha

Para o MVP, recomenda-se uma regra simples:

```text
Mínimo de 8 caracteres
Pelo menos uma letra
Pelo menos um número
```

Para versões futuras, o sistema poderá exigir:

```text
Letra maiúscula
Letra minúscula
Número
Caractere especial
Histórico de senhas anteriores
Expiração periódica
```

---

## 21. Regras de segurança do MVP

```text
1. Não permitir auto cadastro.
2. Não permitir que o administrador defina senha manualmente.
3. Gerar senha temporária aleatória ao cadastrar usuário.
4. Enviar instruções de primeiro acesso por email.
5. Obrigar troca da senha no primeiro acesso.
6. Não informar se o email existe na recuperação de senha.
7. Exigir senha segura.
8. Proteger todas as páginas internas.
9. Encerrar sessão no logout.
10. Registrar data do último acesso do usuário.
11. Impedir acesso de usuários inativos.
12. Vincular cada usuário a um perfil.
13. Aplicar permissões conforme perfil.
14. Proteger módulos administrativos.
```

---

## 22. Funcionalidades dentro do MVP

```text
Login com email e senha
Logout
Cadastro de usuário sem senha manual
Geração automática de senha temporária
Envio de acesso por email
Primeiro acesso com troca obrigatória de senha
Recuperação de senha por email
Alteração de senha pelo usuário logado
Bloqueio de usuários inativos
Redirecionamento automático para login
Controle de acesso por perfil
```

---

## 23. Funcionalidades para versões futuras

```text
Autenticação em dois fatores
Passkeys
Histórico completo de acessos
Bloqueio automático por tentativas inválidas
Política de expiração de senha
Login por certificado digital
Login por conta Google/Microsoft
Notificação de novo acesso
Controle de IP autorizado
```

---

## 24. Fluxo resumido da funcionalidade

```text
Administrador cadastra usuário
   ↓
Sistema gera senha temporária
   ↓
Sistema envia acesso por email
   ↓
Usuário faz login com senha temporária
   ↓
Sistema exige troca da senha
   ↓
Usuário define nova senha
   ↓
Sistema libera acesso
   ↓
Sistema carrega perfil e permissões
   ↓
Usuário acessa os módulos permitidos
```

---

## 25. Resultado esperado

Ao final desta funcionalidade, o sistema deve permitir que a imobiliária tenha controle seguro sobre quem acessa a plataforma, quais usuários estão ativos e quais áreas cada perfil pode utilizar.

A autenticação será a porta de entrada do sistema e deverá funcionar integrada aos módulos de **Usuários**, **Perfis** e **Permissões**.

A regra de senha temporária enviada por email torna o fluxo mais profissional, evita senhas padrão compartilhadas e garante que cada usuário defina sua própria senha no primeiro acesso.

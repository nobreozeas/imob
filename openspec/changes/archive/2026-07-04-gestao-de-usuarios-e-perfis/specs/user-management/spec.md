## ADDED Requirements

### Requirement: Listar usuários
O sistema SHALL exibir uma tabela paginada de usuários internos com colunas: Nome, Email, Perfil, Status, Primeiro acesso pendente, Último acesso, Ações.

#### Scenario: Listagem com dados
- **WHEN** um usuário com permissão `usuarios.viewAny` acessa `/usuarios`
- **THEN** o sistema exibe a tabela paginada com todos os usuários não excluídos

#### Scenario: Filtro por status
- **WHEN** o usuário aplica o filtro de status "inativo"
- **THEN** somente usuários com status `inativo` são exibidos

#### Scenario: Filtro por perfil
- **WHEN** o usuário seleciona um perfil no filtro
- **THEN** somente usuários com aquele perfil são exibidos

#### Scenario: Filtro por primeiro acesso pendente
- **WHEN** o usuário marca o filtro "primeiro acesso pendente"
- **THEN** somente usuários com `deve_alterar_senha = true` são exibidos

#### Scenario: Busca por nome ou email
- **WHEN** o usuário digita parte do nome ou email no campo de busca
- **THEN** a listagem filtra usuários que contenham o termo no nome ou email

#### Scenario: Acesso sem permissão
- **WHEN** um usuário sem permissão `usuarios.viewAny` tenta acessar `/usuarios`
- **THEN** o sistema retorna erro 403

---

### Requirement: Cadastrar usuário
O sistema SHALL permitir o cadastro de novos usuários internos com nome, email, perfil e status. Após o cadastro, SHALL gerar uma senha temporária e enviar o acesso inicial por email.

#### Scenario: Cadastro com dados válidos
- **WHEN** um administrador submete o formulário com nome, email válido, perfil ativo e status
- **THEN** o sistema cria o usuário com `deve_alterar_senha = true`, envia o email de primeiro acesso e exibe mensagem de sucesso

#### Scenario: Email duplicado
- **WHEN** o administrador informa um email já cadastrado em um usuário não excluído
- **THEN** o sistema retorna erro de validação informando que o email já está em uso

#### Scenario: Perfil inativo
- **WHEN** o administrador seleciona um perfil com status inativo
- **THEN** o sistema retorna erro de validação

#### Scenario: Acesso sem permissão
- **WHEN** um usuário sem permissão `usuarios.create` acessa o formulário de cadastro
- **THEN** o sistema retorna erro 403

---

### Requirement: Editar usuário
O sistema SHALL permitir editar o nome, perfil e status de um usuário existente. O email SHALL ser exibido mas não deve ser editável após o cadastro no MVP.

#### Scenario: Edição com dados válidos
- **WHEN** um administrador altera o nome e o perfil de um usuário e salva
- **THEN** o sistema atualiza o usuário e exibe mensagem de sucesso

#### Scenario: Acesso sem permissão
- **WHEN** um usuário sem permissão `usuarios.update` tenta editar
- **THEN** o sistema retorna erro 403

---

### Requirement: Ativar usuário
O sistema SHALL permitir ativar um usuário com status `inativo`, alterando seu status para `ativo`.

#### Scenario: Ativação bem-sucedida
- **WHEN** um administrador aciona "Ativar" em um usuário inativo
- **THEN** o status do usuário muda para `ativo` e uma mensagem de sucesso é exibida

#### Scenario: Usuário já ativo
- **WHEN** o sistema recebe uma requisição de ativação para um usuário já ativo
- **THEN** o sistema retorna erro indicando que o usuário já está ativo

---

### Requirement: Inativar usuário
O sistema SHALL permitir inativar um usuário ativo. Um usuário inativo não SHALL conseguir acessar o sistema.

#### Scenario: Inativação bem-sucedida
- **WHEN** um administrador aciona "Inativar" em um usuário ativo
- **THEN** o status do usuário muda para `inativo` e uma mensagem de sucesso é exibida

#### Scenario: Tentativa de login com usuário inativo
- **WHEN** um usuário com status `inativo` tenta fazer login
- **THEN** o sistema bloqueia o acesso com mensagem genérica de acesso bloqueado

---

### Requirement: Reenviar acesso inicial
O sistema SHALL permitir reenviar o email de primeiro acesso para usuários que ainda não concluíram o primeiro acesso (`deve_alterar_senha = true`). Uma nova senha temporária SHALL ser gerada e a anterior invalidada.

#### Scenario: Reenvio com sucesso
- **WHEN** um administrador aciona "Reenviar acesso" em um usuário com primeiro acesso pendente
- **THEN** o sistema gera nova senha temporária, atualiza o hash no banco e envia novo email de acesso

#### Scenario: Reenvio para usuário que já concluiu o primeiro acesso
- **WHEN** o administrador tenta reenviar acesso para um usuário com `deve_alterar_senha = false`
- **THEN** o sistema retorna erro informando que o primeiro acesso já foi concluído

#### Scenario: Reenvio para usuário inativo
- **WHEN** o administrador tenta reenviar acesso para um usuário inativo
- **THEN** o sistema retorna erro informando que o usuário está inativo

---

### Requirement: Seeds de perfis e permissões completos
O sistema SHALL possuir seeders que criem os quatro perfis padrão (Administrador, Financeiro, Atendente, Corretor) e as permissões do módulo `usuarios` e `perfis`, atribuindo a matriz de permissões correta para cada perfil em todos os módulos existentes.

#### Scenario: Seed do módulo usuarios
- **WHEN** o `UsuarioPermissionsSeeder` é executado
- **THEN** as permissões `usuarios.viewAny`, `usuarios.view`, `usuarios.create`, `usuarios.update`, `usuarios.alterar-status`, `usuarios.reenviar-acesso` são criadas e atribuídas ao perfil `admin`

#### Scenario: Seed do módulo perfis
- **WHEN** o `PerfilPermissionsSeeder` é executado
- **THEN** as permissões `perfis.viewAny`, `perfis.view` são criadas e atribuídas ao perfil `admin`

#### Scenario: Seed de todos os perfis padrão
- **WHEN** o `PerfisEPermissoesSeeder` é executado
- **THEN** os perfis `admin`, `financeiro`, `atendente`, `corretor` existem com suas permissões definidas conforme a matriz do PRD

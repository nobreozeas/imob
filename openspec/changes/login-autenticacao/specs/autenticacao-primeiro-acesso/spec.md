## ADDED Requirements

### Requirement: Sistema obriga troca de senha no primeiro acesso
O sistema SHALL detectar quando `deve_alterar_senha = true` após o login e redirecionar obrigatoriamente para a tela de troca de senha antes de liberar acesso aos módulos internos.

#### Scenario: Redirecionamento obrigatório no primeiro acesso
- **WHEN** o usuário faz login com `deve_alterar_senha = true`
- **THEN** o sistema redireciona para `/primeiro-acesso` e o usuário NÃO SHALL acessar nenhuma rota interna antes de trocar a senha

#### Scenario: Tentativa de burlar a troca de senha
- **WHEN** o usuário com `deve_alterar_senha = true` tenta acessar qualquer rota interna diretamente (ex: `/dashboard`)
- **THEN** o sistema redireciona para `/primeiro-acesso`

### Requirement: Usuário define nova senha no primeiro acesso
O sistema SHALL permitir que o usuário defina uma nova senha definitiva na tela de primeiro acesso, sem precisar informar a senha atual.

#### Scenario: Troca de senha com sucesso
- **WHEN** o usuário informa uma nova senha válida e a confirmação correta
- **THEN** o sistema atualiza a senha, define `deve_alterar_senha = false`, registra `primeiro_acesso_em` (se for o primeiro), e redireciona para o dashboard

#### Scenario: Nova senha igual à senha temporária
- **WHEN** o usuário informa uma nova senha igual à senha temporária que usou para fazer login
- **THEN** o sistema exibe mensagem de erro "A nova senha deve ser diferente da senha temporária." e não realiza a troca

#### Scenario: Confirmação de senha diferente
- **WHEN** o usuário informa nova senha e confirmação com valores diferentes
- **THEN** o sistema exibe mensagem de erro "As senhas não coincidem." e não realiza a troca

#### Scenario: Nova senha não atende critérios mínimos
- **WHEN** o usuário informa uma nova senha com menos de 8 caracteres ou sem letra e número
- **THEN** o sistema exibe mensagem de erro com os critérios mínimos e não realiza a troca

### Requirement: Administrador pode gerar nova senha temporária e reenviar acesso
O sistema SHALL permitir que um administrador reenvie as instruções de primeiro acesso, gerando uma nova senha temporária.

#### Scenario: Reenvio de acesso pelo administrador
- **WHEN** o administrador clica em "Reenviar acesso" para um usuário
- **THEN** o sistema gera uma nova senha temporária, atualiza `deve_alterar_senha = true`, registra `convite_enviado_em`, e envia email com as novas credenciais

### Requirement: Administrador pode forçar troca de senha de usuário ativo
O sistema SHALL permitir que um administrador marque `deve_alterar_senha = true` para um usuário já ativo, forçando a troca no próximo login.

#### Scenario: Forçar troca de senha
- **WHEN** o administrador clica em "Forçar troca de senha" para um usuário ativo
- **THEN** o sistema define `deve_alterar_senha = true` e no próximo login do usuário ele é redirecionado para a tela de troca de senha

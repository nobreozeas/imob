## ADDED Requirements

### Requirement: Usuário autenticado pode alterar sua própria senha
O sistema SHALL permitir que o usuário autenticado altere sua senha informando a senha atual, a nova senha e a confirmação, acessível pelo menu "Minha Conta".

#### Scenario: Alteração de senha com sucesso
- **WHEN** o usuário informa a senha atual correta, uma nova senha válida e a confirmação correta
- **THEN** o sistema atualiza a senha e exibe mensagem "Senha alterada com sucesso."

#### Scenario: Senha atual incorreta
- **WHEN** o usuário informa uma senha atual incorreta
- **THEN** o sistema exibe mensagem "A senha atual informada está incorreta." e não realiza a alteração

#### Scenario: Nova senha igual à senha atual
- **WHEN** o usuário informa uma nova senha idêntica à senha atual
- **THEN** o sistema exibe mensagem "A nova senha deve ser diferente da senha atual." e não realiza a alteração

#### Scenario: Confirmação de senha diferente
- **WHEN** o usuário informa nova senha e confirmação com valores diferentes
- **THEN** o sistema exibe mensagem "As senhas não coincidem." e não realiza a alteração

#### Scenario: Nova senha não atende critérios mínimos
- **WHEN** o usuário informa uma nova senha com menos de 8 caracteres ou sem letra e número
- **THEN** o sistema exibe mensagem de erro com os critérios e não realiza a alteração

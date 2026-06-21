## ADDED Requirements

### Requirement: Usuário pode solicitar recuperação de senha por email
O sistema SHALL permitir que um usuário solicite a redefinição de senha informando seu email cadastrado, sem revelar se o email existe ou não.

#### Scenario: Solicitação com email cadastrado
- **WHEN** o usuário informa um email que está cadastrado no sistema
- **THEN** o sistema envia um email com link de recuperação e exibe a mensagem "Se este email estiver cadastrado, enviaremos as instruções para redefinição de senha."

#### Scenario: Solicitação com email não cadastrado
- **WHEN** o usuário informa um email que NÃO está cadastrado no sistema
- **THEN** o sistema exibe a mesma mensagem genérica "Se este email estiver cadastrado, enviaremos as instruções para redefinição de senha." sem revelar que o email não existe

#### Scenario: Solicitação para usuário inativo
- **WHEN** o usuário informa o email de um usuário com status `inativo`
- **THEN** o sistema NÃO SHALL enviar o email de recuperação, mas SHALL exibir a mensagem genérica para não revelar o status

### Requirement: Link de recuperação de senha tem prazo de expiração
O sistema SHALL gerar um link de recuperação único e seguro com validade limitada (60 minutos).

#### Scenario: Link válido utilizado dentro do prazo
- **WHEN** o usuário acessa o link de recuperação dentro do prazo de validade
- **THEN** o sistema exibe o formulário para definição de nova senha

#### Scenario: Link expirado
- **WHEN** o usuário acessa o link de recuperação após o prazo de validade
- **THEN** o sistema exibe mensagem "Este link de recuperação expirou. Solicite um novo." e redireciona para a tela de recuperação

#### Scenario: Link já utilizado
- **WHEN** o usuário tenta usar um link de recuperação que já foi utilizado para redefinir a senha
- **THEN** o sistema exibe mensagem de erro e NÃO SHALL permitir nova redefinição com o mesmo link

### Requirement: Usuário define nova senha via link de recuperação
O sistema SHALL permitir que o usuário defina uma nova senha ao acessar o link de recuperação válido.

#### Scenario: Redefinição de senha com sucesso
- **WHEN** o usuário informa uma nova senha válida e a confirmação correta através do link de recuperação
- **THEN** o sistema atualiza a senha, invalida o token de recuperação, e redireciona para a tela de login com mensagem "Senha redefinida com sucesso. Faça login com sua nova senha."

#### Scenario: Confirmação de senha diferente
- **WHEN** o usuário informa nova senha e confirmação com valores diferentes
- **THEN** o sistema exibe mensagem de erro "As senhas não coincidem." e não realiza a troca

#### Scenario: Nova senha não atende critérios mínimos
- **WHEN** o usuário informa uma senha com menos de 8 caracteres ou sem letra e número
- **THEN** o sistema exibe mensagem de erro com os critérios mínimos e não realiza a troca

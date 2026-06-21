## MODIFIED Requirements

### Requirement: Cadastro de cliente pessoa física
O sistema SHALL permitir o cadastro de clientes do tipo pessoa física através de um wizard de 5 etapas. Os campos nome completo, CPF, RG, data de nascimento, contatos, endereço e observações gerais SHALL ser distribuídos entre as etapas do wizard. CPF SHALL ser único no sistema.

#### Scenario: Cadastro válido de pessoa física
- **WHEN** o usuário preenche todas as etapas do wizard com nome, CPF válido e único, pelo menos um papel, e clica em "Salvar" na última etapa
- **THEN** o sistema cria o cliente com status ativo e redireciona para a tela de detalhes com mensagem de sucesso

#### Scenario: CPF duplicado
- **WHEN** o usuário tenta cadastrar um cliente com CPF já existente no sistema
- **THEN** o sistema SHALL retornar erro de validação e navegar automaticamente para a Etapa 1 exibindo o erro no campo CPF

#### Scenario: Nome obrigatório ausente
- **WHEN** o usuário tenta avançar da Etapa 1 sem preencher o nome completo
- **THEN** o sistema SHALL exibir erro inline no campo e bloquear o avanço

---

### Requirement: Cadastro de cliente pessoa jurídica
O sistema SHALL permitir o cadastro de clientes do tipo pessoa jurídica através de um wizard de 5 etapas. CNPJ SHALL ser único no sistema.

#### Scenario: Cadastro válido de pessoa jurídica
- **WHEN** o usuário seleciona tipo "jurídica" na Etapa 1, preenche razão social e CNPJ válido, seleciona ao menos um papel na Etapa 2, e clica em "Salvar" na Etapa 5
- **THEN** o sistema cria o cliente com status ativo e redireciona para a tela de detalhes

#### Scenario: CNPJ duplicado
- **WHEN** o usuário submete o wizard com CNPJ já existente
- **THEN** o sistema SHALL retornar erro de validação e navegar para a Etapa 1 com o erro exibido no campo CNPJ

---

### Requirement: Sistema de papéis do cliente
O sistema SHALL apresentar a seleção de papéis (proprietário e/ou inquilino) na Etapa 2 do wizard, de forma destacada. Ao menos um papel SHALL ser obrigatório para avançar desta etapa.

#### Scenario: Cliente com papel único de proprietário
- **WHEN** o usuário marca somente o papel "proprietário" na Etapa 2 e avança
- **THEN** a Etapa 5 SHALL exibir apenas a seção de dados de proprietário

#### Scenario: Cliente com ambos os papéis
- **WHEN** o usuário marca os papéis "proprietário" e "inquilino" na Etapa 2
- **THEN** a Etapa 5 SHALL exibir as seções de dados de proprietário e de inquilino

#### Scenario: Bloqueio sem papel na Etapa 2
- **WHEN** o usuário tenta avançar da Etapa 2 sem selecionar nenhum papel
- **THEN** o sistema SHALL exibir erro e bloquear o avanço para a Etapa 3

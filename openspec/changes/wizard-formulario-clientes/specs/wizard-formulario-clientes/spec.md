## ADDED Requirements

### Requirement: Wizard de cadastro e edição em etapas sequenciais
O sistema SHALL apresentar o formulário de cadastro e edição de clientes em um wizard com 5 etapas sequenciais: (1) Dados Principais, (2) Papéis, (3) Contatos, (4) Endereço, (5) Dados Adicionais e Resumo. Uma barra de progresso SHALL indicar a etapa atual e o total de etapas.

#### Scenario: Exibição da barra de progresso
- **WHEN** o usuário acessa o formulário de cadastro ou edição
- **THEN** o sistema exibe a barra de progresso com as 5 etapas indicando a etapa ativa

#### Scenario: Etapa inicial ao abrir cadastro
- **WHEN** o usuário clica em "Novo Cliente"
- **THEN** o sistema exibe a Etapa 1 (Dados Principais) com todos os campos desta etapa

#### Scenario: Etapa inicial ao abrir edição
- **WHEN** o usuário clica em "Editar" em um cliente existente
- **THEN** o sistema exibe a Etapa 1 (Dados Principais) com os campos pré-preenchidos com os dados atuais do cliente

---

### Requirement: Navegação para próxima etapa com validação
O sistema SHALL validar os campos obrigatórios da etapa atual antes de permitir avançar. Se houver campos obrigatórios não preenchidos, o sistema SHALL exibir mensagens de erro inline e bloquear o avanço.

#### Scenario: Avanço com campos obrigatórios preenchidos
- **WHEN** o usuário clica em "Próximo" com todos os campos obrigatórios da etapa atual preenchidos
- **THEN** o sistema avança para a próxima etapa e a barra de progresso é atualizada

#### Scenario: Bloqueio de avanço sem nome (Etapa 1, pessoa física)
- **WHEN** o usuário clica em "Próximo" na Etapa 1 sem preencher o nome completo (pessoa física)
- **THEN** o sistema SHALL exibir erro inline no campo e NOT SHALL avançar para a Etapa 2

#### Scenario: Bloqueio de avanço sem papel (Etapa 2)
- **WHEN** o usuário clica em "Próximo" na Etapa 2 sem selecionar nenhum papel
- **THEN** o sistema SHALL exibir erro indicando que ao menos um papel é obrigatório e NOT SHALL avançar para a Etapa 3

---

### Requirement: Navegação para etapa anterior
O sistema SHALL permitir que o usuário retorne a qualquer etapa anterior clicando no botão "Anterior". Os dados preenchidos nas etapas posteriores SHALL ser preservados ao retornar.

#### Scenario: Retorno à etapa anterior
- **WHEN** o usuário clica em "Anterior" em qualquer etapa (exceto a Etapa 1)
- **THEN** o sistema exibe a etapa anterior com os dados previamente preenchidos intactos

#### Scenario: Etapa 1 não tem botão Anterior
- **WHEN** o usuário está na Etapa 1
- **THEN** o botão "Anterior" NOT SHALL ser exibido

---

### Requirement: Redirecionamento automático para etapa com erro do servidor
Quando o submit retornar erros de validação do servidor (HTTP 422), o sistema SHALL identificar a qual etapa pertence o primeiro campo com erro e navegar automaticamente para essa etapa.

#### Scenario: Erro de CPF duplicado após submit
- **WHEN** o usuário submete o formulário na Etapa 5 e o servidor retorna erro no campo `cpf`
- **THEN** o sistema SHALL navegar automaticamente para a Etapa 1 e exibir o erro no campo CPF

#### Scenario: Erro de papel após submit
- **WHEN** o servidor retorna erro no campo `papeis`
- **THEN** o sistema SHALL navegar automaticamente para a Etapa 2

---

### Requirement: Resumo na última etapa antes do envio
A Etapa 5 SHALL exibir um resumo dos dados preenchidos nas etapas anteriores, além das seções condicionais de dados de proprietário e/ou inquilino conforme os papéis selecionados. O botão de submit ("Salvar") SHALL aparecer apenas na Etapa 5.

#### Scenario: Resumo com papel proprietário
- **WHEN** o usuário chega à Etapa 5 com o papel "proprietário" selecionado
- **THEN** o sistema exibe as seções de dados principais, contatos, endereço E dados de proprietário

#### Scenario: Resumo com ambos os papéis
- **WHEN** o usuário chega à Etapa 5 com ambos os papéis selecionados
- **THEN** o sistema exibe as seções de dados de proprietário e dados de inquilino

#### Scenario: Botão Salvar apenas na última etapa
- **WHEN** o usuário está em qualquer etapa anterior à Etapa 5
- **THEN** o botão "Salvar" NOT SHALL ser exibido; apenas o botão "Próximo"

---

### Requirement: Dados preservados ao navegar entre etapas
Todos os campos preenchidos em qualquer etapa SHALL ser preservados ao navegar para frente ou para trás no wizard, até o submit ou cancelamento.

#### Scenario: Dados da Etapa 1 preservados ao voltar da Etapa 3
- **WHEN** o usuário preenche a Etapa 1, avança até a Etapa 3 e clica em "Anterior" duas vezes
- **THEN** os campos da Etapa 1 SHALL exibir os valores previamente preenchidos

#### Scenario: Dados adicionais condicionais preservados ao trocar papel
- **WHEN** o usuário seleciona "proprietário" na Etapa 2, preenche dados na Etapa 5 e volta à Etapa 2 para adicionar "inquilino"
- **THEN** os dados de proprietário preenchidos anteriormente SHALL ser mantidos

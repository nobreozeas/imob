## ADDED Requirements

### Requirement: Cadastro de cliente pessoa física
O sistema SHALL permitir o cadastro de clientes do tipo pessoa física com os campos: nome completo, CPF, RG, data de nascimento, telefone principal, WhatsApp, telefone secundário, e-mail principal, e-mail alternativo, endereço completo e observações gerais. CPF SHALL ser único no sistema.

#### Scenario: Cadastro válido de pessoa física
- **WHEN** o usuário preenche nome, CPF válido e único, pelo menos um papel, e submete o formulário
- **THEN** o sistema cria o cliente com status ativo e redireciona para a tela de detalhes com mensagem de sucesso

#### Scenario: CPF duplicado
- **WHEN** o usuário tenta cadastrar um cliente com CPF já existente no sistema
- **THEN** o sistema SHALL retornar erro de validação informando que o CPF já está cadastrado

#### Scenario: Nome obrigatório ausente
- **WHEN** o usuário submete o formulário sem preencher o nome completo
- **THEN** o sistema SHALL retornar erro de validação indicando que o nome é obrigatório

---

### Requirement: Cadastro de cliente pessoa jurídica
O sistema SHALL permitir o cadastro de clientes do tipo pessoa jurídica com os campos: razão social, nome fantasia, CNPJ, telefone principal, WhatsApp, telefone secundário, e-mail principal, e-mail alternativo, endereço completo e observações gerais. CNPJ SHALL ser único no sistema.

#### Scenario: Cadastro válido de pessoa jurídica
- **WHEN** o usuário seleciona tipo "jurídica", preenche razão social, CNPJ válido e único, pelo menos um papel, e submete
- **THEN** o sistema cria o cliente com status ativo e redireciona para a tela de detalhes

#### Scenario: CNPJ duplicado
- **WHEN** o usuário tenta cadastrar um cliente com CNPJ já existente no sistema
- **THEN** o sistema SHALL retornar erro de validação informando que o CNPJ já está cadastrado

---

### Requirement: Sistema de papéis do cliente
O sistema SHALL permitir que um cliente seja marcado com um ou mais papéis: proprietário e/ou inquilino. Ao menos um papel SHALL ser obrigatório no cadastro.

#### Scenario: Cliente com papel único de proprietário
- **WHEN** o usuário marca somente o papel "proprietário" ao cadastrar ou editar o cliente
- **THEN** o sistema salva o papel e o cliente aparece como opção somente em campos de proprietário de imóveis

#### Scenario: Cliente com papel único de inquilino
- **WHEN** o usuário marca somente o papel "inquilino"
- **THEN** o sistema salva o papel e o cliente aparece como opção somente em campos de inquilino de contratos

#### Scenario: Cliente com ambos os papéis
- **WHEN** o usuário marca os papéis "proprietário" e "inquilino"
- **THEN** o sistema salva ambos os papéis e o cliente aparece como opção tanto em imóveis quanto em contratos

#### Scenario: Cadastro sem papel
- **WHEN** o usuário submete o formulário sem selecionar nenhum papel
- **THEN** o sistema SHALL retornar erro de validação indicando que ao menos um papel é obrigatório

---

### Requirement: Dados adicionais para proprietário
Quando o cliente possuir o papel de proprietário, o sistema SHALL permitir armazenar: banco, agência, conta, tipo de conta, chave PIX, tipo de chave PIX, percentual padrão de administração, indicação se emite nota fiscal, preferência de recebimento e observações de repasse.

#### Scenario: Salvamento de dados bancários do proprietário
- **WHEN** o usuário preenche os dados bancários ao cadastrar ou editar um cliente com papel de proprietário
- **THEN** o sistema salva os dados na tabela `cliente_dados_proprietario` vinculada ao cliente

#### Scenario: Dados de proprietário ignorados sem o papel
- **WHEN** o usuário preenche campos de proprietário mas não marca o papel "proprietário"
- **THEN** o sistema SHALL ignorar esses dados e não persistir registros em `cliente_dados_proprietario`

---

### Requirement: Dados adicionais para inquilino
Quando o cliente possuir o papel de inquilino, o sistema SHALL permitir armazenar: profissão, renda mensal aproximada, local de trabalho, telefone comercial, contato de emergência, observações cadastrais e restrições relevantes.

#### Scenario: Salvamento de dados do inquilino
- **WHEN** o usuário preenche os dados de inquilino ao cadastrar ou editar um cliente com papel de inquilino
- **THEN** o sistema salva os dados na tabela `cliente_dados_inquilino` vinculada ao cliente

#### Scenario: Dados de inquilino ignorados sem o papel
- **WHEN** o usuário preenche campos de inquilino mas não marca o papel "inquilino"
- **THEN** o sistema SHALL ignorar esses dados e não persistir registros em `cliente_dados_inquilino`

---

### Requirement: Listagem de clientes com filtros e paginação
O sistema SHALL exibir uma lista paginada de clientes com as colunas: nome/razão social, CPF/CNPJ, tipo de pessoa, telefone, e-mail, papéis, cidade, status, data de cadastro e ações.

#### Scenario: Listagem padrão
- **WHEN** o usuário acessa a área de clientes
- **THEN** o sistema exibe a lista paginada com todos os clientes ordenados por nome, com 20 registros por página

#### Scenario: Busca textual
- **WHEN** o usuário digita um termo na caixa de busca
- **THEN** o sistema filtra clientes cujo nome/razão social, CPF ou CNPJ contenha o termo buscado

#### Scenario: Filtro por tipo de pessoa
- **WHEN** o usuário seleciona o filtro "Tipo de Pessoa" com valor "Física" ou "Jurídica"
- **THEN** a listagem exibe apenas clientes do tipo selecionado

#### Scenario: Filtro por papel
- **WHEN** o usuário seleciona o filtro "Papel" com valor "Proprietário", "Inquilino" ou "Ambos"
- **THEN** a listagem exibe apenas clientes com o papel selecionado

#### Scenario: Filtro por status
- **WHEN** o usuário seleciona o filtro "Status" com valor "Ativo" ou "Inativo"
- **THEN** a listagem exibe apenas clientes com o status selecionado

#### Scenario: Filtro por cidade
- **WHEN** o usuário seleciona ou digita uma cidade no filtro
- **THEN** a listagem exibe apenas clientes da cidade selecionada

#### Scenario: Ordenação
- **WHEN** o usuário clica no cabeçalho da coluna "Nome", "Data de Cadastro" ou "Status"
- **THEN** a listagem é reordenada pela coluna selecionada, alternando entre ascendente e descendente

---

### Requirement: Visualização detalhada do cliente
O sistema SHALL exibir uma tela de detalhes do cliente com todas as informações: dados principais, contatos, endereço, papéis, dados específicos de proprietário (se aplicável) e dados específicos de inquilino (se aplicável).

#### Scenario: Exibição de dados do proprietário
- **WHEN** o usuário acessa a tela de detalhes de um cliente com papel de proprietário
- **THEN** o sistema exibe a seção de dados de proprietário com informações bancárias e de repasse

#### Scenario: Exibição de dados do inquilino
- **WHEN** o usuário acessa a tela de detalhes de um cliente com papel de inquilino
- **THEN** o sistema exibe a seção de dados de inquilino com profissão, renda e contatos adicionais

#### Scenario: Seções condicionais
- **WHEN** o cliente não possui o papel de proprietário
- **THEN** a seção de dados de proprietário NOT SHALL ser exibida

---

### Requirement: Edição de cliente com proteção de vínculos
O sistema SHALL permitir editar os dados de um cliente. Não SHALL ser permitido remover o papel de proprietário se o cliente possuir imóveis ativos vinculados. Não SHALL ser permitido remover o papel de inquilino se o cliente possuir contratos ativos vinculados.

#### Scenario: Remoção de papel sem vínculo ativo
- **WHEN** o usuário remove o papel de proprietário de um cliente sem imóveis ativos
- **THEN** o sistema salva a alteração e remove o papel com sucesso

#### Scenario: Tentativa de remover papel com vínculo ativo
- **WHEN** o usuário tenta remover o papel de proprietário de um cliente que possui imóveis ativos vinculados
- **THEN** o sistema SHALL retornar erro 422 com mensagem explicando que o papel não pode ser removido enquanto houver imóveis ativos

---

### Requirement: Controle de status do cliente
O sistema SHALL permitir ativar e inativar clientes. Clientes inativos NOT SHALL aparecer como opção em novos cadastros de imóveis ou contratos.

#### Scenario: Inativação de cliente
- **WHEN** o usuário confirma a inativação de um cliente ativo
- **THEN** o sistema altera o status para "inativo" e exibe mensagem de confirmação

#### Scenario: Ativação de cliente inativo
- **WHEN** o usuário confirma a ativação de um cliente inativo
- **THEN** o sistema altera o status para "ativo"

#### Scenario: Cliente inativo não disponível para seleção
- **WHEN** outro módulo (imóveis, contratos) busca clientes disponíveis
- **THEN** o sistema SHALL retornar apenas clientes com status "ativo"

---

### Requirement: Proteção contra exclusão física
O sistema SHALL priorizar a inativação em vez da exclusão. Clientes com imóveis ou contratos vinculados (ativos ou históricos) NOT SHALL ser excluídos fisicamente.

#### Scenario: Tentativa de exclusão de cliente com vínculo
- **WHEN** o sistema tenta excluir fisicamente um cliente que possui vínculos
- **THEN** a operação SHALL ser bloqueada e o cliente permanecer com soft delete aplicado

#### Scenario: Soft delete para todos os clientes
- **WHEN** qualquer cliente é "excluído" via interface
- **THEN** o sistema aplica soft delete (preenche `deleted_at`) e não remove o registro do banco

---

### Requirement: Controle de permissões de acesso
O sistema SHALL controlar o acesso às funcionalidades de clientes via permissões Spatie: `clientes.ver`, `clientes.criar`, `clientes.editar`, `clientes.ativar-inativar`.

#### Scenario: Usuário sem permissão de criar
- **WHEN** um usuário sem a permissão `clientes.criar` tenta acessar o formulário de cadastro
- **THEN** o sistema SHALL retornar HTTP 403 (Forbidden)

#### Scenario: Usuário com permissão de visualizar apenas
- **WHEN** um usuário com somente `clientes.ver` acessa a listagem ou detalhes
- **THEN** o sistema exibe as informações sem botões de edição ou cadastro

#### Scenario: Administrador com acesso completo
- **WHEN** um usuário administrador acessa qualquer funcionalidade de clientes
- **THEN** o sistema concede acesso a todas as ações disponíveis

---

### Requirement: Exibição visual de papéis na listagem
O sistema SHALL exibir os papéis do cliente visualmente na listagem com badges/etiquetas distintas para cada papel.

#### Scenario: Badge de proprietário
- **WHEN** um cliente possui apenas o papel de proprietário
- **THEN** a coluna "Papéis" exibe o badge "Proprietário"

#### Scenario: Badge de inquilino
- **WHEN** um cliente possui apenas o papel de inquilino
- **THEN** a coluna "Papéis" exibe o badge "Inquilino"

#### Scenario: Múltiplos badges
- **WHEN** um cliente possui ambos os papéis
- **THEN** a coluna "Papéis" exibe os badges "Proprietário" e "Inquilino" lado a lado

---

### Requirement: Confirmação de ações críticas com SweetAlert
O sistema SHALL exibir caixas de confirmação SweetAlert antes de executar ações de ativar, inativar ou excluir clientes.

#### Scenario: Confirmação de inativação
- **WHEN** o usuário clica em "Inativar" na listagem ou tela de detalhes
- **THEN** o sistema exibe diálogo SweetAlert pedindo confirmação antes de executar a ação

#### Scenario: Cancelamento da ação
- **WHEN** o usuário cancela a confirmação no SweetAlert
- **THEN** nenhuma alteração é realizada e o cliente permanece com o status atual

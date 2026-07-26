## ADDED Requirements

### Requirement: Todo lançamento financeiro possui tipo, categoria, status e origem
Todo lançamento financeiro SHALL ser `entrada` ou `saida`, SHALL pertencer a uma categoria financeira ativa, SHALL possuir um `status` (`pendente`, `pago`, `cancelado`, `estornado`) e SHALL registrar sua `origem` (`manual`, `pagamento_aluguel`, `repasse_proprietario`, `caucao`, `movimentacao_caucao`, `despesa`, `receita_diversa`, `ajuste`).

#### Scenario: Lançamento sem categoria é rejeitado
- **WHEN** o usuário tenta salvar um lançamento financeiro sem selecionar categoria
- **THEN** o sistema rejeita a operação com erro de validação

### Requirement: Cadastro manual de receita diversa
O sistema SHALL permitir cadastrar manualmente uma receita (origem `receita_diversa`) com descrição, categoria, valor maior que zero, cliente/contrato/imóvel relacionados (opcionais), data de vencimento, e status `pendente` ou `pago`.

#### Scenario: Receita paga exige dados de recebimento
- **WHEN** o usuário salva uma receita manual com status `pago`
- **THEN** o sistema exige `data_pagamento` e `forma_pagamento`, e rejeita a operação se algum estiver ausente

#### Scenario: Receita diversa não gera repasse
- **WHEN** uma receita diversa é registrada como paga
- **THEN** nenhum repasse ao proprietário é gerado automaticamente

### Requirement: Cadastro manual de despesa
O sistema SHALL permitir cadastrar manualmente uma despesa (origem `despesa`) com descrição, categoria, valor maior que zero, fornecedor/contrato/imóvel relacionados (opcionais), data de vencimento, e status `pendente` ou `pago`.

#### Scenario: Despesa paga exige dados de pagamento
- **WHEN** o usuário salva uma despesa manual com status `pago`
- **THEN** o sistema exige `data_pagamento` e `forma_pagamento`, e rejeita a operação se algum estiver ausente

### Requirement: Lançamentos automáticos de pagamento de aluguel, repasse e caução
O sistema SHALL criar automaticamente um lançamento financeiro com status `pago` sempre que: um pagamento de aluguel for registrado (entrada, categoria `aluguel`), um repasse for marcado como pago (saída, categoria `repasse_proprietario`), uma caução for recebida (entrada, categoria `caucao`) ou devolvida (saída, categoria `devolucao_caucao`).

#### Scenario: Pagamento de aluguel gera lançamento de entrada
- **WHEN** um pagamento de parcela de aluguel é registrado
- **THEN** um lançamento financeiro de entrada, categoria `aluguel`, origem `pagamento_aluguel`, status `pago`, é criado vinculado ao contrato e à parcela

#### Scenario: Repasse pago gera lançamento de saída
- **WHEN** um repasse ao proprietário é marcado como pago
- **THEN** um lançamento financeiro de saída, categoria `repasse_proprietario`, origem `repasse_proprietario`, status `pago`, é criado vinculado ao repasse

#### Scenario: Recebimento de caução gera lançamento de entrada
- **WHEN** o recebimento de uma caução é registrado
- **THEN** um lançamento financeiro de entrada, categoria `caucao`, origem `caucao`, status `pago`, é criado vinculado à caução

#### Scenario: Devolução de caução gera lançamento de saída
- **WHEN** a devolução de uma caução é registrada
- **THEN** um lançamento financeiro de saída, categoria `devolucao_caucao`, origem `movimentacao_caucao`, status `pago`, é criado vinculado à movimentação de caução

### Requirement: Lançamento automático tem edição limitada
O sistema SHALL impedir a edição livre de valor, categoria e vínculos de um lançamento cuja origem não seja `manual`; apenas as ações de cancelar e estornar são permitidas.

#### Scenario: Tentativa de editar lançamento automático
- **WHEN** o usuário tenta editar um lançamento com origem `pagamento_aluguel`
- **THEN** o sistema rejeita a edição

### Requirement: Marcar lançamento pendente como pago
O sistema SHALL permitir marcar um lançamento manual `pendente` como `pago`, exigindo data e forma de pagamento, e registrando o usuário responsável.

#### Scenario: Lançamento pendente marcado como pago
- **WHEN** o usuário marca uma despesa `pendente` como paga, informando data e forma de pagamento
- **THEN** o status do lançamento muda para `pago` e o usuário responsável é registrado como `pago_por`

### Requirement: Cancelamento exige motivo e preserva histórico
O sistema SHALL permitir cancelar um lançamento `pendente`, exigindo motivo, sem apagar o registro.

#### Scenario: Cancelamento sem motivo é rejeitado
- **WHEN** o usuário tenta cancelar um lançamento pendente sem informar motivo
- **THEN** o sistema rejeita a operação com erro de validação

#### Scenario: Cancelamento com motivo é aceito
- **WHEN** o usuário cancela um lançamento pendente informando o motivo
- **THEN** o status muda para `cancelado`, `motivo_cancelamento` e `cancelado_por` são registrados, e o registro permanece no histórico

### Requirement: Estorno exige motivo e preserva o lançamento original
O sistema SHALL permitir estornar um lançamento `pago`, exigindo motivo, sem apagar ou alterar o valor do lançamento original.

#### Scenario: Estorno sem motivo é rejeitado
- **WHEN** o usuário tenta estornar um lançamento pago sem informar motivo
- **THEN** o sistema rejeita a operação com erro de validação

#### Scenario: Estorno com motivo é aceito
- **WHEN** o usuário estorna um lançamento pago informando o motivo
- **THEN** o status muda para `estornado`, `motivo_estorno` e `estornado_por` são registrados, e o lançamento deixa de compor os indicadores de receita/despesa realizada

### Requirement: Listagem paginada e filtrável de lançamentos
O sistema SHALL listar os lançamentos financeiros em tabela paginada, com busca geral e filtros por tipo, categoria, status, forma de pagamento, período de vencimento, período de pagamento, contrato, imóvel, cliente e origem.

#### Scenario: Filtro por status
- **WHEN** o usuário filtra a listagem por status `pendente`
- **THEN** apenas lançamentos com status `pendente` são exibidos

### Requirement: Toda ação relevante gera histórico financeiro
O sistema SHALL registrar em `historicos_financeiros` a criação, edição, pagamento, cancelamento e estorno de um lançamento financeiro, com dados anteriores, dados novos e usuário responsável.

#### Scenario: Histórico registrado ao cancelar
- **WHEN** um lançamento é cancelado
- **THEN** um registro de histórico financeiro é criado com ação `lancamento_cancelado`, o motivo informado e o usuário responsável

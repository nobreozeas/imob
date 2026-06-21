## MODIFIED Requirements

### Requirement: Verificação de contrato ativo no imóvel
O modelo `Imovel` SHALL consultar a tabela `contratos_locacao` para determinar se o imóvel possui contrato ativo, substituindo o retorno fixo `false` anterior. Um imóvel possui contrato ativo quando existe pelo menos um registro em `contratos_locacao` com `imovel_id = $this->id` e `status = 'ativo'`.

#### Scenario: Imóvel com contrato ativo
- **WHEN** `Imovel::temContratoAtivo()` é chamado em imóvel com contrato de status `ativo`
- **THEN** método retorna `true`

#### Scenario: Imóvel sem contrato ativo
- **WHEN** `Imovel::temContratoAtivo()` é chamado em imóvel sem contratos ou apenas com contratos em status não-ativo
- **THEN** método retorna `false`

### Requirement: Bloqueio de alteração manual de status para "disponível" com contrato ativo
A action `alterarStatus` do `ImovelController` SHALL impedir a transição manual para `disponivel` quando `temContratoAtivo()` retornar `true`. O status `disponivel` do imóvel SHALL ser definido automaticamente pelo encerramento ou rescisão do contrato.

#### Scenario: Tentativa de liberar imóvel com contrato ativo
- **WHEN** usuário tenta alterar status do imóvel para `disponivel` via `PATCH /imoveis/{imovel}/status` enquanto há contrato ativo
- **THEN** sistema retorna erro de validação com mensagem: "O imóvel possui contrato de locação ativo. Encerre ou rescinda o contrato para liberar o imóvel."

#### Scenario: Alteração de status sem contrato ativo
- **WHEN** usuário altera status do imóvel para qualquer valor sem contrato ativo
- **THEN** sistema aplica a alteração normalmente

## ADDED Requirements

### Requirement: Status `alugado` definido automaticamente pela ativação do contrato
Quando um contrato de locação é ativado, o sistema SHALL automaticamente atualizar o status do imóvel para `alugado` dentro da mesma transação de banco de dados.

#### Scenario: Ativação do contrato atualiza imóvel
- **WHEN** contrato é ativado (via wizard na criação ou via action de ativação)
- **THEN** imóvel vinculado tem status atualizado para `alugado` na mesma `DB::transaction`

#### Scenario: Falha na atualização do imóvel reverte o contrato
- **WHEN** a atualização do status do imóvel falha durante a ativação do contrato
- **THEN** a transação é revertida e o contrato permanece no status anterior

### Requirement: Status do imóvel liberado automaticamente pelo encerramento ou rescisão do contrato
Quando um contrato ativo é encerrado ou rescindido, o sistema SHALL automaticamente atualizar o status do imóvel para `disponivel` dentro da mesma transação.

#### Scenario: Encerramento libera o imóvel
- **WHEN** contrato ativo é encerrado via action `POST /contratos/{contrato}/encerrar`
- **THEN** imóvel vinculado tem status atualizado para `disponivel` na mesma transação

#### Scenario: Rescisão libera o imóvel
- **WHEN** contrato ativo é rescindido via action `POST /contratos/{contrato}/rescindir`
- **THEN** imóvel vinculado tem status atualizado para `disponivel` na mesma transação

## ADDED Requirements

### Requirement: Configuração de caução no contrato
O sistema SHALL permitir registrar caução vinculada ao contrato, armazenada na tabela `contrato_caucoes` (relação 1:1, sempre criada mesmo que `possui_caucao = false`). A caução possui tipo (dinheiro/imovel/fiador/seguro_fianca/deposito_bancario), valor e data de recebimento.

#### Scenario: Caução habilitada na criação
- **WHEN** usuário ativa `possui_caucao` na etapa 5 do wizard
- **THEN** campos `tipo_caucao`, `valor_caucao` e `data_recebimento_caucao` são exibidos e tornam-se obrigatórios

#### Scenario: Caução desabilitada
- **WHEN** usuário deixa `possui_caucao = false`
- **THEN** sistema cria registro em `contrato_caucoes` com `possui_caucao = false` e campos de valor/data como `null`

#### Scenario: Exibição na tela de detalhes
- **WHEN** contrato possui `possui_caucao = true`
- **THEN** sistema exibe seção de caução com tipo, valor formatado como moeda, data de recebimento e status atual (recebida/devolvida/retida)

### Requirement: Controle de devolução da caução
O sistema SHALL permitir registrar a devolução ou retenção da caução no momento do encerramento ou rescisão do contrato. Os campos `data_devolucao_caucao`, `valor_devolvido` e `observacao_caucao` SHALL ser preenchíveis via modal de encerramento/rescisão.

#### Scenario: Registrar devolução integral
- **WHEN** usuário encerra contrato e informa `valor_devolvido = valor_caucao`
- **THEN** sistema salva `status_caucao = devolvida`, `data_devolucao_caucao` e `valor_devolvido` no registro `contrato_caucoes`

#### Scenario: Registrar retenção parcial ou total
- **WHEN** usuário informa `valor_devolvido < valor_caucao` no encerramento
- **THEN** sistema salva `status_caucao = retida_parcialmente` ou `retida_totalmente` conforme o valor, com `observacao_caucao` obrigatória

#### Scenario: Caução sem devolução registrada em rescisão
- **WHEN** contrato é rescindido sem informar dados de devolução da caução
- **THEN** sistema mantém `status_caucao = recebida` e exibe alerta na tela de detalhes indicando devolução pendente

### Requirement: Exibição do status da caução
O sistema SHALL exibir o status da caução em badge colorido na tela de detalhes: `recebida` (amarelo), `devolvida` (verde), `retida_parcialmente` (laranja), `retida_totalmente` (vermelho).

#### Scenario: Badge de status da caução
- **WHEN** usuário visualiza a seção de caução no contrato
- **THEN** sistema exibe badge com o status atual da caução e, se devolvida/retida, exibe valor e data da devolução

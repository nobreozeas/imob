## ADDED Requirements

### Requirement: Status vencido para contratos expirados
O sistema SHALL marcar automaticamente um contrato `ativo` como `vencido` quando sua `data_fim` já tiver passado e ele não tiver sido renovado ou encerrado, através de uma rotina agendada diária.

#### Scenario: Contrato ativo expira sem renovação
- **WHEN** um contrato `ativo` tem `data_fim` anterior à data atual e não foi renovado nem encerrado
- **THEN** a rotina agendada altera seu status para `vencido`

#### Scenario: Contrato vencido permite renovação ou encerramento
- **WHEN** um contrato está com status `vencido`
- **THEN** o sistema permite renovar o contrato ou encerrá-lo, mas não permite editá-lo como rascunho

### Requirement: Rescisão calcula multa e trata débitos e caução
Ao rescindir um contrato ativo, o sistema SHALL calcular a multa de rescisão (quando configurada), verificar parcelas vencidas em aberto, e permitir usar o saldo da caução para abater débitos, reter valores ou devolver o saldo.

#### Scenario: Rescisão calcula multa proporcional
- **WHEN** um contrato com aluguel de R$ 1.500,00, multa contratual de 3 aluguéis e 12 meses de duração é rescindido com 6 meses restantes
- **THEN** o sistema calcula multa cheia de R$ 4.500,00 e multa proporcional de R$ 2.250,00

#### Scenario: Rescisão exige motivo
- **WHEN** o usuário tenta rescindir um contrato ativo sem informar o motivo
- **THEN** o sistema rejeita a operação com erro de validação

#### Scenario: Rescisão muda imóvel conforme destino escolhido
- **WHEN** a rescisão é confirmada com `destino_imovel = disponivel`
- **THEN** o imóvel do contrato muda para status `disponivel`

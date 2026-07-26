## ADDED Requirements

### Requirement: Relatório de fluxo de caixa por período
O sistema SHALL exibir, para diária, mensal ou período customizado, as entradas previstas, entradas realizadas, saídas previstas, saídas realizadas, saldo previsto e saldo realizado.

#### Scenario: Saldo realizado do dia
- **WHEN** o usuário consulta o fluxo de caixa diário de um dia com R$ 1.000,00 de entradas pagas e R$ 400,00 de saídas pagas
- **THEN** o saldo realizado exibido para esse dia é R$ 600,00

#### Scenario: Entradas previstas consideram lançamentos pendentes
- **WHEN** existem lançamentos de entrada com status `pendente` e data de vencimento dentro do período consultado
- **THEN** o valor desses lançamentos compõe as "entradas previstas" do período, sem afetar o saldo realizado

### Requirement: Filtro de período no fluxo de caixa
O sistema SHALL permitir consultar o fluxo de caixa em visão diária, mensal ou por intervalo de datas customizado.

#### Scenario: Consulta por intervalo customizado
- **WHEN** o usuário informa uma data inicial e uma data final
- **THEN** o fluxo de caixa exibe os valores agregados por dia dentro desse intervalo

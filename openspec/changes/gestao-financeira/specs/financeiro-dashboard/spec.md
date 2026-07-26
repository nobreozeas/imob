## ADDED Requirements

### Requirement: Indicadores financeiros do mês
O sistema SHALL exibir, para o período selecionado (padrão: mês corrente), os indicadores: receitas do período, despesas do período, saldo do período, aluguéis recebidos, aluguéis em aberto, aluguéis vencidos, repasses pendentes, repasses pagos e cauções recebidas.

#### Scenario: Cálculo do saldo do período
- **WHEN** o dashboard financeiro é carregado para um período com R$ 5.000,00 em lançamentos de entrada pagos e R$ 2.000,00 em lançamentos de saída pagos
- **THEN** o indicador de saldo do período exibe R$ 3.000,00

#### Scenario: Aluguéis vencidos considera parcelas em aberto
- **WHEN** existem parcelas de aluguel com status `pendente` ou `pago_parcial` e `data_vencimento` anterior à data atual
- **THEN** o indicador de aluguéis vencidos soma o valor total dessas parcelas

### Requirement: Gráficos do dashboard financeiro
O sistema SHALL exibir gráficos de receitas x despesas por mês, recebimentos de aluguel por mês, inadimplência por mês e repasses pendentes x pagos, para o período filtrado.

#### Scenario: Gráfico de receitas x despesas por mês
- **WHEN** o usuário acessa o dashboard financeiro com um período de 3 meses selecionado
- **THEN** o gráfico de receitas x despesas exibe uma série por mês dentro do período

### Requirement: Filtros do dashboard financeiro
O sistema SHALL permitir filtrar os indicadores e gráficos do dashboard por período, proprietário, status de contrato e status de imóvel.

#### Scenario: Filtro por proprietário
- **WHEN** o usuário filtra o dashboard financeiro por um proprietário específico
- **THEN** os indicadores exibidos consideram apenas lançamentos e parcelas vinculados a imóveis/contratos desse proprietário

### Requirement: Atalhos rápidos do dashboard financeiro
O sistema SHALL exibir atalhos para: nova receita, nova despesa, registrar pagamento de aluguel, ver repasses pendentes, ver inadimplência e ver fluxo de caixa.

#### Scenario: Atalho para inadimplência
- **WHEN** o usuário clica no atalho "Ver inadimplência" no dashboard financeiro
- **THEN** o sistema navega para a listagem de inadimplência

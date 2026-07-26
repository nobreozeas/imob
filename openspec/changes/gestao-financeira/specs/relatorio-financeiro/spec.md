## ADDED Requirements

### Requirement: Relatórios financeiros básicos
O sistema SHALL disponibilizar relatórios de: receitas por período, despesas por período, fluxo de caixa, aluguéis recebidos, aluguéis em aberto, aluguéis vencidos, repasses pendentes, repasses pagos e cauções movimentadas.

#### Scenario: Relatório de receitas por período
- **WHEN** o usuário gera o relatório de receitas informando um período
- **THEN** o sistema exibe todos os lançamentos de entrada pagos dentro desse período, com o total somado

### Requirement: Filtros comuns aos relatórios financeiros
O sistema SHALL permitir filtrar qualquer relatório financeiro por período, status, categoria, tipo, cliente, contrato, imóvel e forma de pagamento, quando aplicável ao relatório.

#### Scenario: Relatório de repasses filtrado por proprietário
- **WHEN** o usuário filtra o relatório de repasses por um proprietário específico
- **THEN** o relatório lista apenas repasses vinculados a esse proprietário

### Requirement: Relatórios separam receita da imobiliária de valores de terceiros
Todo relatório financeiro que envolva valores de aluguel SHALL identificar separadamente a receita da imobiliária (taxa de administração) dos valores pertencentes ao proprietário e de valores de caução.

#### Scenario: Relatório de aluguéis recebidos distingue taxa e repasse
- **WHEN** o usuário gera o relatório de aluguéis recebidos no período
- **THEN** o relatório exibe, para cada pagamento, o valor total recebido, a taxa de administração retida e o valor destinado ao repasse

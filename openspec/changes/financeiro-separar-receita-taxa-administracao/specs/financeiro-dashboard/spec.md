## MODIFIED Requirements

### Requirement: Indicadores financeiros do mês
O sistema SHALL exibir, para o período selecionado (padrão: mês corrente), os indicadores: receitas do período, despesas do período, saldo do período, aluguéis recebidos, aluguéis em aberto, aluguéis vencidos, repasses pendentes, repasses pagos e cauções recebidas. Os indicadores de receitas, despesas e saldo do período SHALL considerar apenas lançamentos cuja categoria financeira tenha `impacta_resultado = true`, excluindo valores de terceiros em trânsito pela imobiliária (aluguel bruto, caução, devolução de caução, repasse ao proprietário).

#### Scenario: Cálculo do saldo do período considera apenas o resultado da imobiliária
- **WHEN** no período há um aluguel de R$ 1.000,00 recebido (gerando entrada de R$ 100,00 em taxa de administração) e R$ 50,00 em despesas operacionais pagas
- **THEN** o indicador de receitas do período exibe R$ 100,00, despesas exibe R$ 50,00 e saldo exibe R$ 50,00

#### Scenario: Aluguel bruto e repasse não entram no cálculo de receitas/despesas
- **WHEN** um lançamento de entrada de categoria `aluguel` e um lançamento de saída de categoria `repasse_proprietario` são pagos no período
- **THEN** nenhum dos dois valores é somado aos indicadores de receitas ou despesas do período

#### Scenario: Aluguéis vencidos considera parcelas em aberto
- **WHEN** existem parcelas de aluguel com status `pendente` ou `pago_parcial` e `data_vencimento` anterior à data atual
- **THEN** o indicador de aluguéis vencidos soma o valor total dessas parcelas

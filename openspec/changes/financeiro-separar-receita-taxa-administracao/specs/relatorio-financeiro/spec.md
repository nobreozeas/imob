## MODIFIED Requirements

### Requirement: Relatórios separam receita da imobiliária de valores de terceiros
Todo relatório financeiro que envolva valores de aluguel SHALL identificar separadamente a receita da imobiliária (taxa de administração) dos valores pertencentes ao proprietário e de valores de caução. Os relatórios de receitas e despesas por período SHALL considerar apenas lançamentos cuja categoria tenha `impacta_resultado = true`.

#### Scenario: Relatório de aluguéis recebidos distingue taxa e repasse
- **WHEN** o usuário gera o relatório de aluguéis recebidos no período
- **THEN** o relatório exibe, para cada pagamento, o valor total recebido, a taxa de administração retida e o valor destinado ao repasse

#### Scenario: Relatório de receitas exclui valores de terceiros
- **WHEN** o usuário gera o relatório de receitas por período
- **THEN** o relatório lista apenas lançamentos de entrada cuja categoria tenha `impacta_resultado = true`, sem incluir o valor bruto do aluguel ou cauções recebidas

## Why

Hoje, ao registrar um pagamento de aluguel, o sistema lança na `lancamentos_financeiros` o **valor integral do aluguel** como entrada (categoria `aluguel`), e só cria a saída correspondente ao repasse (`repasse_proprietario`) quando o repasse é efetivamente marcado como pago — muitas vezes em um período diferente. O indicador "Receitas do período" e o "Saldo do período" (`IndicadoresFinanceirosService::resumoMensal`) somam todas as entradas pagas sem distinguir categoria, então o valor de terceiros (o que pertence ao proprietário) aparece contabilizado como receita da imobiliária, distorcendo o resultado financeiro real da empresa. O PRD e a doc de Gestão Financeira já deixam essa regra explícita ("nem todo valor recebido é receita da imobiliária"; "a imobiliária recebe somente o percentual definido no contrato"), mas a implementação atual não aplica essa separação nos indicadores. O aluguel bruto continua útil de ser registrado (ajuda a controlar recebimento e inadimplência, como o próprio usuário observou), só não deve contar como receita definitiva da empresa.

## What Changes

- Ao registrar o pagamento de uma parcela de aluguel, o sistema passa a criar, além do lançamento bruto de `aluguel` (mantido, para controle de recebimento/inadimplência), um segundo lançamento automático de entrada na categoria `taxa_administracao`, com o valor efetivamente calculado pelo `RepasseProprietarioService` (o mesmo valor que hoje só aparece dentro do repasse) — essa é a receita real e definitiva da imobiliária, reconhecida no momento do recebimento, independente de quando o repasse ao proprietário for pago.
- **BREAKING** (nos indicadores, não no schema): `categorias_financeiras` ganha a coluna `impacta_resultado` (boolean, default `true`). As categorias que representam valores de terceiros em trânsito pela imobiliária (`aluguel`, `caucao`, `devolucao_caucao`, `repasse_proprietario`) são marcadas com `impacta_resultado = false`.
- `IndicadoresFinanceirosService::resumoMensal()` (indicadores "Receitas do período", "Despesas do período", "Saldo do período") e os relatórios de receitas/despesas (`RelatorioFinanceiroController`) passam a somar apenas lançamentos cuja categoria tenha `impacta_resultado = true`.
- Os indicadores "Aluguéis recebidos/em aberto/vencidos" (que já são calculados a partir de `parcelas_aluguel`, não de `lancamentos_financeiros`) e o Fluxo de Caixa (que reflete a movimentação real de caixa da imobiliária, incluindo valores de terceiros em trânsito) não mudam de comportamento.

## Capabilities

### New Capabilities
(nenhuma)

### Modified Capabilities
- `categoria-financeira` (definida em `openspec/changes/gestao-financeira`, ainda não arquivada em `openspec/specs/`): categoria financeira ganha a flag `impacta_resultado`, que define se lançamentos dessa categoria contam para o resultado (receita/despesa) da imobiliária.
- `lancamento-financeiro` (idem): o pagamento de aluguel passa a gerar também um lançamento de entrada na categoria `taxa_administracao`, além do lançamento bruto de `aluguel`.
- `financeiro-dashboard` (idem): os indicadores de receitas/despesas/saldo do período passam a considerar apenas categorias com `impacta_resultado = true`.
- `relatorio-financeiro` (idem): os relatórios de receitas e despesas seguem a mesma regra de filtro por `impacta_resultado`.

## Impact

- **Banco de dados**: nova coluna `impacta_resultado` em `categorias_financeiras`; migração de dados para marcar `aluguel`, `caucao`, `devolucao_caucao`, `repasse_proprietario` como `false`.
- **Backend**: `PagamentoAluguelService` (novo lançamento de taxa), `IndicadoresFinanceirosService`, `RelatorioFinanceiroController`; testes já existentes que verificam "receitas do período"/"saldo" e o lançamento único de aluguel precisam ser atualizados.
- **Frontend**: nenhuma mudança de estrutura — os mesmos cards/relatórios passam a exibir valores corretos.

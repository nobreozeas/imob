## ADDED Requirements

### Requirement: Categoria financeira define se impacta o resultado da imobiliária
Toda categoria financeira SHALL possuir a flag `impacta_resultado` (booleana, padrão `true`), que determina se lançamentos dessa categoria SHALL ser somados nos indicadores e relatórios de receita/despesa/saldo da imobiliária.

#### Scenario: Categorias de valores de terceiros nascem com impacta_resultado = false
- **WHEN** as categorias padrão `aluguel`, `caucao`, `devolucao_caucao` e `repasse_proprietario` são seedadas
- **THEN** todas são criadas com `impacta_resultado = false`

#### Scenario: Categorias de receita/despesa própria nascem com impacta_resultado = true
- **WHEN** as categorias padrão `taxa_administracao`, `receita_diversa`, `despesa_operacional`, `despesa_administrativa` (e demais categorias de receita/despesa própria da imobiliária) são seedadas
- **THEN** todas são criadas com `impacta_resultado = true`

#### Scenario: Nova categoria criada manualmente impacta o resultado por padrão
- **WHEN** o usuário cria uma nova categoria financeira sem informar `impacta_resultado`
- **THEN** a categoria é criada com `impacta_resultado = true`

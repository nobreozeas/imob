## Context

`PagamentoAluguelService::registrar()` hoje cria um único lançamento financeiro automático por pagamento: uma entrada na categoria `aluguel`, com o **valor total pago** (aluguel + encargos + multa + juros − desconto). O repasse ao proprietário (`RepasseProprietarioService::gerarPendente()`) já calcula corretamente `valor_bruto`/`valor_taxa_administracao`/`valor_liquido`, mas só gera a saída financeira correspondente quando o repasse é marcado como pago — o que pode acontecer dias ou semanas depois, em outro mês de referência.

`IndicadoresFinanceirosService::resumoMensal()` soma **todas** as entradas/saídas pagas no período, sem distinguir categoria, para calcular "Receitas do período", "Despesas do período" e "Saldo do período". Isso faz o valor bruto do aluguel (que na maior parte pertence ao proprietário) aparecer como receita da imobiliária, e o mesmo vale para caução (recebida/devolvida) e para o próprio repasse (que é a contrapartida do aluguel, não uma despesa operacional). O PRD (`docs/PRD-Sistema-Gestao-Imobiliaria.md`, regra 11-14 da seção 33) e a doc de Gestão Financeira (seção 9-10) já deixam explícito que só a taxa de administração é receita da imobiliária — a implementação atual não aplica essa regra nos indicadores.

## Goals / Non-Goals

**Goals:**
- Fazer "Receitas do período", "Despesas do período" e "Saldo do período" (dashboard e relatórios) refletirem apenas o resultado real da imobiliária: taxa de administração, receitas diversas, despesas operacionais/administrativas etc.
- Reconhecer a receita da taxa de administração no momento do recebimento do aluguel, não no momento (posterior e variável) do pagamento do repasse.
- Preservar o lançamento bruto de `aluguel` como está hoje (ele continua útil para conferência de recebimento e para os indicadores de "Aluguéis recebidos/em aberto/vencidos", que já são calculados a partir de `parcelas_aluguel`, não de `lancamentos_financeiros`).

**Non-Goals:**
- Não muda o Fluxo de Caixa (`FluxoCaixaService`): ele representa movimentação real de caixa da imobiliária (inclui valores de terceiros em trânsito) e continua somando todos os lançamentos por `data_vencimento`/`data_pagamento`, independente de categoria.
- Não decide o tratamento de multa/juros por atraso como receita própria ou repassável — hoje eles são somados dentro do valor da parcela e do lançamento bruto de `aluguel`, sem lançamento próprio; fica como questão em aberto (mesma decisão já registrada em `gestao-financeira/design.md`).
- Não reprocessa cauções: `caucao`/`devolucao_caucao` já não deveriam contar como resultado (regra 11-14 do PRD); esta mudança apenas passa a aplicar isso de fato nos indicadores, via a mesma flag.

## Decisions

### 1. Flag `impacta_resultado` na categoria, não filtro por `origem` no lançamento
Adicionar `impacta_resultado` (boolean, default `true`) em `categorias_financeiras`, marcando `false` para `aluguel`, `caucao`, `devolucao_caucao` e `repasse_proprietario`. Alternativa descartada: filtrar por `origem` do lançamento (`pagamento_aluguel`, `caucao`, etc.) — rejeitada porque o novo lançamento de `taxa_administracao` nasce com a mesma `origem = pagamento_aluguel` do lançamento bruto de aluguel (ambos vêm do mesmo evento), então `origem` sozinha não distingue os dois. A categoria já é o eixo natural dessa distinção e é editável pelo usuário (CRUD de categorias), então a flag fica visível e ajustável sem alterar código.

### 2. Taxa reconhecida no recebimento do aluguel, reaproveitando o cálculo do repasse
`PagamentoAluguelService::registrar()` passa a chamar `RepasseProprietarioService::gerarPendente($parcela)` **antes** de criar os lançamentos financeiros (ou captura o `RepasseProprietario` retornado) e usa `$repasse->valor_taxa_administracao` para criar o segundo lançamento (entrada, categoria `taxa_administracao`, mesma `origem = pagamento_aluguel`, vinculado ao mesmo `contrato_id`/`parcela_aluguel_id`). Isso evita duplicar a lógica de cálculo percentual/valor fixo, que já existe em `RepasseProprietarioService`. Alternativa descartada: recalcular a taxa dentro de `PagamentoAluguelService` — rejeitada por duplicar regra de negócio já centralizada.

### 3. Backfill dos lançamentos já existentes
Uma migration de dados cria o lançamento de `taxa_administracao` retroativamente para todo `RepasseProprietario` já existente cuja parcela tenha um lançamento de `aluguel` mas ainda não tenha um lançamento de `taxa_administracao` correspondente (verificado por `parcela_aluguel_id` + categoria), usando o `valor_taxa_administracao` já armazenado no repasse e a `data_pagamento` da parcela. A migration é idempotente (não duplica se rodar mais de uma vez). Sem esse backfill, os indicadores de meses já fechados ficariam com receita zerada para pagamentos antigos.

### 4. Indicadores e relatórios filtram por categoria com `impacta_resultado = true`
`IndicadoresFinanceirosService::resumoMensal()` (receitas/despesas/saldo) e `RelatorioFinanceiroController` (relatórios de receitas/despesas) passam a fazer `whereHas('categoria', fn ($q) => $q->where('impacta_resultado', true))` antes de somar. Os demais indicadores (aluguéis recebidos/em aberto/vencidos, repasses pendentes/pagos, cauções recebidas) não mudam — já são calculados sobre `parcelas_aluguel`/`repasses_proprietarios` diretamente, não sobre a soma genérica de lançamentos.

## Risks / Trade-offs

- [Backfill duplicar lançamentos se rodado novamente ou se algum repasse não tiver parcela associada] → migration verifica existência prévia por `parcela_aluguel_id` + categoria `taxa_administracao` antes de inserir; repasses sem `parcela_aluguel_id` (não deveria existir, a coluna é obrigatória) são ignorados com segurança.
- [Categoria `taxa_administracao` inexistente em bases que não rodaram o seeder do `gestao-financeira`] → `MovimentacaoFinanceiraService::registrar()` já falha explicitamente (`firstOrFail`) nesse caso, então o erro é visível em vez de silencioso.
- [Usuário reclassificar manualmente uma categoria de `impacta_resultado = true` para `false` sem entender o efeito retroativo] → o campo é exibido no CRUD de categorias com o nome já autoexplicativo; validação adicional de UX fica fora do escopo desta correção pontual.

## Migration Plan

1. Migration: adicionar `impacta_resultado` boolean (default `true`) em `categorias_financeiras`.
2. Migration de dados: marcar `impacta_resultado = false` para as categorias `aluguel`, `caucao`, `devolucao_caucao`, `repasse_proprietario`.
3. Migration de dados: backfill dos lançamentos de `taxa_administracao` ausentes para repasses já existentes.
4. Atualizar `PagamentoAluguelService` para criar o lançamento de `taxa_administracao` em todo pagamento novo.
5. Atualizar `IndicadoresFinanceirosService` e `RelatorioFinanceiroController` para filtrar por `impacta_resultado`.
6. Atualizar testes existentes (`PagamentoAluguelTest`, `IndicadoresFinanceirosServiceTest`, `IndicadoresFinanceirosTest`) que hoje esperam um único lançamento de `aluguel` e receita bruta igual ao valor pago.

Rollback: reverter as migrations remove a coluna e os lançamentos de `taxa_administracao` criados pelo backfill (a migration de backfill deve ter `down()` removendo lançamentos com `origem = pagamento_aluguel` e categoria `taxa_administracao` criados por ela); aceitável em ambiente de desenvolvimento, sem plano de rollback em produção pois o sistema ainda não foi lançado.

## Open Questions

- Multa e juros por atraso continuam sem lançamento próprio (somados dentro da entrada bruta de `aluguel`). Quando isso for endereçado, decidir se são receita da imobiliária, do proprietário, ou configurável — mesma pergunta em aberto do PRD (seção 38, item 7).

## Context

O módulo de contratos (`gestao-de-contratos`, implementado mas ainda não arquivado em `openspec/specs/`) já criou `movimentacoes_financeiras` (migration `2026_07_04_100006`) e `MovimentacaoFinanceiraService::registrar(tipo, categoria, valor, referencias)`, chamado por `PagamentoAluguelService`, `RepasseProprietarioService` e `MovimentacaoCaucaoService` (2x — recebimento e devolução). A `categoria` hoje é uma string fixa validada por CHECK constraint (`aluguel`, `caucao`, `multa`, `juros`, `repasse_proprietario`, `devolucao_caucao`), sem tabela própria, sem `status`, sem lançamento manual e sem nenhuma tela.

`docs/Funcionalidade-04-Gestao-Financeira.md` especifica uma estrutura bem mais rica (`lancamentos_financeiros` + `categorias_financeiras` + `historicos_financeiros`, com `status`, `origem`, `codigo`, vínculo a cliente/imóvel, cancelamento/estorno com motivo). Esta mudança evolui a estrutura existente para essa forma, mantendo os 4 pontos de chamada já implementados intactos na interface pública.

## Goals / Non-Goals

**Goals:**
- Substituir `movimentacoes_financeiras` por `lancamentos_financeiros` com o schema completo da doc, preservando os dados já gravados.
- Manter `MovimentacaoFinanceiraService::registrar()` como único ponto de entrada usado pelos services de contrato (pagamento, repasse, caução), só que agora gravando lançamentos já `pago` com `origem` correta e `categoria_financeira_id` resolvido a partir do slug.
- Adicionar lançamento manual (receita diversa / despesa) com ciclo de status pendente → pago, e ações de cancelar/estornar.
- Entregar indicadores (dashboard), fluxo de caixa, inadimplência e relatórios básicos, todos somente leitura sobre `lancamentos_financeiros` e `parcelas_aluguel`.
- Permissões granulares `financeiro.*` e policies coerentes com a matriz do PRD (Administrador e Financeiro têm acesso total; Atendente e Corretor não têm acesso ao financeiro).

**Non-Goals:**
- Sem PIX/boleto automático, conciliação bancária, plano de contas avançado, DRE ou integração contábil (fora do MVP, seção 4/37 do PRD e seção 4 da Funcionalidade-04).
- Sem exportação em PDF/Excel dos relatórios (questão em aberto #10 do PRD — relatórios ficam on-screen, com filtros, nesta primeira versão).
- Sem múltiplas contas bancárias / centro de custo.
- Não altera as regras de cálculo de multa, juros, taxa de administração ou caução já implementadas em `gestao-de-contratos` — apenas onde e como o lançamento financeiro resultante é persistido.

## Decisions

### 1. Renomear/estender em vez de conviver com duas tabelas
Optou-se por migrar `movimentacoes_financeiras` → `lancamentos_financeiros` (uma migration `rename` + `add columns` + backfill), em vez de manter as duas tabelas (uma "automática" e outra "manual/rica"). Alternativa descartada: manter `movimentacoes_financeiras` só para lançamentos automáticos e criar `lancamentos_financeiros` só para manuais — rejeitada porque duplicaria a lógica de indicadores/fluxo de caixa/inadimplência (teriam que somar duas tabelas) e contraria a doc, que trata tudo como um único ledger diferenciado pelo campo `origem`.

### 2. `MovimentacaoFinanceiraService::registrar()` continua sendo a fachada usada pelos services de contrato
Assinatura muda de `registrar(string $tipo, string $categoria, float $valor, array $referencias = [])` para aceitar também `origem` (obrigatório) e resolver `categoria_financeira_id` internamente via slug (`CategoriaFinanceira::where('slug', $categoria)->firstOrFail()`), preservando os 4 call sites com o mínimo de diff — cada um passa a incluir `'origem' => 'pagamento_aluguel' | 'repasse_proprietario' | 'caucao' | 'movimentacao_caucao'` no array de referências. Como esses lançamentos nascem já liquidados (pagamento já ocorreu no momento em que o service é chamado), `status` é sempre criado como `pago` nesse fluxo, com `pago_por = criado_por` e `data_pagamento = data_movimentacao`.

### 3. `categoria_financeira_id` obrigatório, resolvido por slug fixo nos call sites automáticos
As categorias usadas pelos fluxos automáticos (`aluguel`, `caucao`, `multa`, `juros`, `repasse_proprietario`, `devolucao_caucao`) são seedadas com slugs estáveis e marcadas como não editáveis via flag `ativa` (permanecem sempre ativas; exclusão lógica bloqueada quando houver lançamentos vinculados, verificada na policy/service). O CRUD de categorias serve principalmente para as categorias adicionais usadas em lançamentos manuais (despesa/receita diversa) listadas na seção 6 e 49 da doc.

### 4. Cancelamento e estorno como transições de status, não exclusão
`cancelar` (só permitido em `pendente`) e `estornar` (só permitido em `pago`) exigem motivo obrigatório, preservam o lançamento original (soft delete nunca usado para isso) e criam entrada em `historicos_financeiros`. Estorno de um lançamento `pago` não cria um segundo lançamento negativo nesta primeira versão — apenas muda o status para `estornado` e registra `motivo_estorno`/`estornado_por`; o valor deixa de contar nos indicadores (que somam apenas `pago`). Simplificação deliberada para o MVP; lançamento de estorno como partida dobrada fica para uma iteração futura caso a imobiliária precise de rastreabilidade contábil mais forte.

### 5. Indicadores, fluxo de caixa e inadimplência como Services de leitura, sem tabelas de cache
`IndicadoresFinanceirosService`, `FluxoCaixaService` e `InadimplenciaService` fazem agregações diretas via query builder sobre `lancamentos_financeiros` (status `pago` para realizado, `pendente` para previsto) e `parcelas_aluguel` (para inadimplência, que é definida sobre parcelas, não sobre lançamentos). Volume esperado no MVP não justifica materialização/cache.

### 6. Edição de lançamento automático é bloqueada
Lançamentos com `origem != 'manual'` não podem ser editados livremente (só cancelados/estornados) — reflete a regra da seção 24 da doc ("lançamentos originados automaticamente... devem ter edição limitada"). A policy `LancamentoFinanceiroPolicy::update()` nega para `origem != 'manual'`.

## Risks / Trade-offs

- [Migração de dados de `movimentacoes_financeiras` para `lancamentos_financeiros` perde granularidade se algum campo novo não tiver valor default sensato] → todos os registros existentes são poucos (ambiente ainda em desenvolvimento, sem dados de produção) e recebem `status = 'pago'`, `origem` inferida pela `categoria` antiga (`aluguel`→`pagamento_aluguel`, `repasse_proprietario`→`repasse_proprietario`, `caucao`/`devolucao_caucao`→`caucao`, demais→`ajuste`), `codigo` gerado sequencialmente na própria migration.
- [Resolver `categoria_financeira_id` por slug em runtime pode falhar silenciosamente se o seeder não rodar] → seeder de categorias roda como parte da migration de dados (não como seeder separado opcional), garantindo que as categorias automáticas sempre existam antes de qualquer lançamento.
- [Indicadores calculados via agregação direta podem ficar lentos conforme a base cresce] → aceitável para o volume do MVP; índices em `status`, `data_pagamento`, `data_vencimento` e `categoria_financeira_id` mitigam no curto prazo.

## Migration Plan

1. Criar `categorias_financeiras` e seedar categorias padrão (seção 49 da doc).
2. Criar `lancamentos_financeiros` com o schema completo (seção 37 da doc) e `historicos_financeiros` (seção 38).
3. Migration de dados: copiar cada linha de `movimentacoes_financeiras` para `lancamentos_financeiros` (mapeamento do item de risco acima), depois `Schema::dropIfExists('movimentacoes_financeiras')`.
4. Atualizar `MovimentacaoFinanceira` → renomear para `LancamentoFinanceiro` (model), atualizar os 4 call sites e os testes existentes que os exercitam (`PagamentoAluguelTest`, `RepasseProprietarioTest`, `MovimentacaoCaucaoTest`).
5. Construir o restante do módulo (CRUD manual, ações, dashboard, fluxo de caixa, inadimplência, relatórios, permissões, telas) sobre a nova estrutura.

Rollback: reverter a migration de dados restauraria `movimentacoes_financeiras` a partir de `lancamentos_financeiros` (mapeamento inverso) — aceitável em ambiente de desenvolvimento; não há plano de rollback em produção pois o sistema ainda não foi lançado.

## Open Questions

- Multas e juros recebidos junto ao aluguel: hoje entram compostos dentro da mesma parcela/pagamento e não geram lançamento financeiro separado por categoria `multa`/`juros` (só `aluguel`). Mantido assim nesta mudança — abrir lançamento separado por multa/juros fica para quando a pergunta 7 do PRD ("questões em aberto") for respondida pelo negócio.

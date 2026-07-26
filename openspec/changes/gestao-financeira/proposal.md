## Why

O módulo de contratos (`gestao-de-contratos`) já gera lançamentos financeiros automáticos (pagamento de aluguel, repasse, caução) numa tabela simples `movimentacoes_financeiras`, sem status, categorias configuráveis, lançamentos manuais ou qualquer tela própria. A imobiliária não tem hoje nenhuma visão financeira consolidada: não é possível cadastrar receita/despesa avulsa, marcar/cancelar/estornar um lançamento, ver indicadores do mês, fluxo de caixa, inadimplência ou relatórios básicos — tudo isso é essencial para o MVP (PRD seção 24 e `docs/Funcionalidade-04-Gestao-Financeira.md`) e ainda não existe.

## What Changes

- Criar `categorias_financeiras` (com seed das categorias padrão de entrada/saída da doc) e `historicos_financeiros` (auditoria de ações sobre lançamentos, repasses e caução).
- **BREAKING**: substituir a tabela/model `movimentacoes_financeiras` por `lancamentos_financeiros`, com `codigo`, `categoria_financeira_id`, `cliente_id`, `imovel_id`, `status` (`pendente`/`pago`/`cancelado`/`estornado`), `origem` (`manual`/`pagamento_aluguel`/`repasse_proprietario`/`caucao`/`movimentacao_caucao`/`despesa`/`receita_diversa`/`ajuste`), `motivo_cancelamento`, `motivo_estorno` e rastreio de quem criou/pagou/cancelou/estornou. Migrar os dados existentes da tabela antiga.
- Atualizar `MovimentacaoFinanceiraService`, `PagamentoAluguelService`, `RepasseProprietarioService` e `MovimentacaoCaucaoService` (criados em `gestao-de-contratos`) para escrever nessa nova estrutura, já nascendo com status `pago` e `origem` correta.
- Adicionar CRUD manual de lançamento financeiro (receita diversa e despesa), com ações de marcar como pago, cancelar (com motivo) e estornar (com motivo, preservando o lançamento original).
- Adicionar indicadores e gráficos financeiros (dashboard), relatório de fluxo de caixa por período, listagem de inadimplência (parcelas vencidas/não pagas) e relatórios básicos (receitas, despesas, aluguéis recebidos/em aberto/vencidos, repasses, cauções movimentadas).
- Adicionar permissões granulares do módulo financeiro (`financeiro.*`) e policies correspondentes.
- Criar as telas Vue/Inertia do módulo Financeiro (dashboard, lançamentos, fluxo de caixa, inadimplência, categorias) e o menu de navegação.

## Capabilities

### New Capabilities
- `categoria-financeira`: cadastro e manutenção das categorias de entrada/saída usadas pelos lançamentos financeiros.
- `lancamento-financeiro`: núcleo do ledger financeiro — lançamentos manuais (receita/despesa) e automáticos, ciclo de status (pendente/pago/cancelado/estornado), listagem com filtros e histórico de auditoria.
- `financeiro-dashboard`: indicadores (receitas, despesas, saldo, aluguéis recebidos/em aberto/vencidos, repasses pendentes/pagos, cauções recebidas) e gráficos do mês.
- `fluxo-caixa`: relatório de entradas, saídas e saldo (previsto/realizado) por dia, mês ou período customizado.
- `inadimplencia`: identificação e listagem de parcelas vencidas e não pagas, com indicadores de atraso.
- `relatorio-financeiro`: relatórios básicos filtráveis por período/proprietário/imóvel/contrato/status (receitas, despesas, aluguéis, repasses, cauções).

### Modified Capabilities
(nenhuma — os specs de `pagamento-aluguel`, `repasse-proprietario` e `movimentacao-caucao` ainda não foram arquivados em `openspec/specs/`; o ajuste desses services para gravar em `lancamentos_financeiros` é tratado como detalhe de implementação desta mudança, descrito no design.md)

## Impact

- **Banco de dados**: novas tabelas `categorias_financeiras`, `lancamentos_financeiros`, `historicos_financeiros`; drop/migração de dados de `movimentacoes_financeiras`.
- **Backend**: novos models, services (`LancamentoFinanceiroService`, `ReceitaFinanceiraService`, `DespesaFinanceiraService`, `FluxoCaixaService`, `InadimplenciaService`, `IndicadoresFinanceirosService`, `HistoricoFinanceiroService`), form requests, policies, controllers e rotas; ajuste dos services financeiros existentes do módulo de contratos.
- **Frontend**: novas páginas `resources/js/Pages/Financeiro/*`, componentes `resources/js/Components/Financeiro/*`, tipos TypeScript, item de menu.
- **Permissões**: novo conjunto `financeiro.*` no seeder de permissões, atribuído aos perfis Administrador e Financeiro.

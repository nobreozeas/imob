## 1. Banco de dados — Novas tabelas

- [x] 1.1 Criar migration `create_categorias_financeiras_table`: uuid PK, `nome` string, `tipo` CHECK (`entrada`,`saida`), `slug` string unique, `descricao` text nullable, `ativa` boolean default true, timestamps, `deleted_at`
- [x] 1.2 Criar seeder `CategoriaFinanceiraSeeder`: cria as categorias padrão de entrada (`aluguel`, `receita_diversa`, `taxa_administracao`, `multa_atraso`, `juros_atraso`, `caucao`, `ajuste_positivo`) e de saída (`repasse_proprietario`, `despesa_operacional`, `despesa_administrativa`, `fornecedor`, `devolucao_caucao`, `manutencao_imovel`, `comissao_corretor`, `ajuste_negativo`) com slugs estáveis; registrado no `DatabaseSeeder`
- [x] 1.3 Criar migration `create_lancamentos_financeiros_table`: uuid PK, `codigo` string unique, `tipo` CHECK (`entrada`,`saida`), `categoria_financeira_id` FK, `contrato_id`/`parcela_aluguel_id`/`repasse_proprietario_id`/`caucao_contrato_id`/`movimentacao_caucao_id`/`imovel_id`/`cliente_id` FKs nullable, `descricao` text nullable, `valor` decimal(12,2), `data_vencimento` date nullable, `data_pagamento` date nullable, `forma_pagamento` string nullable, `status` CHECK (`pendente`,`pago`,`cancelado`,`estornado`), `origem` CHECK (`manual`,`pagamento_aluguel`,`repasse_proprietario`,`caucao`,`movimentacao_caucao`,`despesa`,`receita_diversa`,`ajuste`), `observacoes` text nullable, `motivo_cancelamento` text nullable, `motivo_estorno` text nullable, `criado_por`/`pago_por`/`cancelado_por`/`estornado_por` FKs users nullable, timestamps, `deleted_at`; índices em `status`, `data_pagamento`, `data_vencimento`, `categoria_financeira_id`
- [x] 1.4 Criar migration `create_historicos_financeiros_table`: uuid PK, `lancamento_financeiro_id` FK nullable, `entidade_tipo` string, `entidade_id` uuid, `acao` string, `descricao` text nullable, `dados_anteriores` json nullable, `dados_novos` json nullable, `criado_por` FK users nullable, `created_at`

## 2. Banco de dados — Migração de `movimentacoes_financeiras`

- [x] 2.1 Migration de dados `2026_07_05_100004_migrate_movimentacoes_financeiras_to_lancamentos`: dentro de uma transação, copia cada linha de `movimentacoes_financeiras` para `lancamentos_financeiros` com `status = 'pago'`, `data_pagamento = data_movimentacao`, `codigo` sequencial (`LF-000001`...), `categoria_financeira_id` resolvido pelo slug, `origem` mapeada; roda `CategoriaFinanceiraSeeder` no próprio `up()` para garantir que as categorias existam antes do mapeamento
- [x] 2.2 Na mesma migration, `Schema::dropIfExists('movimentacoes_financeiras')`; `down()` recria a tabela antiga e devolve os dados (rollback best-effort para ambiente de desenvolvimento)
- [x] 2.3 Migrations e seeders rodados em ambiente local (`docker compose exec app php artisan migrate --force`)

## 3. Models

- [x] 3.1 Criar `app/Models/CategoriaFinanceira.php`
- [x] 3.2 Criar `app/Models/LancamentoFinanceiro.php` (substitui `MovimentacaoFinanceira`)
- [x] 3.3 Remover `app/Models/MovimentacaoFinanceira.php`
- [x] 3.4 Criar `app/Models/HistoricoFinanceiro.php`

## 4. Services — Ledger central

- [x] 4.1 `MovimentacaoFinanceiraService::registrar()` atualizado: aceita `origem` obrigatório, resolve `categoria_financeira_id` por slug, gera `codigo` sequencial (`gerarCodigo()`, reaproveitado pelos services de lançamento manual), cria `LancamentoFinanceiro` com `status = 'pago'`
- [x] 4.2 Atualizados os 4 call sites (`PagamentoAluguelService`, `RepasseProprietarioService`, `MovimentacaoCaucaoService` recebimento e devolução) com `origem`, `imovel_id`, `cliente_id` e `movimentacao_caucao_id` (nos dois últimos)
- [x] 4.3 Criar `app/Services/Financeiro/LancamentoFinanceiroService.php` com `marcarComoPago`/`cancelar`/`estornar`
- [x] 4.4 Criar `app/Services/Financeiro/HistoricoFinanceiroService.php`

## 5. Services — Lançamentos manuais

- [x] 5.1 Criar `app/Services/Financeiro/ReceitaFinanceiraService.php`
- [x] 5.2 Criar `app/Services/Financeiro/DespesaFinanceiraService.php`
- [x] 5.3 Criar `StoreReceitaFinanceiraRequest`/`StoreDespesaFinanceiraRequest`
- [x] 5.4 Criar `UpdateLancamentoFinanceiroRequest` (edição restrita a `origem = manual` e status `pendente`, aplicada no controller)
- [x] 5.5 Criar `MarcarLancamentoComoPagoRequest`, `CancelarLancamentoFinanceiroRequest`, `EstornarLancamentoFinanceiroRequest`; extra: `StoreCategoriaFinanceiraRequest`/`UpdateCategoriaFinanceiraRequest` para o CRUD de categorias

## 6. Services — Indicadores, fluxo de caixa e inadimplência

- [x] 6.1 Criar `app/Services/Financeiro/IndicadoresFinanceirosService.php`
- [x] 6.2 Criar `app/Services/Financeiro/FluxoCaixaService.php`
- [x] 6.3 Criar `app/Services/Financeiro/InadimplenciaService.php`
- [x] 6.4 Testes unitários dos 3 services em `tests/Unit/Services/Financeiro/` (rodam contra o Postgres de teste real do ambiente, com `firstOrCreate` para as categorias — ver nota de ambiente na seção 15)

## 7. Permissões e Policies

- [x] 7.1 Criar `database/seeders/FinanceiroPermissionsSeeder.php`, registrado no `DatabaseSeeder` antes do `PerfisEPermissoesSeeder`
- [x] 7.2 `PerfisEPermissoesSeeder`: role `financeiro` recebe todas as permissões `financeiro.*`
- [x] 7.3 Criar `app/Policies/LancamentoFinanceiroPolicy.php`
- [x] 7.4 Criar `app/Policies/CategoriaFinanceiraPolicy.php` (bloqueio de exclusão em uso é feito no controller/service, não na policy, para manter a policy só sobre permissão)
- [x] 7.5 Criar `app/Policies/RelatorioFinanceiroPolicy.php`, registrada via `Gate::define('ver-relatorios-financeiros', ...)` no `AppServiceProvider` (não é uma model policy, pois não há model de relatório)
- [x] 7.6 `LancamentoFinanceiro::class` e `CategoriaFinanceira::class` registrados no `AppServiceProvider`

## 8. Controllers e Rotas

- [x] 8.1 Criar `app/Http/Controllers/Financeiro/LancamentoFinanceiroController.php`: `index`, `show`, `update`, `destroy`; `store` foi dividido em `storeReceita`/`storeDespesa` (cada um com seu próprio FormRequest tipado) em vez de um único `store` genérico — necessário porque um `FormRequest` só valida automaticamente quando resolvido via injeção de método, não quando escolhido dinamicamente em runtime
- [x] 8.2 Adicionados `marcarComoPago`, `cancelar`, `estornar`
- [x] 8.3 Criar `app/Http/Controllers/Financeiro/CategoriaFinanceiraController.php`
- [x] 8.4 Criar `app/Http/Controllers/Financeiro/FinanceiroDashboardController.php` (inclui lista de proprietários para o filtro)
- [x] 8.5 Criar `app/Http/Controllers/Financeiro/FluxoCaixaController.php`
- [x] 8.6 Criar `app/Http/Controllers/Financeiro/InadimplenciaController.php`
- [x] 8.7 Criar `app/Http/Controllers/Financeiro/RelatorioFinanceiroController.php`
- [x] 8.8 Rotas adicionadas em `routes/web.php`; extra: `GET financeiro/repasses` (`financeiro.repasses.index`) adicionado ao `RepasseProprietarioController` existente (tarefa 13.1) e `POST financeiro/lancamentos/receitas`/`despesas` no lugar do `store` genérico do resource

## 9. Frontend — Types

- [x] 9.1 Criar `resources/js/types/categoriaFinanceira.ts`
- [x] 9.2 Criar `resources/js/types/lancamentoFinanceiro.ts` (inclui `LancamentoFinanceiroFiltros`/`LancamentoFinanceiroPaginado`)

## 10. Frontend — Dashboard financeiro

- [x] 10.1 Criar `resources/js/Components/Financeiro/FinanceiroResumoCards.vue`
- [ ] 10.2 Gráficos (`GraficoReceitasDespesas`, `GraficoRecebimentos`, `GraficoInadimplencia`) **não implementados** — não há biblioteca de gráficos no projeto (`package.json` não tem chart.js/apexcharts/recharts) e adicionar uma nova dependência não fazia parte do pedido; os dados que alimentariam os gráficos já estão disponíveis via `IndicadoresFinanceirosService`/`FluxoCaixaService` para uma iteração futura
- [ ] 10.3 `FiltrosFinanceiro.vue` **não extraído como componente separado** — os filtros do dashboard foram implementados inline em `Dashboard.vue`, seguindo o padrão já usado em `Contratos/Index.vue` (filtros inline na página, sem componente de filtro genérico)
- [x] 10.4 Criar `resources/js/Pages/Admin/Financeiro/Dashboard.vue` com cards, filtros (período, proprietário, status de imóvel) e atalhos rápidos

## 11. Frontend — Lançamentos

- [x] 11.1 Criar `resources/js/Components/Financeiro/StatusLancamentoBadge.vue`
- [x] 11.2 Criar `resources/js/Components/Financeiro/TabelaLancamentos.vue`
- [x] 11.3 Criar `resources/js/Components/Financeiro/ModalReceita.vue` e `ModalDespesa.vue`
- [ ] 11.4 `ModalMarcarComoPago`/`ModalCancelarLancamento`/`ModalEstornarLancamento` **implementados como diálogos SweetAlert2 inline em `TabelaLancamentos.vue`**, não como componentes `.vue` separados — mesmo padrão já usado em `TabelaRepasses.vue` para ações de uma ou duas perguntas (data/motivo)
- [x] 11.5 Criar `resources/js/Pages/Admin/Financeiro/Lancamentos/Index.vue` e `Show.vue`

## 12. Frontend — Fluxo de caixa e inadimplência

- [x] 12.1 Criar `resources/js/Pages/Admin/Financeiro/FluxoCaixa.vue`
- [x] 12.2 Criar `resources/js/Components/Financeiro/TabelaInadimplencia.vue` e `resources/js/Pages/Admin/Financeiro/Inadimplencia.vue` com indicadores e ação "Ver contrato"; ação "Enviar notificação por email" **não incluída** — depende do módulo de Notificações (PRD seção 26), fora do escopo desta mudança

## 13. Frontend — Repasses e Categorias

- [x] 13.1 Criar `resources/js/Pages/Admin/Financeiro/Repasses/Index.vue`, reaproveitando `RepasseProprietarioService`/rotas já existentes (`repasses-proprietarios.marcar-como-pago`/`cancelar`), agora com listagem própria via `RepasseProprietarioController::index` (novo) em vez de um `ModalPagarRepasse.vue` separado — ação de marcar como pago feita via SweetAlert2, mesmo padrão de `TabelaRepasses.vue`
- [x] 13.2 Criar `resources/js/Pages/Admin/Financeiro/Categorias/Index.vue` com CRUD simples de categorias financeiras (modal)

## 14. Navegação

- [x] 14.1 Item "Financeiro" atualizado em `resources/js/composables/useNavigation.ts` (Dashboard, Lançamentos, Repasses, Fluxo de Caixa, Inadimplência, Categorias), consumido por `AppSidebar.vue`; cada item usa a permissão correspondente (`financeiro.visualizar` ou `repasses.visualizar`)

## 15. Testes backend

- [x] 15.1 `PagamentoAluguelTest`, `RepasseProprietarioTest`, `MovimentacaoCaucaoTest` atualizados para `LancamentoFinanceiro`; `ContratoTestHelpers::setUp()` agora roda `CategoriaFinanceiraSeeder` para todo teste que usa a trait
- [x] 15.2 Criar `tests/Feature/Financeiro/CategoriaFinanceiraTest.php`
- [x] 15.3 Criar `tests/Feature/Financeiro/LancamentoFinanceiroManualTest.php`
- [x] 15.4 Criar `tests/Feature/Financeiro/AcoesLancamentoFinanceiroTest.php`
- [x] 15.5 Criar `tests/Feature/Financeiro/IndicadoresFinanceirosTest.php`
- [x] 15.6 Criar `tests/Feature/Financeiro/FluxoCaixaTest.php`
- [x] 15.7 Criar `tests/Feature/Financeiro/InadimplenciaTest.php`
- [x] 15.8 Criar `tests/Feature/Financeiro/PermissoesFinanceiroTest.php`
- [x] 15.9 Suíte completa rodada (`docker compose exec -e APP_ENV=testing app ./vendor/bin/phpunit`): 74 testes, 73 passam — a única falha é `ExampleTest` (scaffold padrão do Laravel, testa `GET /` = 200, mas a rota redireciona para `/login`; pré-existente, não relacionado a esta mudança). `vue-tsc --noEmit` roda com os mesmos erros de `Cannot find name 'route'` que já existem em **todo** o restante do código (Ziggy não tem tipos globais declarados no projeto); nenhum arquivo do módulo Financeiro introduz um erro de tipo além desse padrão pré-existente
- Nota de ambiente: nesta instância, `APP_ENV=testing`/`DB_CONNECTION=sqlite` do `phpunit.xml` **não** têm efeito — os testes rodam contra o Postgres real de desenvolvimento (`getenv()` do container prevalece sobre o `force="true"` do phpunit). Como consequência, dados persistem entre métodos de teste dentro da mesma execução; por isso os testes desta mudança usam `firstOrCreate` para categorias financeiras em vez de `create`. Também foi criado `config/inertia.php` (antes inexistente) corrigindo `pages.paths` de `resources/js/pages` (default do pacote, minúsculo) para `resources/js/Pages` (convenção real do projeto) — sem isso, `assertInertia()->component(...)` falha para **qualquer** página do sistema, não só as novas.

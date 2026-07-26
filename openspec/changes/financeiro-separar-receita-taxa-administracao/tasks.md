## 1. Banco de dados

- [x] 1.1 Criar migration `add_impacta_resultado_to_categorias_financeiras_table`: `boolean('impacta_resultado')->default(true)`
- [x] 1.2 Criar migration de dados `marcar_categorias_de_transito_sem_impacto_no_resultado`: `UPDATE categorias_financeiras SET impacta_resultado = false WHERE slug IN ('aluguel', 'caucao', 'devolucao_caucao', 'repasse_proprietario')`; `down()` reverte para `true`
- [x] 1.3 Criar migration de dados `backfill_lancamentos_taxa_administracao`: para cada `RepasseProprietario` cuja `parcela_aluguel_id` já possua um lançamento de categoria `aluguel` mas NÃO possua um lançamento de categoria `taxa_administracao` vinculado à mesma parcela, insere o lançamento faltante; marca os lançamentos criados com `observacoes = 'Backfill automático (financeiro-separar-receita-taxa-administracao)'` para permitir um `down()` seguro (remove só os que ela criou)
- [x] 1.4 Migrations rodadas em ambiente local (`docker compose exec app php artisan migrate --force`); verificado via tinker que as 4 categorias de trânsito ficaram com `impacta_resultado = false` e que os 2 repasses pré-existentes no ambiente de dev receberam o lançamento de `taxa_administracao` retroativo

## 2. Models e seeders

- [x] 2.1 `app/Models/CategoriaFinanceira.php`: `impacta_resultado` adicionado ao `$fillable` e `$casts` (boolean)
- [x] 2.2 `database/seeders/CategoriaFinanceiraSeeder.php`: categorias de trânsito seedadas com `impacta_resultado => false`; demais com `true`. Extra não previsto: a seeder agora verifica `Schema::hasColumn('categorias_financeiras', 'impacta_resultado')` antes de incluir a coluna no insert — ela é chamada também de dentro da migration `2026_07_05_100004` (criada em `gestao-financeira`), que roda **antes** da migration que cria essa coluna; sem essa checagem, um `migrate:fresh` do zero (como o que o `RefreshDatabase` dos testes dispara) quebrava com "column impacta_resultado does not exist"

## 3. Services

- [x] 3.1 `app/Services/Contratos/PagamentoAluguelService.php::registrar()`: captura o retorno de `repasseProprietarioService->gerarPendente($parcela)` e usa `$repasse->valor_taxa_administracao` para criar o segundo lançamento (`taxa_administracao`, entrada, mesma origem/contrato/parcela/imóvel/cliente), dentro da mesma transação
- [x] 3.2 `IndicadoresFinanceirosService::resumoMensal()`: `receitas`/`despesas` agora filtram por `whereHas('categoria', fn ($q) => $q->where('impacta_resultado', true))`; `caucoesRecebidas` e os indicadores baseados em `parcelas_aluguel`/`repasses_proprietarios` não mudaram
- [x] 3.3 `RelatorioFinanceiroController::lancamentos()` (usado pelos relatórios de receitas e despesas) recebeu o mesmo filtro; `lancamentosPorOrigem()` (usado pelo relatório de cauções) não muda

## 4. Testes

- [x] 4.1 `PagamentoAluguelTest`: teste de pagamento passa a verificar também o lançamento de `taxa_administracao` (valor e vínculo com a parcela)
- [x] 4.2 `IndicadoresFinanceirosServiceTest`: fixture do teste original ajustado para incluir o lançamento de `taxa_administracao`; asserções de `receitas`/`saldo` corrigidas para refletir só a taxa
- [x] 4.3 `tests/Feature/Financeiro/IndicadoresFinanceirosTest.php`: não precisou de alteração — usa a categoria `receita_diversa`, que não é de trânsito e já tinha `impacta_resultado = true` por padrão
- [x] 4.4 Criado `test_receitas_e_saldo_consideram_apenas_a_taxa_de_administracao` em `IndicadoresFinanceirosServiceTest`, cobrindo exatamente o cenário do PRD (aluguel 1000/taxa 10% → receita 100; despesa 50 → saldo 50)
- [x] 4.5 Criado `tests/Feature/Financeiro/BackfillTaxaAdministracaoMigrationTest.php`: carrega a migration de backfill diretamente (`require` do arquivo), roda `up()` duas vezes seguidas (confirma que não duplica) e depois `down()` (confirma que remove só o que ela criou)
- [x] 4.6 Suíte completa rodada: 76 testes, 75 passam — único "failure" é o `ExampleTest` pré-existente e não relacionado (`GET /` retorna 302 por redirecionar para `/login`, o teste scaffold espera 200). `vue-tsc --noEmit` conferido: nenhum erro novo (frontend não foi alterado nesta mudança)

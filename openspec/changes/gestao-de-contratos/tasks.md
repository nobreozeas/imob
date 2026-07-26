## 1. Banco de dados — Migrations novas

- [x] 1.1 Criar migration `create_parcelas_aluguel_table`: uuid PK, `contrato_id` FK (`cascadeOnDelete`), `mes_referencia`/`ano_referencia` integer, `data_vencimento` date, `valor_aluguel`/`valor_encargos`/`valor_multa_atraso`/`valor_juros_atraso`/`valor_desconto`/`valor_total`/`valor_pago` decimal(12,2), `data_pagamento` date nullable, `forma_pagamento` string nullable, `status` com CHECK (`pendente`,`pago`,`vencido`,`cancelado`,`pago_parcial`), unique(`contrato_id`,`mes_referencia`,`ano_referencia`), timestamps
- [x] 1.2 Criar migration `create_repasses_proprietarios_table`: uuid PK, `contrato_id`/`imovel_id`/`proprietario_id` FKs, `parcela_aluguel_id` FK unique, `valor_bruto`/`valor_taxa_administracao`/`valor_liquido` decimal(12,2), `status` CHECK (`pendente`,`pago`,`cancelado`), `data_pagamento` date nullable, `forma_pagamento` string nullable, `motivo_cancelamento` text nullable, timestamps
- [x] 1.3 Criar migration `create_movimentacoes_caucao_table`: uuid PK, `caucao_contrato_id` FK (`cascadeOnDelete`, referenciando `contrato_caucoes`), `tipo_movimentacao` CHECK (`recebimento`,`devolucao`,`abatimento`,`retencao_parcial`,`retencao_integral`,`ajuste`), `valor` decimal(12,2), `data_movimentacao` date, `forma_movimentacao` string nullable, `descricao` text nullable, `referencia_debito` string nullable (ex.: id da parcela abatida), `criado_por` FK users nullable, timestamps
- [x] 1.4 Criar migration `create_contrato_rescisoes_table`: uuid PK, `contrato_id` FK unique (`cascadeOnDelete`), `data_rescisao` date, `motivo` text, `solicitado_por` CHECK (`locatario`,`locador`,`imobiliaria`,`acordo`), `meses_restantes` integer nullable, `valor_multa_rescisao`/`valor_desconto`/`valor_final_multa` decimal(12,2) nullable, `debitos_em_aberto` decimal(12,2) default 0, `valor_caucao_retida`/`valor_caucao_abatida`/`valor_caucao_devolvida` decimal(12,2) nullable, `destino_imovel` CHECK (`disponivel`,`inativo`), `acao_parcelas_futuras` CHECK (`cancelar_parcelas_futuras`,`manter_parcelas_futuras`), `observacoes` text nullable, `criado_por` FK users nullable, timestamps
- [x] 1.5 Criar migration `create_contrato_renovacoes_table`: uuid PK, `contrato_original_id`/`novo_contrato_id` FKs para `contratos_locacao`, `data_renovacao` date, `valor_aluguel_anterior`/`valor_aluguel_novo` decimal(12,2), `data_inicio_anterior`/`data_fim_anterior`/`nova_data_inicio`/`nova_data_fim` date, `manter_encargos`/`manter_regras_multa`/`gerar_novas_parcelas` boolean, `caucao_acao` CHECK (`manter`,`devolver`,`complementar`) nullable, `observacoes` text nullable, `criado_por` FK users nullable, timestamps
- [x] 1.6 Criar migration `create_movimentacoes_financeiras_table`: uuid PK, `tipo` CHECK (`entrada`,`saida`), `categoria` CHECK (`aluguel`,`caucao`,`multa`,`juros`,`repasse_proprietario`,`devolucao_caucao`), `descricao` text nullable, `valor` decimal(12,2), `data_movimentacao` date, `forma_pagamento` string nullable, `contrato_id`/`parcela_aluguel_id`/`repasse_proprietario_id`/`caucao_contrato_id` FKs nullable, `criado_por` FK users nullable, timestamps

## 2. Banco de dados — Alterações em tabelas existentes

- [x] 2.1 Migration: adicionar `gerar_parcelas_automaticamente` boolean default true e `quantidade_parcelas` integer nullable em `contratos_locacao`
- [x] 2.2 Migration: adicionar `saldo_atual` decimal(12,2) default 0 em `contrato_caucoes`, com backfill (`valor_caucao - COALESCE(valor_devolvido, 0)` para cauções já recebidas); `status_caucao` passa a ser nullable (sem default), setado apenas por movimentação
- [x] 2.3 Migration de dados: copiar contratos com `status = 'rescindido'` de `contratos_locacao` (campos `data_rescisao`, `motivo_rescisao`, `parte_requerente`) para `contrato_rescisoes`, quando existirem
- [x] 2.4 Migration: recriar CHECK constraint de `contratos_locacao.status` incluindo `vencido` (via `DB::statement` drop/add constraint) e de `parcelas_aluguel.status` (já criada com `vencido` na 1.1)
- [x] 2.5 Migration: remover colunas `data_rescisao`, `motivo_rescisao`, `parte_requerente` de `contratos_locacao` (após 2.3)
- [x] 2.7 Migration extra (não prevista originalmente): adicionar `dias_tolerancia_atraso` integer nullable em `contrato_multas` — campo exigido pelo cálculo de multa por atraso (spec `pagamento-aluguel`) mas ausente do schema criado em `contratos-de-locacao`
- [x] 2.8 Migration extra (não prevista originalmente): adicionar `contrato_anterior_id` (FK nullable auto-referenciada) em `contratos_locacao` — o design.md presumiu que essa coluna já existia (citada no PRD genérico), mas o schema real criado em `contratos-de-locacao` não a possui; necessária para `renovacao-contrato`
- [x] 2.6 Rodar todas as migrations em ambiente local (`docker compose exec app php artisan migrate`)
- [x] 2.9 Wiring extra (não previsto originalmente): `dias_tolerancia_atraso` adicionado a `StoreContratoRequest`/`UpdateContratoRequest` e `ContratoLocacaoService::dadosMultas()` — o campo novo precisa ser configurável, não só existir na tabela

## 3. Models

- [x] 3.1 Criar `app/Models/ParcelaAluguel.php`: HasUuids, constantes STATUS, fillable, casts (datas, decimais), `belongsTo(ContratoLocacao, 'contrato_id')`, `hasOne(RepasseProprietario)`
- [x] 3.2 Criar `app/Models/RepasseProprietario.php`: HasUuids, constantes STATUS, fillable, casts, `belongsTo(ContratoLocacao)`, `belongsTo(Imovel)`, `belongsTo(Cliente, 'proprietario_id')`, `belongsTo(ParcelaAluguel)`
- [x] 3.3 Criar `app/Models/MovimentacaoCaucao.php`: HasUuids, constantes TIPO, fillable, casts, `belongsTo(ContratoCaucao, 'caucao_contrato_id')`, `belongsTo(User, 'criado_por')`
- [x] 3.4 Criar `app/Models/ContratoRescisao.php`: HasUuids, constantes SOLICITADO_POR/DESTINO_IMOVEL/ACAO_PARCELAS, fillable, casts, `belongsTo(ContratoLocacao)`
- [x] 3.5 Criar `app/Models/ContratoRenovacao.php`: HasUuids, fillable, casts, `belongsTo(ContratoLocacao, 'contrato_original_id')`, `belongsTo(ContratoLocacao, 'novo_contrato_id')`
- [x] 3.6 Criar `app/Models/MovimentacaoFinanceira.php`: HasUuids, constantes TIPO/CATEGORIA, fillable, casts, `belongsTo` para contrato/parcela/repasse/caução (todos nullable)
- [x] 3.7 Atualizar `app/Models/ContratoLocacao.php`: adicionar `hasMany(ParcelaAluguel)`, `hasMany(RepasseProprietario)`, `hasOne(ContratoRescisao)`, `hasMany(ContratoRenovacao, 'contrato_original_id')`, `belongsTo(ContratoLocacao, 'contrato_anterior_id')`; remover fillable/casts de `data_rescisao`/`motivo_rescisao`/`parte_requerente`; adicionar `gerar_parcelas_automaticamente`/`quantidade_parcelas` ao fillable/casts; adicionar constante `STATUS_VENCIDO`
- [x] 3.8 Atualizar `app/Models/ContratoCaucao.php`: adicionar `saldo_atual` ao fillable/casts e `hasMany(MovimentacaoCaucao, 'caucao_contrato_id')`
- [x] 3.9 Extra: adicionar `dias_tolerancia_atraso` ao fillable/casts de `app/Models/ContratoMultas.php`

## 4. Services de cálculo

- [x] 4.1 Criar `app/Services/Contratos/CalcularMultaAtrasoService.php` com método `calcular(ParcelaAluguel $parcela, ?ContratoMultas $regras, CarbonInterface $dataPagamento): array` retornando `['multa','juros','dias_atraso','total']`, respeitando `dias_tolerancia_atraso`. Fórmula adaptada ao schema real: `valor_juros_dia` já é a taxa diária (%), não `percentual mensal/30`
- [x] 4.2 Criar `app/Services/Contratos/CalcularMultaRescisaoService.php` com método `calcular(ContratoLocacao $contrato, CarbonInterface $dataRescisao): array`. Fórmula adaptada ao schema real (não existem colunas `quantidade_alugueis_multa_rescisao`/`valor_fixo_multa_rescisao`): reaproveitada a fórmula já usada na estimativa client-side de `ModalRescindir.vue` — `base = alugueis_restantes ? valor_aluguel × meses_restantes : valor_aluguel`; `multa = base × percentual_multa_rescisao/100`
- [x] 4.3 Testes unitários dos dois services em `tests/Unit/Services/Contratos/` cobrindo atraso com/sem tolerância, pagamento em dia, e rescisão com/sem multa configurada e após a data fim

## 5. Services — Parcelas e Pagamento

- [x] 5.0 Migration/wiring extra (não previsto originalmente): `contrato_encargos` não possuía `valor_estimado`/`cobrar_junto_aluguel` (só rastreava responsável) — adicionadas as duas colunas, atualizado `ContratoEncargo`, `ContratoLocacaoService::sincronizarEncargos()` e validação em `StoreContratoRequest`/`UpdateContratoRequest`; necessário para `parcela-aluguel` somar encargos cobrados junto ao aluguel
- [x] 5.1 Criar `app/Services/Contratos/GerarParcelasContratoService.php` com método `gerar(ContratoLocacao $contrato): Collection` — gera uma parcela por mês entre `data_inicio`/`data_fim` (ou `quantidade_parcelas`), somando encargos com `cobrar_junto_aluguel = true`, evitando duplicidade por `mes_referencia`/`ano_referencia`
- [x] 5.2 Criar `app/Services/Contratos/MovimentacaoFinanceiraService.php` com método `registrar(string $tipo, string $categoria, float $valor, array $referencias = [])`
- [x] 5.3 Criar `app/Services/Contratos/PagamentoAluguelService.php` com método `registrar(ParcelaAluguel $parcela, array $dados, ?string $usuarioId = null): ParcelaAluguel` — usa `lockForUpdate()`, rejeita se já `pago`, calcula multa/juros via `CalcularMultaAtrasoService`, atualiza status (`pago`/`pago_parcial`), chama `MovimentacaoFinanceiraService` (entrada, categoria `aluguel`) e `RepasseProprietarioService::gerarPendente()`, tudo em uma `DB::transaction`
- [x] 5.4 Atualizar `ContratoStatusService::ativar()` para chamar `GerarParcelasContratoService::gerar()` quando `gerar_parcelas_automaticamente = true`
- [x] 5.5 Criar método de cancelamento de parcelas futuras (`ParcelaAluguel::cancelarFuturas()`) — já criado junto ao model na tarefa 3.1

## 6. Services — Repasse

- [x] 6.1 Criar `app/Services/Contratos/RepasseProprietarioService.php` com métodos `gerarPendente(ParcelaAluguel $parcela): RepasseProprietario` (calcula bruto/taxa/líquido a partir de `valor_aluguel`, sem encargos), `marcarComoPago(RepasseProprietario $repasse, array $dados)` (cria saída financeira), `cancelar(RepasseProprietario $repasse, string $motivo)` (exige motivo, registra no histórico do contrato)

## 7. Services — Caução (rework)

- [x] 7.1 Criar `app/Services/Contratos/MovimentacaoCaucaoService.php` com método `registrar(ContratoCaucao $caucao, string $tipo, float $valor, array $dados, ?string $usuarioId = null): MovimentacaoCaucao` — cria a movimentação, recalcula `saldo_atual` e `status_caucao` da caução (derivado do tipo da última movimentação + saldo resultante), e cria movimentação financeira quando `tipo` for `recebimento` (entrada) ou `devolucao` (saída)
- [x] 7.2 Atualizar `ContratoStatusService::encerrar()` para delegar a movimentação de caução ao `MovimentacaoCaucaoService` em vez de escrever `valor_devolvido`/`status_caucao` diretamente. `::rescindir()` foi removido de `ContratoStatusService` e substituído por `RescisaoContratoService` (ver 8.2) — adicionado `garantirTransicaoValida()` público para reuso da validação de transição de status

## 8. Services — Rescisão (rework)

- [x] 8.1 Criar `app/Services/Contratos/RescisaoContratoService.php` com método `rescindir(ContratoLocacao $contrato, array $dados, ?string $usuarioId = null): ContratoRescisao` — dentro de uma `DB::transaction`: calcula multa via `CalcularMultaRescisaoService`, soma parcelas vencidas em aberto (`debitos_em_aberto`), aplica abatimento/retenção/devolução de caução via `MovimentacaoCaucaoService`, atualiza imóvel conforme `destino_imovel`, cancela ou mantém parcelas futuras via `ParcelaAluguel::cancelarFuturas`, cria o registro em `contrato_rescisoes`, muda contrato para `rescindido`, e registra histórico
- [x] 8.2 `ContratoStatusService::rescindir()` foi removido e substituído por `RescisaoContratoService::rescindir()`; `ContratoLocacaoController::rescindir()` atualizado para usar o novo service + `StoreRescisaoContratoRequest`
- [x] 8.3 Criado `app/Http/Requests/Contratos/StoreRescisaoContratoRequest.php` com validação de `motivo`, `solicitado_por`, `destino_imovel`, `acao_parcelas_futuras` e campos de caução/desconto

## 9. Services — Renovação

- [x] 9.1 Criar `app/Services/Contratos/RenovacaoContratoService.php` com método `renovar(ContratoLocacao $contratoOriginal, array $dados, User $usuario): ContratoLocacao` — dentro de uma `DB::transaction`: cria novo `ContratoLocacao` com `contrato_anterior_id`, copia encargos/multas/caução conforme flags, marca original como `encerrado`, registra `ContratoRenovacao`, registra histórico `renovado_para` no original e `criacao_por_renovacao` no novo, gera parcelas se solicitado. Imóvel permanece `alugado` (não passa por `ContratoStatusService::ativar`, já está ocupado pelo contrato original)
- [x] 9.2 Criar `app/Http/Requests/Contratos/StoreRenovacaoContratoRequest.php` com validação de novas datas, novo valor de aluguel (opcional) e flags de manutenção; controller `RenovacaoContratoController::store()` criado

## 10. Scheduler

- [x] 10.1 Criar comando `app/Console/Commands/AtualizarContratosVencidosCommand.php` (`contratos:atualizar-vencidos`): marca parcelas `pendente` com `data_vencimento < hoje` como `vencido`; marca contratos `ativo` com `data_fim < hoje` como `vencido`
- [x] 10.2 Registrar o comando no scheduler (`routes/console.php`) para rodar diariamente
- [x] 10.3 Teste `tests/Feature/Contratos/AtualizarContratosVencidosCommandTest.php`: comando marca corretamente parcelas e contratos vencidos e ignora os que não se enquadram (criado também `ContratoTestHelpers` trait reutilizável para os próximos testes de contrato)

## 11. Permissões e Policies

- [x] 11.1 Adicionar permissões `contratos.registrar-pagamento`, `contratos.gerenciar-caucao`, `contratos.renovar`, `repasses.visualizar`, `repasses.marcar-como-pago` em `ContratoPermissionsSeeder` (admin recebe tudo via `Permission::all()` na `PerfisEPermissoesSeeder`) e adicionadas ao role `financeiro`
- [x] 11.2 Adicionar métodos correspondentes em `ContratoLocacaoPolicy` (`registrarPagamento`, `gerenciarCaucao`, `renovar`) e criar `RepasseProprietarioPolicy` (`viewAny`, `marcarComoPago`)
- [x] 11.3 Registrar `RepasseProprietario::class => RepasseProprietarioPolicy::class` no `AppServiceProvider`

## 12. Controllers e Rotas

- [x] 12.1 Criar `app/Http/Controllers/Contratos/PagamentoAluguelController.php` com `store(ParcelaAluguel $parcela, RegistrarPagamentoAluguelRequest $request)`
- [x] 12.2 Criar `app/Http/Controllers/Contratos/MovimentacaoCaucaoController.php` com `store(ContratoLocacao $contrato, StoreMovimentacaoCaucaoRequest $request)`
- [x] 12.3 Criar `app/Http/Controllers/Contratos/RepasseProprietarioController.php` com `marcarComoPago(RepasseProprietario $repasse, Request $request)` e `cancelar(RepasseProprietario $repasse, Request $request)`
- [x] 12.4 Criar `app/Http/Controllers/Contratos/RenovacaoContratoController.php` com `store(ContratoLocacao $contrato, StoreRenovacaoContratoRequest $request)` — feito junto com a tarefa 9.2
- [x] 12.5 `ContratoLocacaoController::rescindir()` atualizado para usar `RescisaoContratoService` e `StoreRescisaoContratoRequest` — feito junto com a tarefa 8.2
- [x] 12.6 Rotas adicionadas em `routes/web.php`: `POST contratos/{contrato}/parcelas/{parcela}/pagamento`, `POST contratos/{contrato}/caucao/movimentacoes`, `POST repasses-proprietarios/{repasse}/marcar-como-pago`, `POST repasses-proprietarios/{repasse}/cancelar`, `POST contratos/{contrato}/renovar` — confirmadas via `artisan route:list`

## 13. Frontend — Types

- [x] 13.1 Criar `resources/js/types/parcela.ts` com `ParcelaAluguel`, `StatusParcelaAluguel`
- [x] 13.2 Criar `resources/js/types/repasse.ts` com `RepasseProprietario`, `StatusRepasseProprietario`
- [x] 13.3 Criar `resources/js/types/caucao.ts` com `MovimentacaoCaucao`, `TipoMovimentacaoCaucao`; atualizado `contrato.ts` removendo `data_rescisao`/`motivo_rescisao`/`parte_requerente`, adicionando `saldo_atual`/`ContratoRescisao`/`ContratoRenovacao`, `vencido` no status, `contrato_anterior_id`, `gerar_parcelas_automaticamente`, `dias_tolerancia_atraso`, `valor_estimado`/`cobrar_junto_aluguel` em encargos

## 14. Frontend — Parcelas e Pagamento

- [x] 14.1 Criar `resources/js/Components/Contratos/TabelaParcelas.vue`: lista parcelas com referência, vencimento, valores, status e ação "Registrar pagamento" (condicionada a `contratos.registrar-pagamento`)
- [x] 14.2 Criar `resources/js/Components/Contratos/ModalRegistrarPagamento.vue`: campos data de pagamento, forma de pagamento, valor pago; multa/juros calculados no backend (não há preview client-side, exibido aviso informativo)
- [x] 14.3 Adicionar a `TabelaParcelas` em `Show.vue` do contrato

## 15. Frontend — Repasses

- [x] 15.1 Criar `resources/js/Components/Contratos/TabelaRepasses.vue`: lista repasses com referência, valor bruto/taxa/líquido, status, ações "Marcar como pago" e "Cancelar" (condicionadas a `repasses.marcar-como-pago`)
- [x] 15.2 Removido o card estático `CardRepasse.vue` (estimativa), substituído por `TabelaRepasses.vue` (dados reais) em `Show.vue`; configuração de taxa/dados bancários permanece no card "Resumo Financeiro" existente

## 16. Frontend — Caução (rework)

- [x] 16.1 Atualizar `resources/js/Components/Contratos/CardCaucao.vue` para exibir `saldo_atual` e a lista de movimentações
- [x] 16.2 Criar `resources/js/Components/Contratos/ModalMovimentacaoCaucao.vue`: formulário com tipo de movimentação, valor, data, forma, descrição

## 17. Frontend — Rescisão e Renovação

- [x] 17.1 Atualizar `resources/js/Components/Contratos/ModalRescindir.vue` para incluir `solicitado_por`, `destino_imovel`, `acao_parcelas_futuras`, campos de caução (abater/reter/devolver) e exibir a multa estimada antes de confirmar
- [x] 17.2 Criar `resources/js/Components/Contratos/ModalRenovarContrato.vue`: novo período, novo valor de aluguel opcional, flags de manter encargos/multas/caução, gerar novas parcelas
- [x] 17.3 Adicionar botão "Renovar" em `Show.vue` (ação no cabeçalho) e `Index.vue` (ação por linha, leva para `Show.vue`), visível para contratos `ativo`/`vencido` com permissão `contratos.renovar`; `vue-tsc` sem erros novos nos arquivos de Contratos

## 18. Testes

- [x] 18.1 Teste `AtivarContratoParcelasTest`: ativar contrato com `gerar_parcelas_automaticamente = true` cria uma parcela por mês, sem duplicar. **Bug real encontrado e corrigido durante o teste**: nenhum (apenas confirma comportamento correto)
- [x] 18.2 Teste `PagamentoAluguelTest`: registrar pagamento em dia não gera multa/juros; pagamento em atraso calcula multa e juros corretamente
- [x] 18.3 Teste `PagamentoAluguelTest`: pagamento parcial muda status para `pago_parcial`; tentativa de repagar parcela já paga é rejeitada
- [x] 18.4 Teste `PagamentoAluguelTest`: pagamento de parcela cria movimentação financeira de entrada e repasse pendente com valores corretos (sem incluir encargos no bruto). **Bug real encontrado e corrigido**: `PagamentoAluguelController::store()` não declarava o parâmetro `$contrato` da rota aninhada `contratos/{contrato}/parcelas/{parcela}/pagamento`, quebrando o model binding implícito do Laravel (`$parcela` chegava como string, não como model) — corrigido adicionando `ContratoLocacao $contrato` à assinatura
- [x] Nota de ambiente: os testes de rota (POST/PUT/PATCH/DELETE) só passam com `APP_ENV=testing` explícito na invocação (`docker compose exec -e APP_ENV=testing app php artisan test`), pois `docker-compose.yml` fixa `APP_ENV=local` no container e isso tem precedência sobre o `force="true"` do `phpunit.xml` para verbos HTTP de escrita (CSRF/origin check só é ignorado em `runningUnitTests()`, que depende do ambiente ser `testing`). Isso é uma condição pré-existente do ambiente, não introduzida por esta mudança — vale registrar em `CLAUDE.md`/README se for incomodar outras sessões.
- [x] 18.5 Teste `RepasseProprietarioTest`: marcar repasse como pago cria saída financeira; cancelar repasse sem motivo é rejeitado. **Bug real encontrado e corrigido**: CHECK constraint `contrato_historicos_tipo_evento_check` (criada em `contratos-de-locacao`) não incluía os novos tipos de evento (`repasse_cancelado`, `renovado_para`, `criacao_por_renovacao`) — nova migration `2026_07_04_100014` adiciona os valores
- [x] 18.6 Teste `MovimentacaoCaucaoTest`: recebimento, retenção parcial e devolução recalculam `saldo_atual`/`status_caucao` corretamente e criam histórico auditável
- [x] 18.7 Teste `RescisaoContratoTest`: rescisão calcula multa proporcional, soma débitos vencidos, permite abater com caução, cancela parcelas futuras quando solicitado, muda status do imóvel conforme destino, e exige motivo
- [x] 18.8 Teste `RenovacaoContratoTest`: renovação cria novo contrato vinculado ao original, encerra o original, preserva parcelas/pagamentos antigos, copia encargos quando solicitado e gera novas parcelas quando solicitado
- [x] 18.9 Teste `AtualizarContratosVencidosCommandTest` (feito na seção 10): comando marca parcelas e contratos vencidos corretamente
- [x] 18.10 Teste `PermissoesContratoFinanceiroTest`: usuário sem as novas permissões não consegue registrar pagamento, movimentar caução, marcar repasse como pago ou renovar contrato (todas as ações retornam 403)

## Context

O módulo `contratos-de-locacao` (já implementado) cobre: cadastro do contrato via wizard, transições de status (`rascunho → aguardando_assinatura/ativo`, `ativo → encerrado/rescindido`, `→ cancelado`), encargos (`contrato_encargos`), configuração de multas (`contrato_multas`, só percentuais/flags, sem cálculo), caução como snapshot único (`contrato_caucoes` com `valor_devolvido`/`status_caucao` escritos diretamente no encerramento/rescisão), documentos e histórico imutável (`contrato_historicos`).

Não existe nenhuma tabela ou serviço financeiro: sem `parcelas_aluguel`, sem registro de pagamento, sem `repasses_proprietarios`, sem `movimentacoes_caucao`, sem `movimentacoes_financeiras`. `proprietario_id`/`inquilino_id` apontam para `clientes` (diferenciados por `tipo`) e `corretor_id` aponta para `users` — este design segue essas convenções já estabelecidas em vez das tabelas separadas sugeridas pelo PRD genérico.

O banco é PostgreSQL; colunas `enum()` do Laravel viram `varchar` + `CHECK constraint` no Postgres (não um tipo enum nativo), o que é relevante para as decisões de adicionar novos valores de status.

## Goals / Non-Goals

**Goals:**
- Gerar parcelas mensais ao ativar um contrato e permitir cancelamento das futuras.
- Registrar pagamento de parcela com cálculo de multa por atraso e juros proporcionais.
- Gerar repasse ao proprietário automaticamente a partir do pagamento, com marcação de pago/cancelado.
- Substituir o snapshot de caução por um ledger de movimentações com saldo auditável.
- Calcular multa de rescisão e permitir abater/reter/devolver caução na rescisão.
- Permitir renovar um contrato criando nova vigência vinculada à anterior.
- Detectar contratos e parcelas vencidos (status `vencido`) para alerta no dashboard (fora deste escopo construir o próprio dashboard).

**Non-Goals:**
- Módulo Financeiro completo do PRD (módulo 24): lançamento manual de receita diversa/despesa administrativa, relatórios financeiros. Este design apenas cria `movimentacoes_financeiras` e insere registros automáticos a partir de pagamento/repasse/caução.
- Notificações por email de vencimento/cobrança (módulo 26) — fica para proposta futura; o scheduler aqui só atualiza status, não dispara notificação.
- Correção monetária automática / reajuste automático por índice (já não-objetivo no PRD).
- Dashboard e relatórios (módulos 12 e 27).
- Assinatura digital do contrato (o status `aguardando_assinatura` já existe e não muda).

## Decisions

**1. Pagamento embutido na própria parcela, sem tabela `pagamentos_aluguel` separada.**
`parcelas_aluguel` armazena `valor_pago`, `data_pagamento`, `forma_pagamento`, `valor_multa_atraso`, `valor_juros_atraso`, `valor_desconto` diretamente na linha (1 parcela = 1 registro de pagamento consolidado), igual ao schema sugerido no PRD (29.11).
*Alternativa considerada*: tabela `pagamentos_aluguel` para permitir múltiplos pagamentos parciais históricos por parcela — rejeitada para o MVP porque adiciona um join em toda leitura de parcela e o PRD só exige que `pago_parcial` acumule `valor_pago`, não um histórico detalhado de cada parcela de pagamento.

**2. Repasse gerado na mesma transação do pagamento, 1:1 com a parcela.**
`PagamentoAluguelService::registrar()` chama `RepasseProprietarioService::gerarPendente()` dentro da mesma `DB::transaction`. `repasses_proprietarios.parcela_aluguel_id` é único.
*Alternativa*: gerar repasse de forma assíncrona (job em fila) — rejeitada por complexidade desnecessária no MVP; não há volume que justifique.

**3. Caução vira ledger (`movimentacoes_caucao`) com saldo derivado em `contrato_caucoes`.**
`contrato_caucoes` ganha coluna `saldo_atual` (decimal), recalculada a cada movimentação dentro de `MovimentacaoCaucaoService`. `status_caucao` também passa a ser derivado (nunca mais escrito diretamente por `ContratoStatusService`).
*Alternativa*: manter o snapshot atual e só adicionar campos — rejeitada porque não representa retenção parcial seguida de devolução posterior, nem atende ao requisito de auditoria (PRD 9.2/21.8: "toda movimentação de caução deve ter histórico").

**4. Nova tabela `contrato_rescisoes` (1:1), substituindo os campos soltos de rescisão em `contratos_locacao`.**
Campos atuais (`data_rescisao`, `motivo_rescisao`, `parte_requerente`) migram para `contrato_rescisoes`, que ganha os campos de cálculo: `valor_multa_rescisao`, `valor_desconto`, `valor_final_multa`, `debitos_em_aberto`, `valor_caucao_retida`, `valor_caucao_abatida`, `valor_caucao_devolvida`, `destino_imovel`, `acao_parcelas_futuras`.
*Alternativa*: continuar empilhando colunas em `contratos_locacao` — rejeitada por inconsistência com o padrão já usado para caução/multas/encargos (tabelas satélite 1:1), e por deixar a tabela principal poluída com campos que só fazem sentido pós-rescisão.
*Migração*: como não há dado de produção, os 3 campos legados são descontinuados (coluna `down()` disponível para rollback) sem necessidade de backfill complexo — apenas copiar linhas com `status = 'rescindido'` existentes (se houver) para a nova tabela antes de remover as colunas.

**5. Renovação cria novo `ContratoLocacao` (reaproveitando `contrato_anterior_id` já existente) + tabela de auditoria `contrato_renovacoes`.**
O contrato original é marcado como `encerrado` (sem novo valor de enum) e ganha um evento de histórico `"renovado_para: {novo_contrato_id}"`. `contrato_renovacoes` guarda apenas o diff (valores antigos/novos, flags de manter encargos/multas/caução) para consulta rápida sem precisar comparar as duas linhas de contrato.
*Alternativa*: adicionar status `renovado` ao enum — rejeitada para evitar alteração de `CHECK constraint` no Postgres quando o histórico + `contrato_anterior_id` já resolvem a rastreabilidade (requisito do PRD 23.4).

**6. Novo valor de status `vencido` via `CHECK constraint` alterado por migration com `DB::statement`.**
Necessário tanto em `contratos_locacao.status` quanto em `parcelas_aluguel.status`. Como o Postgres não usa tipo enum nativo aqui (Laravel gera `varchar` + `CHECK`), a migration roda `ALTER TABLE ... DROP CONSTRAINT ...` seguido de `ADD CONSTRAINT ... CHECK (status IN (...))` incluindo `vencido`.

**7. `movimentacoes_financeiras` mínima, só com inserts automáticos.**
Colunas cobrem os casos deste change (`tipo`, `categoria` ∈ `{aluguel, caucao, multa, juros, repasse_proprietario, devolucao_caucao}`, `valor`, referências opcionais a contrato/parcela/repasse/caução). Nenhuma tela de cadastro manual é criada — é puramente o "livro-razão" alimentado pelos services deste change.

**8. Scheduler roda um único comando diário (`contratos:atualizar-vencidos`).**
Marca parcelas `pendente` com `data_vencimento < hoje` como `vencido`, e contratos `ativo` com `data_fim < hoje` como `vencido`. Não dispara notificação (non-goal).

## Risks / Trade-offs

- [Cálculos monetários com ponto flutuante] → usar `decimal:2`/`decimal:4` casts consistentemente e `bcmath`/arredondamento explícito nos services de multa/juros; cobrir com testes unitários os exemplos do PRD (multa 2%, juros 1% proporcional).
- [Pagamento duplicado por duplo clique/retry] → `DB::transaction` com `lockForUpdate()` na parcela; se `status` já for `pago`, o service rejeita com erro de validação em vez de reprocessar.
- [Alterar `CHECK constraint` em produção pode falhar se já existir linha fora do novo conjunto de valores] → migration primeiro valida (`SELECT DISTINCT status`) e só então recria a constraint; ambiente atual é MVP sem dados de produção, risco baixo.
- [Remoção das colunas de rescisão de `contratos_locacao` pode quebrar código existente que as referencia] → grep completo por `motivo_rescisao`/`data_rescisao`/`parte_requerente` antes de remover, atualizar `ContratoStatusService::rescindir` e o frontend (`Show.vue`) na mesma tarefa.
- [Escopo crescer para o módulo Financeiro completo] → `movimentacoes_financeiras` explicitamente limitada a inserts automáticos; qualquer tela de lançamento manual fica para proposta futura (não-goal).

## Migration Plan

1. Criar migrations novas: `parcelas_aluguel`, `repasses_proprietarios`, `movimentacoes_caucao`, `contrato_rescisoes`, `contrato_renovacoes`, `movimentacoes_financeiras`.
2. Migration de alteração: adicionar `saldo_atual` em `contrato_caucoes`; adicionar `gerar_parcelas_automaticamente`/`quantidade_parcelas` em `contratos_locacao` (capturados na ativação).
3. Migration de dados: copiar contratos com `status = 'rescindido'` (se existirem) de `contratos_locacao` para `contrato_rescisoes`.
4. Migration de alteração: recriar `CHECK constraint` de `contratos_locacao.status` e `parcelas_aluguel.status` incluindo `vencido`; remover colunas `motivo_rescisao`, `data_rescisao`, `parte_requerente` de `contratos_locacao`.
5. Atualizar `ContratoStatusService::rescindir`, controllers, `Show.vue` para os novos campos/tabelas.
6. Registrar `contratos:atualizar-vencidos` no `routes/console.php`/`Kernel` do scheduler.

Rollback: cada migration tem `down()` simétrico; a migration de dados (passo 3) deve rodar antes da remoção de colunas (passo 4) para permitir reverter sem perda, revertendo a ordem em caso de rollback.

## Open Questions

- Multas e juros recebidos no pagamento: são repassados integralmente ao proprietário junto com o aluguel, ficam com a imobiliária, ou é configurável por contrato? Assumido para o design: repassados integralmente ao proprietário (mesma base de cálculo do aluguel) — **precisa confirmação do usuário antes da fase de tasks**.
- Encargos cobrados junto ao aluguel (`cobrar_junto_aluguel = true`) entram no valor da parcela mas **não** devem compor o valor bruto usado no cálculo de repasse (regra do PRD 19.5/20.5). Assumido: parcela soma encargos ao total cobrado do inquilino, mas `repasses_proprietarios.valor_bruto` considera só `valor_aluguel` — **precisa confirmação**.
- Contrato original após renovação: fica com status `encerrado` (decisão 5) ou o produto realmente precisa de um status `renovado` visualmente distinto na listagem? Assumido `encerrado` + histórico, sujeito a validação do usuário.

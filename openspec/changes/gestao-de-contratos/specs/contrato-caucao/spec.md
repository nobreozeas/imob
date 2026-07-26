## ADDED Requirements

### Requirement: Status e saldo da caução derivados das movimentações
O sistema SHALL calcular `status_caucao` e `saldo_atual` a partir do histórico de `movimentacoes_caucao`, e não SHALL permitir que encerramento ou rescisão escrevam esses campos diretamente sem uma movimentação correspondente.

#### Scenario: Encerramento exige movimentação de devolução
- **WHEN** um contrato com caução recebida é encerrado
- **THEN** o sistema exige que uma movimentação de devolução, retenção ou abatimento seja registrada antes de considerar a caução resolvida

#### Scenario: Status recalculado após cada movimentação
- **WHEN** uma movimentação de retenção integral é registrada para uma caução com saldo positivo
- **THEN** `status_caucao` é recalculado para `retida_integralmente` e `saldo_atual` passa a zero

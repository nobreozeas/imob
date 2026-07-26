## ADDED Requirements

### Requirement: Registro de recebimento de caução
Quando o contrato possui caução configurada, o sistema SHALL permitir registrar o recebimento, criando uma movimentação do tipo `recebimento`, atualizando o `status_caucao` para `recebida` e criando uma movimentação financeira de entrada na categoria `caucao`.

#### Scenario: Recebimento de caução em contrato ativo
- **WHEN** o usuário registra o recebimento da caução de um contrato ativo com `possui_caucao = true`
- **THEN** uma movimentação `recebimento` é criada, `status_caucao` muda para `recebida`, e uma movimentação financeira de entrada na categoria `caucao` é criada

### Requirement: Saldo da caução é derivado das movimentações
O sistema SHALL manter `saldo_atual` da caução como o resultado acumulado das movimentações (`recebimento` soma, `devolucao`/`abatimento`/`retencao_parcial`/`retencao_integral` subtraem, `ajuste` aplica o delta informado).

#### Scenario: Saldo após recebimento
- **WHEN** uma caução de R$ 3.000,00 é recebida integralmente
- **THEN** `saldo_atual` da caução passa a ser R$ 3.000,00

#### Scenario: Saldo após retenção parcial
- **WHEN** uma caução com saldo de R$ 3.000,00 recebe uma movimentação de `retencao_parcial` de R$ 1.000,00
- **THEN** `saldo_atual` passa a ser R$ 2.000,00 e `status_caucao` muda para `retida_parcialmente`

### Requirement: Devolução da caução
O sistema SHALL permitir registrar devolução total ou parcial do saldo da caução, criando uma movimentação `devolucao` e uma movimentação financeira de saída na categoria `devolucao_caucao`.

#### Scenario: Devolução total
- **WHEN** o usuário registra devolução do saldo integral de uma caução recebida
- **THEN** `saldo_atual` passa a zero, `status_caucao` muda para `devolvida`, e uma saída financeira é criada

### Requirement: Abatimento de débitos com a caução
O sistema SHALL permitir abater um valor da caução contra um débito informado (ex.: parcela vencida), reduzindo o saldo e registrando a movimentação `abatimento` com a referência ao débito abatido.

#### Scenario: Abatimento de parcela vencida na rescisão
- **WHEN** o usuário abate R$ 500,00 do saldo da caução para quitar uma parcela vencida durante a rescisão
- **THEN** uma movimentação `abatimento` de R$ 500,00 é registrada, o saldo da caução é reduzido em R$ 500,00, e a parcela é marcada como paga

### Requirement: Toda movimentação de caução tem histórico auditável
Toda movimentação SHALL registrar data, valor, tipo, descrição e usuário responsável.

#### Scenario: Consulta ao histórico de movimentações
- **WHEN** o usuário acessa a aba Caução do contrato
- **THEN** todas as movimentações são listadas com data, tipo, valor e usuário que a registrou

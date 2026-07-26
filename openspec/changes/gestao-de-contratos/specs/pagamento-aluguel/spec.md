## ADDED Requirements

### Requirement: Registro de pagamento de parcela
O sistema SHALL permitir registrar o pagamento de uma parcela `pendente` ou `vencido`, informando data de pagamento, forma de pagamento e valor pago, e SHALL recalcular multa e juros quando o pagamento ocorrer após a `data_vencimento`.

#### Scenario: Pagamento integral dentro do prazo
- **WHEN** o usuário registra pagamento de uma parcela `pendente` com valor igual ao `valor_total`, antes da data de vencimento
- **THEN** o status da parcela muda para `pago` e `valor_pago` é igual ao `valor_total`

#### Scenario: Pagamento com atraso calcula multa e juros
- **WHEN** uma parcela de R$ 1.500,00 com multa de atraso de 2% e juros de 1% ao mês é paga 10 dias após o vencimento (sem dias de tolerância configurados)
- **THEN** o sistema calcula multa de R$ 30,00 e juros proporcional de R$ 5,00, resultando em `valor_total` de R$ 1.535,00

#### Scenario: Pagamento respeita dias de tolerância
- **WHEN** uma parcela é paga com atraso menor ou igual aos `dias_tolerancia_atraso` configurados no contrato
- **THEN** nenhuma multa ou juros é aplicado

#### Scenario: Pagamento parcial
- **WHEN** o usuário registra um pagamento com valor menor que o `valor_total` da parcela
- **THEN** o status da parcela muda para `pago_parcial` e `valor_pago` reflete o valor informado

#### Scenario: Impedir reprocessar parcela já paga
- **WHEN** o usuário tenta registrar um novo pagamento em uma parcela cujo status já é `pago`
- **THEN** o sistema rejeita a operação com erro de validação

### Requirement: Pagamento gera movimentação financeira de entrada
Ao confirmar um pagamento de parcela, o sistema SHALL criar uma movimentação financeira de entrada vinculada ao contrato e à parcela, na categoria `aluguel`.

#### Scenario: Entrada financeira criada após pagamento
- **WHEN** um pagamento de parcela é confirmado
- **THEN** uma `movimentacao_financeira` do tipo `entrada`, categoria `aluguel`, com o valor total pago é criada e vinculada à parcela

### Requirement: Pagamento gera repasse pendente ao proprietário
Ao confirmar um pagamento de parcela, o sistema SHALL gerar (ou atualizar, se ainda não existir) um repasse pendente ao proprietário do imóvel do contrato.

#### Scenario: Repasse pendente criado após pagamento
- **WHEN** um pagamento de parcela é confirmado
- **THEN** um registro em `repasses_proprietarios` com status `pendente` é criado, vinculado à parcela paga

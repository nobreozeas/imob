## ADDED Requirements

### Requirement: Cálculo do repasse
O sistema SHALL calcular o repasse a partir do `valor_aluguel` da parcela paga (excluindo encargos cobrados junto ao aluguel), aplicando a taxa de administração do contrato para obter valor bruto, valor da taxa e valor líquido.

#### Scenario: Cálculo com taxa percentual
- **WHEN** uma parcela com valor de aluguel de R$ 1.500,00 é paga em um contrato com taxa de administração de 10%
- **THEN** o repasse é gerado com valor bruto de R$ 1.500,00, taxa de R$ 150,00 e valor líquido de R$ 1.350,00

#### Scenario: Encargos não entram no valor bruto do repasse
- **WHEN** uma parcela paga inclui R$ 200,00 de encargos cobrados junto ao aluguel além do valor do aluguel
- **THEN** o valor bruto do repasse considera apenas o valor do aluguel, sem os R$ 200,00 de encargos

### Requirement: Marcar repasse como pago
O sistema SHALL permitir marcar um repasse `pendente` como `pago`, registrando data e forma de pagamento, e SHALL criar uma movimentação financeira de saída correspondente.

#### Scenario: Repasse pago gera saída financeira
- **WHEN** o usuário marca um repasse pendente como pago
- **THEN** o status do repasse muda para `pago` e uma `movimentacao_financeira` do tipo `saida`, categoria `repasse_proprietario`, é criada com o valor líquido

### Requirement: Cancelar repasse exige justificativa
O sistema SHALL exigir uma justificativa ao cancelar um repasse pendente.

#### Scenario: Cancelamento sem motivo é rejeitado
- **WHEN** o usuário tenta cancelar um repasse pendente sem informar motivo
- **THEN** o sistema rejeita a operação com erro de validação

#### Scenario: Cancelamento com motivo é aceito
- **WHEN** o usuário cancela um repasse pendente informando o motivo
- **THEN** o status do repasse muda para `cancelado` e o motivo é registrado no histórico do contrato

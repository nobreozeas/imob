## ADDED Requirements

### Requirement: Criação de nova vigência a partir de um contrato existente
O sistema SHALL permitir renovar um contrato `ativo` ou `vencido`, criando um novo `ContratoLocacao` vinculado ao original via `contrato_anterior_id`, com nova data de início/fim e, opcionalmente, novo valor de aluguel e nova taxa de administração.

#### Scenario: Renovar contrato vencido
- **WHEN** o usuário renova um contrato com status `vencido`, informando nova data de início e nova data de fim
- **THEN** um novo contrato é criado com `contrato_anterior_id` apontando para o contrato original

#### Scenario: Renovação com reajuste de valor
- **WHEN** o usuário informa um novo valor de aluguel na renovação
- **THEN** o novo contrato é criado com o novo valor, e as parcelas geradas para ele usam o valor reajustado

### Requirement: Contrato original é encerrado ao renovar
Ao confirmar a renovação, o sistema SHALL marcar o contrato original como `encerrado` e registrar um evento de histórico indicando a renovação, preservando todo o histórico financeiro do contrato original.

#### Scenario: Histórico do contrato original preservado
- **WHEN** um contrato é renovado
- **THEN** o contrato original permanece com status `encerrado`, mantém suas parcelas e pagamentos anteriores, e um evento de histórico `renovado_para` referencia o novo contrato

### Requirement: Opções de manutenção de encargos, multas e caução na renovação
O sistema SHALL permitir manter ou alterar os encargos e as regras de multa do contrato original, e permitir manter, complementar ou devolver a caução anterior.

#### Scenario: Manter encargos na renovação
- **WHEN** o usuário escolhe "manter encargos anteriores" ao renovar
- **THEN** os encargos do contrato original são copiados para o novo contrato

#### Scenario: Gerar novas parcelas na renovação
- **WHEN** o usuário escolhe "gerar novas parcelas" ao concluir a renovação
- **THEN** parcelas mensais são geradas para o novo contrato conforme a nova data de início/fim e dia de vencimento

## ADDED Requirements

### Requirement: Identificação de parcelas inadimplentes
O sistema SHALL considerar inadimplente toda parcela de aluguel com `data_vencimento` anterior à data atual e status `pendente` ou `pago_parcial`.

#### Scenario: Parcela pendente vencida é inadimplente
- **WHEN** uma parcela tem status `pendente` e `data_vencimento` de 5 dias atrás
- **THEN** a parcela aparece na listagem de inadimplência

#### Scenario: Parcela paga não é inadimplente
- **WHEN** uma parcela vencida tem status `pago`
- **THEN** a parcela não aparece na listagem de inadimplência

### Requirement: Indicadores de inadimplência
O sistema SHALL exibir a quantidade de parcelas vencidas, o valor total vencido, a quantidade de contratos inadimplentes, a quantidade de inquilinos (clientes) inadimplentes e o maior atraso em dias.

#### Scenario: Cálculo do maior atraso
- **WHEN** as parcelas inadimplentes têm atrasos de 3, 10 e 25 dias
- **THEN** o indicador "maior atraso em dias" exibe 25

### Requirement: Ações a partir da listagem de inadimplência
O sistema SHALL permitir, a partir da listagem de inadimplência, registrar pagamento da parcela, acessar o contrato relacionado e enviar notificação por email ao inquilino.

#### Scenario: Registrar pagamento a partir da inadimplência
- **WHEN** o usuário aciona "Registrar pagamento" em uma parcela inadimplente
- **THEN** o sistema abre o fluxo de registro de pagamento para aquela parcela

## MODIFIED Requirements

### Requirement: Lançamentos automáticos de pagamento de aluguel, repasse e caução
O sistema SHALL criar automaticamente um lançamento financeiro com status `pago` sempre que: um pagamento de aluguel for registrado (entrada, categoria `aluguel`, valor bruto recebido do inquilino), um repasse for marcado como pago (saída, categoria `repasse_proprietario`), uma caução for recebida (entrada, categoria `caucao`) ou devolvida (saída, categoria `devolucao_caucao`). Adicionalmente, ao registrar um pagamento de aluguel, o sistema SHALL criar também um lançamento de entrada na categoria `taxa_administracao`, no valor da taxa de administração calculada para aquele pagamento — essa é a receita efetivamente reconhecida pela imobiliária, independente de quando o repasse ao proprietário for pago.

#### Scenario: Pagamento de aluguel gera lançamento de entrada bruto
- **WHEN** um pagamento de parcela de aluguel é registrado
- **THEN** um lançamento financeiro de entrada, categoria `aluguel`, origem `pagamento_aluguel`, status `pago`, é criado vinculado ao contrato e à parcela, com o valor total recebido

#### Scenario: Pagamento de aluguel gera lançamento de taxa de administração
- **WHEN** um pagamento de aluguel de R$ 1.000,00 é registrado em um contrato com taxa de administração de 10%
- **THEN** um segundo lançamento financeiro de entrada, categoria `taxa_administracao`, origem `pagamento_aluguel`, status `pago`, valor R$ 100,00, é criado vinculado ao mesmo contrato e parcela

#### Scenario: Repasse pago gera lançamento de saída
- **WHEN** um repasse ao proprietário é marcado como pago
- **THEN** um lançamento financeiro de saída, categoria `repasse_proprietario`, origem `repasse_proprietario`, status `pago`, é criado vinculado ao repasse

#### Scenario: Recebimento de caução gera lançamento de entrada
- **WHEN** o recebimento de uma caução é registrado
- **THEN** um lançamento financeiro de entrada, categoria `caucao`, origem `caucao`, status `pago`, é criado vinculado à caução

#### Scenario: Devolução de caução gera lançamento de saída
- **WHEN** a devolução de uma caução é registrada
- **THEN** um lançamento financeiro de saída, categoria `devolucao_caucao`, origem `movimentacao_caucao`, status `pago`, é criado vinculado à movimentação de caução

## ADDED Requirements

### Requirement: Geração automática de parcelas na ativação do contrato
Ao ativar um contrato de locação com `gerar_parcelas_automaticamente = true`, o sistema SHALL gerar uma parcela de aluguel para cada mês entre `data_inicio` e `data_fim` (ou pela `quantidade_parcelas` informada quando `data_fim` for nula), usando `dia_vencimento` do contrato para calcular a data de vencimento de cada parcela.

#### Scenario: Ativação gera parcelas mensais
- **WHEN** um contrato com `data_inicio = 2026-01-10`, `data_fim = 2026-12-10`, `dia_vencimento = 5` e `gerar_parcelas_automaticamente = true` é ativado
- **THEN** o sistema cria 12 parcelas, uma por mês de referência, cada uma com vencimento no dia 5

#### Scenario: Contrato em rascunho não gera parcelas
- **WHEN** um contrato em status `rascunho` é salvo ou editado
- **THEN** nenhuma parcela é criada

#### Scenario: Geração automática desativada
- **WHEN** um contrato com `gerar_parcelas_automaticamente = false` é ativado
- **THEN** nenhuma parcela é criada automaticamente e o usuário pode gerá-las manualmente depois

### Requirement: Parcela armazena os valores compostos do mês de referência
Cada parcela SHALL armazenar `mes_referencia`, `ano_referencia`, `data_vencimento`, `valor_aluguel`, `valor_encargos` (soma dos encargos do contrato marcados como `cobrar_junto_aluguel`), `valor_multa_atraso`, `valor_juros_atraso`, `valor_desconto`, `valor_total` e `valor_pago`.

#### Scenario: Parcela inclui encargos cobrados junto ao aluguel
- **WHEN** o contrato possui um encargo do tipo `condominio` com `cobrar_junto_aluguel = true` e valor estimado de R$ 200,00
- **THEN** a parcela gerada tem `valor_encargos = 200.00` somado ao `valor_total` previsto

### Requirement: Não duplicação de parcelas
O sistema SHALL impedir a criação de mais de uma parcela para o mesmo contrato com o mesmo `mes_referencia`/`ano_referencia`.

#### Scenario: Tentativa de gerar parcela duplicada
- **WHEN** já existe uma parcela do contrato para referência `03/2026` e o sistema tenta gerar novamente essa mesma referência
- **THEN** a geração é ignorada para aquele mês, sem criar registro duplicado

### Requirement: Cancelamento de parcelas futuras
O sistema SHALL permitir cancelar parcelas com status `pendente` cuja `data_vencimento` seja futura, mantendo o histórico da parcela (sem exclusão física).

#### Scenario: Cancelar parcelas futuras após rescisão
- **WHEN** um contrato é rescindido com a ação `cancelar_parcelas_futuras`
- **THEN** todas as parcelas `pendente` com vencimento após a data da rescisão mudam para status `cancelado`

#### Scenario: Não cancelar parcela já paga
- **WHEN** o sistema tenta cancelar parcelas futuras de um contrato que já possui uma parcela com status `pago`
- **THEN** a parcela paga permanece inalterada

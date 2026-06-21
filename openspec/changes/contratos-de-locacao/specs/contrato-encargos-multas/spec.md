## ADDED Requirements

### Requirement: Configuração de encargos por tipo
O sistema SHALL permitir definir, para cada contrato, a responsabilidade (proprietario/inquilino/nao_se_aplica) de cada encargo predefinido: IPTU, condomínio, água, energia elétrica, gás, internet e outros. Os encargos SHALL ser armazenados na tabela `contrato_encargos` como registros separados por tipo.

#### Scenario: Salvar encargos na criação
- **WHEN** usuário submete o wizard com encargos configurados na etapa 4
- **THEN** sistema cria um registro em `contrato_encargos` para cada tipo com o responsável definido

#### Scenario: Exibição na tela de detalhes
- **WHEN** usuário visualiza os detalhes de um contrato
- **THEN** sistema exibe tabela de encargos com tipo, responsável e observação para cada item

#### Scenario: Encargo sem responsável definido
- **WHEN** usuário deixa um tipo de encargo sem definir responsável
- **THEN** sistema salva com `responsavel = nao_se_aplica` como padrão

### Requirement: Configuração de multa por atraso de pagamento
O sistema SHALL permitir configurar multa por atraso com percentual sobre o valor do aluguel e juros diários. A configuração SHALL ser armazenada na tabela `contrato_multas` (relação 1:1 com o contrato, sempre criada mesmo que `possui_multa_atraso = false`).

#### Scenario: Multa por atraso habilitada
- **WHEN** usuário ativa `possui_multa_atraso` na etapa 6 do wizard
- **THEN** campos `percentual_multa_atraso` e `valor_juros_dia` são exibidos e tornados obrigatórios

#### Scenario: Multa por atraso desabilitada
- **WHEN** usuário deixa `possui_multa_atraso = false`
- **THEN** sistema salva `contrato_multas` com `possui_multa_atraso = false` e campos numéricos como `null`

#### Scenario: Exibição na tela de detalhes
- **WHEN** contrato possui `possui_multa_atraso = true`
- **THEN** sistema exibe seção de multas com percentual e juros diários formatados

### Requirement: Configuração de multa por rescisão antecipada
O sistema SHALL permitir configurar multa por quebra de contrato, com percentual calculado sobre alugueis restantes ou valor fixo. A configuração reside no mesmo registro `contrato_multas` da multa por atraso.

#### Scenario: Multa por rescisão habilitada
- **WHEN** usuário ativa `possui_multa_rescisao` na etapa 6
- **THEN** campos `percentual_multa_rescisao` e `base_calculo_rescisao` (alugueis_restantes/valor_fixo) são exibidos

#### Scenario: Base de cálculo por alugueis restantes
- **WHEN** usuário seleciona `base_calculo_rescisao = alugueis_restantes`
- **THEN** sistema armazena o percentual a ser aplicado sobre o total dos alugueis do período restante

#### Scenario: Exibição de multa calculada na rescisão
- **WHEN** usuário aciona "Rescindir" via modal com contrato ativo
- **THEN** sistema exibe o valor estimado da multa calculado com base nos dados do contrato (informativo, não persistido)

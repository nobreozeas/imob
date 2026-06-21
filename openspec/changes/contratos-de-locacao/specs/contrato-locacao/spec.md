## ADDED Requirements

### Requirement: Listagem de contratos com filtros
O sistema SHALL exibir todos os contratos de locação em tabela paginada, com filtros por status, imóvel (busca por código/título), proprietário, inquilino e período (data de início entre X e Y).

#### Scenario: Listagem sem filtros
- **WHEN** usuário acessa `/contratos`
- **THEN** sistema exibe tabela com número do contrato, imóvel, proprietário, inquilino, vigência, valor de aluguel, status (badge colorido) e ações

#### Scenario: Filtro por status
- **WHEN** usuário seleciona um status no filtro
- **THEN** tabela exibe apenas contratos com aquele status, com 400ms de debounce para parâmetros textuais

#### Scenario: Paginação
- **WHEN** existem mais de 15 contratos após aplicar filtros
- **THEN** sistema exibe controles de paginação e mantém os filtros ativos ao navegar

### Requirement: Criação de contrato via wizard de 9 etapas
O sistema SHALL permitir criar um contrato através de um wizard com 9 etapas sequenciais. O campo `numero` SHALL ser gerado automaticamente no formato `LOC-{YYYYMM}-{seq}` se não fornecido.

#### Scenario: Navegação entre etapas
- **WHEN** usuário completa os campos obrigatórios de uma etapa e clica em "Próximo"
- **THEN** sistema avança para a próxima etapa e exibe a barra de progresso atualizada

#### Scenario: Auto-preenchimento de proprietário
- **WHEN** usuário seleciona um imóvel na etapa 1
- **THEN** sistema preenche automaticamente o campo `proprietario_id` com o proprietário do imóvel (readonly)

#### Scenario: Salvar como rascunho
- **WHEN** usuário clica em "Salvar como Rascunho" na etapa 9
- **THEN** sistema salva o contrato com `status = rascunho` e redireciona para a tela de detalhes

#### Scenario: Ativar contrato diretamente
- **WHEN** usuário clica em "Ativar Contrato" na etapa 9
- **THEN** sistema salva o contrato, seta `status = ativo`, atualiza o imóvel para `alugado` e registra evento no histórico

#### Scenario: Erro de validação redireciona para etapa correta
- **WHEN** backend retorna erros de validação
- **THEN** wizard redireciona automaticamente para a etapa que contém o campo com erro, usando o mapa `CAMPO_PARA_ETAPA`

### Requirement: Etapas do wizard de criação
O sistema SHALL organizar o formulário de contrato em 9 etapas: (1) Imóvel e Partes, (2) Dados da Locação, (3) Valores, (4) Encargos, (5) Caução, (6) Multas, (7) Repasse, (8) Documentos, (9) Revisão e Ativação.

#### Scenario: Etapa 1 — Imóvel e Partes
- **WHEN** usuário está na etapa 1
- **THEN** sistema exibe seleção de imóvel (apenas disponíveis), proprietário (readonly, preenchido pelo imóvel), seleção de inquilino e seleção de corretor (opcional)

#### Scenario: Etapa 2 — Dados da Locação
- **WHEN** usuário está na etapa 2
- **THEN** sistema exibe campos: data_inicio, data_fim, dia_vencimento (1-31), duracao_meses (calculado automaticamente), tipo_contrato (residencial/comercial/temporada), objetivo_contrato (aluguel puro / temporada)

#### Scenario: Etapa 3 — Valores
- **WHEN** usuário está na etapa 3
- **THEN** sistema exibe campos: valor_aluguel, indice_reajuste (IGPM/IPCA/INPC/fixo), periodicidade_reajuste (meses), data_primeiro_reajuste

#### Scenario: Etapa 4 — Encargos
- **WHEN** usuário está na etapa 4
- **THEN** sistema exibe lista de encargos (IPTU, condomínio, água, energia, gás, internet, outros) com responsável (proprietario/inquilino/nao_se_aplica) para cada

#### Scenario: Etapa 5 — Caução
- **WHEN** usuário está na etapa 5
- **THEN** sistema exibe toggle `possui_caucao` e, se ativo, campos: tipo_caucao (dinheiro/imovel/fiador/seguro_fianca/deposito_bancario), valor_caucao, data_recebimento_caucao

#### Scenario: Etapa 6 — Multas
- **WHEN** usuário está na etapa 6
- **THEN** sistema exibe: `possui_multa_atraso` com percentual_multa_atraso e valor_juros_dia; `possui_multa_rescisao` com percentual_multa_rescisao e percentual_multa_rescisao_base (alugueis restantes / valor fixo)

#### Scenario: Etapa 7 — Repasse
- **WHEN** usuário está na etapa 7
- **THEN** sistema exibe: tipo_taxa_administracao (percentual/valor_fixo), valor_taxa_administracao, dia_repasse, forma_repasse (pix/transferencia/dinheiro), dados bancários do proprietário (banco, agencia, conta, tipo_conta, pix_key) — pré-preenchidos do cadastro do cliente mas editáveis

#### Scenario: Etapa 8 — Documentos
- **WHEN** usuário está na etapa 8
- **THEN** sistema exibe upload de múltiplos arquivos com campo `tipo` por arquivo (contrato_assinado, laudo_vistoria, comprovante_caucao, outros); máximo 10 arquivos; formatos: PDF, JPEG, PNG, DOCX; limite 20MB por arquivo

#### Scenario: Etapa 9 — Revisão e Ativação
- **WHEN** usuário está na etapa 9
- **THEN** sistema exibe resumo de todas as etapas em cards readonly e dois botões: "Salvar como Rascunho" e "Ativar Contrato"

### Requirement: Visualização de detalhes do contrato
O sistema SHALL exibir todos os dados do contrato em tela de detalhes organizada em seções: dados gerais, partes envolvidas, valores, encargos, caução, multas, repasse, documentos, histórico e ações disponíveis.

#### Scenario: Badge de status
- **WHEN** usuário visualiza a tela de detalhes
- **THEN** sistema exibe o número do contrato no header com badge de status colorido (rascunho=cinza, aguardando_assinatura=amarelo, ativo=verde, encerrado=azul, rescindido=laranja, cancelado=vermelho)

#### Scenario: Ações por status
- **WHEN** contrato está no status `rascunho`
- **THEN** sistema exibe botões: "Editar", "Enviar para Assinatura", "Cancelar"

#### Scenario: Ações no status ativo
- **WHEN** contrato está no status `ativo`
- **THEN** sistema exibe botões: "Editar (limitado)", "Encerrar", "Rescindir" e "Adicionar Documento"

#### Scenario: Ações em status final
- **WHEN** contrato está em status `encerrado`, `rescindido` ou `cancelado`
- **THEN** sistema exibe apenas visualização, sem botões de alteração de status

### Requirement: Edição de contrato com restrições por status
O sistema SHALL permitir edição completa (todos os campos) apenas para contratos em `rascunho`. Para contratos `ativo` e `aguardando_assinatura`, apenas campos não contratuais SHALL ser editáveis (observacoes, documentos).

#### Scenario: Edição de rascunho
- **WHEN** usuário abre edição de contrato em `rascunho`
- **THEN** sistema exibe o wizard completo de 9 etapas com todos os campos habilitados

#### Scenario: Tentativa de edição de contrato ativo
- **WHEN** usuário tenta editar campos restritos de um contrato `ativo`
- **THEN** sistema retorna erro 403 e exibe mensagem informando que o contrato está ativo

### Requirement: Fluxo de status do contrato
O sistema SHALL controlar as transições de status de acordo com o fluxo definido: `rascunho → aguardando_assinatura → ativo → encerrado|rescindido` e `rascunho|aguardando_assinatura → cancelado`.

#### Scenario: Enviar para assinatura
- **WHEN** usuário aciona "Enviar para Assinatura" em contrato `rascunho`
- **THEN** sistema seta `status = aguardando_assinatura`, registra no histórico e exibe badge atualizado

#### Scenario: Ativar contrato
- **WHEN** usuário aciona "Ativar" em contrato `aguardando_assinatura`
- **THEN** sistema seta `status = ativo`, atualiza imóvel para `alugado`, registra evento no histórico com user e timestamp

#### Scenario: Cancelar contrato
- **WHEN** usuário cancela contrato em `rascunho` ou `aguardando_assinatura`
- **THEN** sistema seta `status = cancelado`, registra no histórico; imóvel permanece no status anterior

#### Scenario: Encerrar contrato
- **WHEN** usuário aciona "Encerrar" via modal em contrato `ativo`
- **THEN** sistema seta `status = encerrado`, atualiza imóvel para `disponivel`, registra no histórico com `data_encerramento` e `motivo_encerramento`

#### Scenario: Rescindir contrato
- **WHEN** usuário aciona "Rescindir" via modal em contrato `ativo`
- **THEN** sistema seta `status = rescindido`, atualiza imóvel para `disponivel`, registra no histórico com `data_rescisao`, `motivo_rescisao` e `parte_requerente`

#### Scenario: Transição inválida bloqueada
- **WHEN** usuário tenta acionar uma transição de status não permitida pelo fluxo
- **THEN** sistema retorna erro de validação com mensagem clara

### Requirement: Permissões granulares de contratos
O sistema SHALL controlar acesso às ações de contrato via Spatie Permissions com as permissões: `contratos.viewAny`, `contratos.view`, `contratos.create`, `contratos.update`, `contratos.ativar`, `contratos.cancelar`, `contratos.encerrar`, `contratos.rescindir`, `contratos.documentos`.

#### Scenario: Acesso sem permissão
- **WHEN** usuário sem a permissão `contratos.viewAny` acessa `/contratos`
- **THEN** sistema retorna 403

#### Scenario: Role admin possui todas as permissões
- **WHEN** seeder de permissões é executado
- **THEN** role `admin` recebe todas as permissões de contratos

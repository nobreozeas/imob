## ADDED Requirements

### Requirement: Upload e gestão de documentos do contrato
O sistema SHALL permitir anexar documentos ao contrato (contrato assinado, laudo de vistoria, comprovante de caução, outros). Documentos SHALL ser armazenados em `storage/app/public/contratos/{uuid}/` e registrados na tabela `contrato_documentos`.

#### Scenario: Upload de documentos no wizard (etapa 8)
- **WHEN** usuário faz upload de arquivos na etapa 8 do wizard
- **THEN** sistema aceita até 10 arquivos por upload; formatos: PDF, JPEG, PNG, DOCX; limite de 20MB por arquivo; cada arquivo pode ter um tipo informado

#### Scenario: Upload de documentos adicionais na tela de detalhes
- **WHEN** usuário clica em "Adicionar Documento" em contrato ativo ou encerrado
- **THEN** sistema exibe modal com formulário de upload e campo de tipo, e persiste o arquivo após confirmação

#### Scenario: Listagem de documentos
- **WHEN** usuário visualiza a seção de documentos no contrato
- **THEN** sistema exibe lista com nome do arquivo, tipo, data de upload e link para download

#### Scenario: Remoção de documento
- **WHEN** usuário clica em remover documento e confirma
- **THEN** sistema exclui o arquivo do storage e remove o registro de `contrato_documentos`

#### Scenario: Tentativa de upload de arquivo inválido
- **WHEN** usuário tenta fazer upload de arquivo com formato não permitido ou acima de 20MB
- **THEN** sistema rejeita o arquivo com mensagem de validação clara

### Requirement: Registro de histórico de eventos do contrato
O sistema SHALL registrar automaticamente na tabela `contrato_historicos` cada evento relevante do ciclo de vida do contrato. O histórico é imutável (sem softDelete, sem update) e deve incluir: tipo_evento, descricao, dados_anteriores (JSON), dados_novos (JSON), usuario_id e created_at.

#### Scenario: Registro na criação
- **WHEN** contrato é criado (como rascunho ou ativo)
- **THEN** sistema cria registro em `contrato_historicos` com `tipo_evento = criacao` e dados do contrato

#### Scenario: Registro em mudança de status
- **WHEN** status do contrato é alterado (ativação, cancelamento, encerramento, rescisão)
- **THEN** sistema cria registro com `tipo_evento` correspondente, `dados_anteriores = {status: anterior}` e `dados_novos = {status: novo}`

#### Scenario: Registro em alteração de campos sensíveis
- **WHEN** campos sensíveis são alterados (valor_aluguel, data_inicio, data_fim, inquilino_id, imovel_id, taxa_administracao, caução, multas)
- **THEN** sistema cria registro com `tipo_evento = alteracao`, incluindo os valores anteriores e novos no JSON de dados

#### Scenario: Exibição da linha do tempo
- **WHEN** usuário visualiza a seção de histórico na tela de detalhes
- **THEN** sistema exibe lista cronológica reversa com ícone do tipo de evento, descrição, nome do usuário responsável e data/hora formatada

#### Scenario: Imutabilidade do histórico
- **WHEN** código tenta atualizar ou excluir um registro de histórico
- **THEN** operação é bloqueada (sem método `update` no ContratoHistoricoService; tabela sem softDeletes)

## Why

O ImobGestor possui cadastro de imóveis e clientes, mas ainda não possui o vínculo formal entre eles: o contrato de locação. Sem esse módulo, a imobiliária não consegue registrar acordos firmados, controlar prazos, gerar cobranças, calcular repasses nem acompanhar o ciclo de vida de uma locação — tornando o sistema incompleto para operação real.

## What Changes

- Módulo completo de **Contratos de Locação** com wizard de 9 etapas (imóvel/partes, dados da locação, valores, encargos, caução, multas, repasses, documentos e revisão/ativação)
- Listagem de contratos com filtros por status, imóvel, proprietário, inquilino e período
- Tela de detalhes exibindo todas as seções do contrato e ações disponíveis por status
- Edição de contratos com restrições por status (rascunho livre, ativo limitado)
- Fluxo de ativação: `rascunho → ativo`, com atualização automática do imóvel para **Alugado**
- Fluxo de encerramento e rescisão com definição do novo status do imóvel
- Registro de histórico em toda ação relevante (ativação, encerramento, rescisão, edição de campos sensíveis)
- Upload de documentos anexos ao contrato (contrato assinado, laudos, comprovantes etc.)
- Gestão de caução com controle de recebimento e devolução
- Configuração de regras de multa por atraso e por quebra de contrato
- Definição de encargos e responsabilidades (IPTU, condomínio, água, energia, etc.)
- Configuração de repasse ao proprietário com taxa de administração
- Permissões granulares via Spatie Permissions
- **BREAKING** (integração com `imovel-crud`): O status do imóvel passa a ser controlado automaticamente pelos eventos do contrato, não apenas manualmente

## Capabilities

### New Capabilities

- `contrato-locacao`: Ciclo de vida completo do contrato — CRUD com wizard de 9 etapas, listagem com filtros, detalhes, ativação, cancelamento, encerramento, rescisão e permissões
- `contrato-encargos-multas`: Configuração de encargos e responsabilidades por encargo, regras de multa por atraso e por quebra de contrato
- `contrato-caucao`: Gestão de caução vinculada ao contrato — recebimento, acompanhamento e devolução/retenção no encerramento
- `contrato-documentos-historico`: Anexo e gestão de documentos do contrato, registro de histórico de eventos e alterações

### Modified Capabilities

- `imovel-crud`: O imóvel agora muda de status automaticamente via contrato — ativação seta `alugado`, encerramento/rescisão libera o imóvel; a action `alterar-status` manual deve bloquear `disponivel` quando `temContratoAtivo()` retornar `true`

## Impact

- **Banco de dados**: novas tabelas `contratos_locacao`, `contrato_encargos`, `contrato_caucoes`, `contrato_multas`, `contrato_documentos`, `contrato_historicos`
- **Backend**: Models, Migrations, Services (`ContratoLocacaoService`, `ContratoStatusService`, `ContratoDocumentoService`, `ContratoHistoricoService`), Controller, Policies, Form Requests em `App\Services\Contratos\` e `App\Http\Controllers\Contratos\`
- **Modelo Imovel**: `temContratoAtivo()` passa a consultar a tabela `contratos_locacao` em vez de retornar `false`
- **Frontend**: Pages `Admin/Contratos/{Index,Create,Edit,Show}.vue`, wizard com 9 steps, componentes de badge, cards e modais de encerramento/rescisão
- **Rotas**: grupo protegido por `auth`+`must.change.password` com resource + ações adicionais (ativar, cancelar, encerrar, rescindir, documentos)
- **Permissões**: seeder com permissões granulares (viewAny, view, create, update, ativar, cancelar, encerrar, rescindir, documentos) atribuídas ao role `admin`
- **Storage**: documentos em `storage/app/public/contratos/{uuid}/`

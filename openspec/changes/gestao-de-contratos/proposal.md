## Why

O módulo `contratos-de-locacao` já cobre o cadastro do contrato (imóvel, partes, valores, encargos, multas configuradas, caução como snapshot, documentos e histórico) e as transições de status (ativação, encerramento, rescisão, cancelamento). Porém o contrato ainda não gera nenhuma cobrança: não existem parcelas mensais, registro de pagamentos, cálculo de multa/juros por atraso, repasse ao proprietário nem movimentações de caução com histórico de saldo. Sem isso, a imobiliária não consegue operar financeiramente um contrato ativo — que é o objetivo central do PRD (`docs/PRD-Sistema-Gestao-Imobiliaria.md`, módulos 17–21) e da especificação funcional (`docs/Funcionalidade-03-Gestao-contratos.md`, seções 15–20).

## What Changes

- Geração automática de **parcelas de aluguel** mensais ao ativar um contrato (respeitando data de início/fim, dia de vencimento), com status `pendente`, `pago`, `pago_parcial`, `vencido`, `cancelado`.
- **Registro de pagamento** de parcela: cálculo de multa por atraso e juros proporcionais (conforme `contrato_multas`), aplicação de desconto, criação de movimentação financeira de entrada e atualização do status da parcela.
- **Repasse ao proprietário**: gerado automaticamente ao registrar pagamento de aluguel (valor bruto, taxa de administração, valor líquido), com ação de marcar como pago (gera saída financeira) ou cancelar (exige justificativa).
- **Movimentações de caução**: substitui o snapshot atual (`valor_devolvido`/`status_caucao` preenchidos direto no encerramento) por um ledger (`recebimento`, `devolucao`, `abatimento`, `retencao_parcial`, `retencao_integral`, `ajuste`) com saldo calculado e histórico de quem/quando/por quê.
- **Rescisão de contrato** reforçada: cálculo de multa por rescisão (proporcional ao tempo restante, conforme `contrato_multas`), verificação de parcelas vencidas em aberto, e uso do saldo de caução para abater débitos, reter ou devolver — via as novas movimentações de caução.
- **Renovação de contrato**: nova entidade que cria uma vigência vinculada ao contrato original (`contrato_anterior_id` já existe em `contratos_locacao`), permitindo reajuste de valor/taxa, manter ou alterar encargos e regras de multa, manter/complementar/devolver caução, e gerar novas parcelas.
- **Status `vencido`** para contratos ativos cuja `data_fim` já passou sem renovação/encerramento (para alerta no dashboard).
- Novas tabs em `Show.vue` do contrato: Parcelas, Repasses, Caução (com movimentações), substituindo os blocos estáticos atuais.
- **BREAKING** (`contrato-caucao`): o encerramento/rescisão deixa de escrever diretamente em `valor_devolvido`/`status_caucao`; passa a exigir uma movimentação de caução explícita, que recalcula esses campos.

## Capabilities

### New Capabilities

- `parcela-aluguel`: geração de parcelas mensais ao ativar contrato, consulta e cancelamento de parcelas futuras
- `pagamento-aluguel`: registro de pagamento de parcela com cálculo de multa/juros e criação de entrada financeira
- `repasse-proprietario`: geração automática de repasse por parcela paga, marcação como pago, cancelamento com justificativa
- `movimentacao-caucao`: ledger de movimentações de caução com saldo calculado
- `renovacao-contrato`: criação de nova vigência vinculada a um contrato existente

### Modified Capabilities

- `contrato-locacao`: ativação passa a gerar parcelas automaticamente quando configurado; adiciona status `vencido` (verificado por scheduler); rescisão passa a calcular multa e a exigir tratamento de débitos/caução via movimentação em vez de campos soltos
- `contrato-caucao`: substitui atualização direta de `valor_devolvido`/`status_caucao` por recálculo a partir das `movimentacoes_caucao`

## Impact

- **Banco de dados**: novas tabelas `parcelas_aluguel`, `pagamentos_aluguel` (ou campos de pagamento na própria parcela), `repasses_proprietarios`, `movimentacoes_caucao`, `renovacoes_contrato`; `movimentacoes_financeiras` para registrar entradas/saídas geradas por pagamento, repasse e caução (sem construir o módulo financeiro completo do PRD, que fica para proposta futura)
- **Backend**: `GerarParcelasContratoService`, `PagamentoAluguelService`, `RepasseProprietarioService`, `CalcularMultaAtrasoService`, `CalcularMultaRescisaoService`, `MovimentacaoCaucaoService`, `RenovacaoContratoService`; novos controllers em `App\Http\Controllers\Contratos\`; scheduler para marcar contratos/parcelas vencidos
- **Modelo `ContratoLocacao`**: novo status `vencido`; `ContratoStatusService::rescindir` passa a delegar cálculo de multa e tratamento de caução para os novos services
- **Modelo `ContratoCaucao`**: passa a ser alimentado por `movimentacoes_caucao` (saldo derivado) em vez de campos preenchidos manualmente no encerramento/rescisão
- **Frontend**: novas tabs em `Show.vue` (Parcelas, Repasses, Caução), modais de registrar pagamento, movimentar caução, renovar contrato
- **Rotas**: `contratos/{contrato}/parcelas/{parcela}/pagamento`, `contratos/{contrato}/caucao/movimentacoes`, `contratos/{contrato}/renovar`, `repasses/{repasse}/marcar-como-pago`
- **Permissões**: novas permissões granulares (`contratos.registrar-pagamento`, `contratos.gerenciar-caucao`, `contratos.renovar`, `repasses.marcar-como-pago`)

## 1. Banco de Dados — Migrations

- [x] 1.1 Criar migration `create_contratos_locacao_table` com UUID PK, FKs para imovel/proprietario/inquilino/corretor/criado_por, campos de dados da locação (numero, status, tipo_contrato, data_inicio, data_fim, dia_vencimento, duracao_meses), campos de valores (valor_aluguel, indice_reajuste, periodicidade_reajuste, data_primeiro_reajuste), campos de repasse (tipo_taxa_administracao, valor_taxa_administracao, dia_repasse, forma_repasse, dados bancários), softDeletes e timestamps
- [x] 1.2 Criar migration `create_contrato_encargos_table` com UUID PK, contrato_id FK, tipo_encargo enum, responsavel enum (proprietario/inquilino/nao_se_aplica), observacao nullable, timestamps
- [x] 1.3 Criar migration `create_contrato_caucoes_table` com UUID PK, contrato_id FK (unique), possui_caucao boolean, tipo_caucao enum nullable, valor_caucao decimal nullable, data_recebimento_caucao date nullable, status_caucao enum (recebida/devolvida/retida_parcialmente/retida_totalmente), valor_devolvido decimal nullable, data_devolucao_caucao date nullable, observacao_caucao text nullable, timestamps
- [x] 1.4 Criar migration `create_contrato_multas_table` com UUID PK, contrato_id FK (unique), possui_multa_atraso boolean, percentual_multa_atraso decimal nullable, valor_juros_dia decimal nullable, possui_multa_rescisao boolean, percentual_multa_rescisao decimal nullable, base_calculo_rescisao enum (alugueis_restantes/valor_fixo) nullable, timestamps
- [x] 1.5 Criar migration `create_contrato_documentos_table` com UUID PK, contrato_id FK (cascadeOnDelete), caminho, nome_original, tipo enum (contrato_assinado/laudo_vistoria/comprovante_caucao/outros), criado_por FK nullable, timestamps
- [x] 1.6 Criar migration `create_contrato_historicos_table` com UUID PK, contrato_id FK (cascadeOnDelete), tipo_evento enum (criacao/ativacao/cancelamento/encerramento/rescisao/alteracao/documento_adicionado), descricao text, dados_anteriores JSON nullable, dados_novos JSON nullable, usuario_id FK nullable, timestamps (sem softDeletes)

## 2. Models

- [x] 2.1 Criar `app/Models/ContratoLocacao.php` com HasUuids, SoftDeletes, constantes de STATUS/TIPO/INDICES/FORMAS_REPASSE, fillable completo, casts (datas como `date:Y-m-d`, decimais, booleanos), relacionamentos: imovel(), proprietario(), inquilino(), corretor(), criadoPor(), encargos(), caucao(), multas(), documentos(), historicos()
- [x] 2.2 Criar `app/Models/ContratoEncargo.php` com HasUuids, fillable, casts, belongsTo(ContratoLocacao)
- [x] 2.3 Criar `app/Models/ContratoCaucao.php` com HasUuids, fillable, casts (decimais, datas, booleano, enum status_caucao), belongsTo(ContratoLocacao)
- [x] 2.4 Criar `app/Models/ContratoMultas.php` com HasUuids, fillable, casts (decimais, booleanos), belongsTo(ContratoLocacao)
- [x] 2.5 Criar `app/Models/ContratoDocumento.php` com HasUuids, `$appends = ['url']`, getUrlAttribute() via Storage::url(), belongsTo(ContratoLocacao)
- [x] 2.6 Criar `app/Models/ContratoHistorico.php` com HasUuids (sem SoftDeletes), fillable, casts (JSON para dados_anteriores/dados_novos), belongsTo(ContratoLocacao), belongsTo(User, 'usuario_id')
- [x] 2.7 Atualizar `app/Models/Imovel.php`: método `temContratoAtivo()` passa a consultar `contratos_locacao` onde `imovel_id = $this->id AND status = 'ativo'`; adicionar `hasMany(ContratoLocacao)` relationship

## 3. Permissões e Policy

- [x] 3.1 Criar `database/seeders/ContratoPermissionsSeeder.php` com permissões: `contratos.viewAny`, `contratos.view`, `contratos.create`, `contratos.update`, `contratos.ativar`, `contratos.cancelar`, `contratos.encerrar`, `contratos.rescindir`, `contratos.documentos` — usando `App\Models\Permission` com `firstOrCreate` + UUID explícito; atribuir todas ao role `admin`
- [x] 3.2 Criar `app/Policies/ContratoLocacaoPolicy.php` com métodos viewAny, view, create, update, ativar, cancelar, encerrar, rescindir, documentos
- [x] 3.3 Registrar `ContratoLocacao::class => ContratoLocacaoPolicy::class` no array `$policies` do `AppServiceProvider`
- [x] 3.4 Adicionar `ContratoPermissionsSeeder` ao `DatabaseSeeder`

## 4. Services

- [x] 4.1 Criar `app/Services/Contratos/ContratoHistoricoService.php` com método `registrar(ContratoLocacao $contrato, string $tipoEvento, string $descricao, array $dadosAnteriores = [], array $dadosNovos = [])` — cria registro imutável em `contrato_historicos`
- [x] 4.2 Criar `app/Services/Contratos/ContratoDocumentoService.php` com métodos `salvarDocumentos(ContratoLocacao, array $arquivos, array $tipos)`, `removerDocumento(ContratoDocumento)` — gerencia storage em `contratos/{uuid}/`
- [x] 4.3 Criar `app/Services/Contratos/ContratoStatusService.php` com métodos `ativar()`, `enviarParaAssinatura()`, `cancelar()`, `encerrar()`, `rescindir()` — cada um em `DB::transaction`, atualiza status do contrato, status do imóvel e chama `ContratoHistoricoService::registrar()`; `ativar()` usa `lockForUpdate()` no imóvel
- [x] 4.4 Criar `app/Services/Contratos/ContratoLocacaoService.php` com métodos `criar(array $dados, User $criador)`, `atualizar(ContratoLocacao, array $dados)`, `gerarNumero()` — `criar()` usa `DB::transaction`, gera número se vazio, cria contrato + encargos (foreach) + caucao (updateOrCreate) + multas (updateOrCreate) + documentos (via ContratoDocumentoService) + histórico; `atualizar()` valida status permite edição completa; `gerarNumero()` no formato `LOC-{YYYYMM}-{seq}`

## 5. Form Requests

- [x] 5.1 Criar `app/Http/Requests/Contratos/StoreContratoRequest.php` com validação completa: campos obrigatórios (imovel_id, inquilino_id, data_inicio, dia_vencimento, valor_aluguel), arrays de encargos (encargos.*.tipo_encargo, encargos.*.responsavel), objeto caucao (caucao.possui_caucao + campos condicionais), objeto multas (multas.possui_multa_atraso + campos condicionais), array documentos_novos (file, mimes:pdf,jpg,jpeg,png,docx, max:20480, max array: 10)
- [x] 5.2 Criar `app/Http/Requests/Contratos/UpdateContratoRequest.php` — igual ao Store mas com `Rule::unique('contratos_locacao', 'numero')->ignore($contratoId)` e validação de status permitindo edição

## 6. Controller e Rotas

- [x] 6.1 Criar `app/Http/Controllers/Contratos/ContratoLocacaoController.php` com actions: `index()` (filtros + eager loads), `create()` (props: imoveis disponíveis com proprietário + inquilinos), `store()` (chama service), `show()` (eager loads completos), `edit()` (eager loads), `update()` (chama service), `ativar()`, `enviarParaAssinatura()`, `cancelar()`, `encerrar()` (valida dados do modal), `rescindir()` (valida dados do modal), `adicionarDocumento()`, `removerDocumento()`
- [x] 6.2 Adicionar rotas em `routes/web.php` dentro do middleware group existente: `Route::resource('contratos', ContratoLocacaoController::class)->except(['destroy'])->names('contratos')->parameters(['contratos' => 'contrato'])`; rotas adicionais: `POST /contratos/{contrato}/ativar`, `POST /contratos/{contrato}/enviar-assinatura`, `POST /contratos/{contrato}/cancelar`, `POST /contratos/{contrato}/encerrar`, `POST /contratos/{contrato}/rescindir`, `POST /contratos/{contrato}/documentos`, `DELETE /contratos/{contrato}/documentos/{documento}`

## 7. TypeScript Types e Composables

- [x] 7.1 Criar `resources/js/types/contrato.ts` com tipos: `StatusContrato`, `TipoContrato`, `IndiceReajuste`, `FormaRepasse`, `TipoEncargo`, `ResponsavelEncargo`, `TipoCaucao`, `StatusCaucao`, `BaseCalculoRescisao`, `TipoDocumentoContrato`, `TipoEventoHistorico`; interfaces: `ContratoEncargo`, `ContratoCaucao`, `ContratoMultas`, `ContratoDocumento`, `ContratoHistorico`, `ContratoLocacao`, `ContratoFiltros`, `ContratoPaginado`, `FormularioContratoData`; `FormularioContratoData` inclui `documentos_novos: File[]` e `tipos_documentos: string[]`
- [x] 7.2 Criar `resources/js/composables/useContratoStatus.ts` com `labelStatus()`, `corStatus()` (classes DaisyUI), `labelTipoContrato()`, `labelIndice()`, `labelResponsavel()`, `labelTipoCaucao()`, `labelStatusCaucao()`, `corStatusCaucao()`, `labelTipoEvento()`, `iconeEvento()`

## 8. Componentes Vue

- [x] 8.1 Criar `resources/js/Components/Contratos/BadgeStatus.vue` — badge colorido por status do contrato
- [x] 8.2 Criar `resources/js/Components/Contratos/CardEncargos.vue` — tabela de encargos com tipo e responsável
- [x] 8.3 Criar `resources/js/Components/Contratos/CardCaucao.vue` — seção de caução com badge de status, valor, datas
- [x] 8.4 Criar `resources/js/Components/Contratos/CardMultas.vue` — seção de multas com regras de atraso e rescisão
- [x] 8.5 Criar `resources/js/Components/Contratos/CardRepasse.vue` — informações de repasse ao proprietário com taxa e dados bancários
- [x] 8.6 Criar `resources/js/Components/Contratos/CardHistorico.vue` — timeline cronológica reversa de eventos com ícone, descrição, usuário e data
- [x] 8.7 Criar `resources/js/Components/Contratos/ModalEncerrar.vue` — modal DaisyUI com campos: data_encerramento (date), motivo_encerramento (textarea), e se possui_caucao: valor_devolvido, data_devolucao_caucao, observacao_caucao
- [x] 8.8 Criar `resources/js/Components/Contratos/ModalRescindir.vue` — modal DaisyUI com campos: data_rescisao (date), motivo_rescisao (textarea), parte_requerente (proprietario/inquilino/ambos), e campos de devolução de caução se aplicável; exibe multa estimada se possui_multa_rescisao

## 9. Wizard de Criação/Edição

- [x] 9.1 Criar `resources/js/Components/Contratos/WizardContrato.vue` — 9 steps DaisyUI, mapa `CAMPO_PARA_ETAPA` cobrindo todos os campos do formulário, watch(errors) para auto-redirect, recebe props `:form`, `:imoveis`, `:inquilinos`, `:corretores`
- [x] 9.2 Criar `resources/js/Components/Contratos/WizardStep1ImoveiPartes.vue` — select de imóvel (disponíveis), auto-preenchimento de proprietario_id readonly ao selecionar imóvel, select de inquilino, select de corretor (opcional)
- [x] 9.3 Criar `resources/js/Components/Contratos/WizardStep2DadosLocacao.vue` — data_inicio, data_fim, dia_vencimento (1-31), duracao_meses (calculado e readonly), tipo_contrato, objetivo_contrato
- [x] 9.4 Criar `resources/js/Components/Contratos/WizardStep3Valores.vue` — valor_aluguel, indice_reajuste (select), periodicidade_reajuste, data_primeiro_reajuste
- [x] 9.5 Criar `resources/js/Components/Contratos/WizardStep4Encargos.vue` — lista de encargos predefinidos com select de responsável para cada (proprietario/inquilino/nao_se_aplica)
- [x] 9.6 Criar `resources/js/Components/Contratos/WizardStep5Caucao.vue` — toggle possui_caucao, campos condicionais: tipo_caucao, valor_caucao, data_recebimento_caucao
- [x] 9.7 Criar `resources/js/Components/Contratos/WizardStep6Multas.vue` — toggle possui_multa_atraso com percentual_multa_atraso e valor_juros_dia; toggle possui_multa_rescisao com percentual e base_calculo_rescisao
- [x] 9.8 Criar `resources/js/Components/Contratos/WizardStep7Repasse.vue` — tipo_taxa_administracao, valor_taxa_administracao, dia_repasse, forma_repasse, campos bancários do proprietário
- [x] 9.9 Criar `resources/js/Components/Contratos/WizardStep8Documentos.vue` — upload de múltiplos arquivos com tipo por arquivo, preview de nome, limite visual de 10 arquivos
- [x] 9.10 Criar `resources/js/Components/Contratos/WizardStep9Revisao.vue` — cards readonly com resumo de todas as etapas; dois botões: "Salvar como Rascunho" e "Ativar Contrato" (emits 'submit' com campo `acao`)

## 10. Pages Vue

- [x] 10.1 Criar `resources/js/Pages/Admin/Contratos/Index.vue` — tabela com numero, imóvel (codigo+titulo), proprietário, inquilino, vigência, valor_aluguel formatado, BadgeStatus, ações (ver, editar se rascunho); filtros com 400ms debounce; paginação
- [x] 10.2 Criar `resources/js/Pages/Admin/Contratos/Create.vue` — `useForm<FormularioContratoData>` com defaults, usa `form.post(route('contratos.store'), { forceFormData: true })`; passa imoveis, inquilinos, corretores como props
- [x] 10.3 Criar `resources/js/Pages/Admin/Contratos/Edit.vue` — `useForm` inicializado dos dados do contrato e sub-entidades; `form.put(route('contratos.update', id), { forceFormData: true })`; wizard bloqueado se status não permite edição completa
- [x] 10.4 Criar `resources/js/Pages/Admin/Contratos/Show.vue` — layout 2 colunas: main (dados gerais, partes, CardEncargos, CardCaucao, CardMultas, CardRepasse, seção documentos com links, CardHistorico) + sidebar (resumo de valores, ações por status usando SweetAlert para confirmações simples, ModalEncerrar, ModalRescindir)

## 11. Testes e Finalização

- [x] 11.1 Executar `docker compose exec app php artisan migrate` para aplicar as 6 novas migrations
- [x] 11.2 Executar `docker compose exec app php artisan db:seed --class=ContratoPermissionsSeeder` para criar permissões
- [x] 11.3 Verificar rota `php artisan route:list | grep contrato` e testar criação de contrato pelo wizard
- [x] 11.4 Verificar fluxo completo: criar rascunho → ativar → verificar imóvel como `alugado` → encerrar → verificar imóvel como `disponivel`
- [x] 11.5 Verificar que `temContratoAtivo()` bloqueia alteração manual de status do imóvel para `disponivel` quando há contrato ativo

## 1. Banco de Dados — Migrations

- [x] 1.1 Criar migration `create_imoveis_table` com campos: `id` (UUID PK), `codigo` (varchar unique), `titulo`, `tipo` (enum), `finalidade` (enum), `status` (enum), `proprietario_id` (FK clientes), `corretor_id` (FK users nullable), `descricao` (text nullable), endereço inline (cep, logradouro, numero, complemento, bairro, cidade, estado, ponto_referencia), `criado_por` (FK users), softDeletes, timestamps
- [x] 1.2 Criar migration `create_imovel_caracteristicas_table` com campos: `id` (UUID PK), `imovel_id` (FK unique), `area_total` (decimal nullable), `area_construida` (decimal nullable), `quartos`, `suites`, `banheiros`, `vagas_garagem` (integer defaults 0), `mobiliado`, `aceita_pet`, `possui_piscina`, `possui_quintal`, `possui_varanda` (boolean defaults false), `outras_caracteristicas` (text nullable), timestamps
- [x] 1.3 Criar migration `create_imovel_dados_comerciais_table` com campos: `id` (UUID PK), `imovel_id` (FK unique), `valor_aluguel` (decimal nullable), `valor_venda` (decimal nullable), `valor_condominio` (decimal nullable), `valor_iptu` (decimal nullable), `condominio_incluso` (boolean default false), `responsavel_iptu`, `responsavel_agua`, `responsavel_energia`, `responsavel_condominio` (enum: proprietario/inquilino, nullable), `valor_caucao_sugerido` (decimal nullable), `observacoes_comerciais` (text nullable), timestamps
- [x] 1.4 Criar migration `create_imovel_fotos_table` com campos: `id` (UUID PK), `imovel_id` (FK), `caminho` (varchar), `nome_original` (varchar), `is_principal` (boolean default false), `ordem` (integer default 0), timestamps
- [x] 1.5 Criar migration `create_imovel_documentos_table` com campos: `id` (UUID PK), `imovel_id` (FK), `caminho` (varchar), `nome_original` (varchar), `tipo` (varchar nullable), timestamps
- [x] 1.6 Executar `docker compose exec app php artisan migrate`

## 2. Models

- [x] 2.1 Criar `app/Models/Imovel.php` com HasUuids, SoftDeletes, fillable completo, casts (status enum, tipo enum, finalidade enum, booleanos de características via relacionamento), constantes STATUS_*, TIPO_*, FINALIDADE_*, scopes `disponivel()` e `porProprietario()`, relationships `hasOne(ImovelCaracteristicas)`, `hasOne(ImovelDadosComerciais)`, `hasMany(ImovelFoto)`, `hasMany(ImovelDocumento)`, `belongsTo(Cliente, 'proprietario_id')`, `belongsTo(User, 'corretor_id')`, accessor `fotoPrincipal()`
- [x] 2.2 Criar `app/Models/ImovelCaracteristicas.php` com HasUuids, fillable, casts booleanos e decimais, `belongsTo(Imovel)`
- [x] 2.3 Criar `app/Models/ImovelDadosComerciais.php` com HasUuids, fillable, casts decimais, `belongsTo(Imovel)`
- [x] 2.4 Criar `app/Models/ImovelFoto.php` com HasUuids, fillable, accessor `url` (usando `Storage::url($this->caminho)`), `belongsTo(Imovel)`
- [x] 2.5 Criar `app/Models/ImovelDocumento.php` com HasUuids, fillable, accessor `url`, `belongsTo(Imovel)`

## 3. Permissões, Seeder e Policy

- [x] 3.1 Criar `database/seeders/ImovelPermissionsSeeder.php` que cria (via `firstOrCreate`) as permissões `imoveis.viewAny`, `imoveis.view`, `imoveis.create`, `imoveis.update`, `imoveis.alterar-status` usando `App\Models\Permission`, e as atribui ao role `admin`
- [x] 3.2 Registrar `ImovelPermissionsSeeder` no `DatabaseSeeder`
- [x] 3.3 Criar `app/Policies/ImovelPolicy.php` com métodos `viewAny`, `view`, `create`, `update`, `alterarStatus` mapeados para as permissões Spatie via `$user->can()`
- [x] 3.4 Registrar `ImovelPolicy` em `app/Providers/AppServiceProvider.php` dentro do `Gate::policy()`
- [x] 3.5 Executar `docker compose exec app php artisan db:seed --class=ImovelPermissionsSeeder`

## 4. Service

- [x] 4.1 Criar `app/Services/Imoveis/ImovelService.php` com método `criar(array $dados): Imovel` usando `DB::transaction`, gerando código automático via `gerarCodigo()` se não informado, criando o imóvel + características + dados comerciais + fotos + documentos
- [x] 4.2 Implementar método `atualizar(Imovel $imovel, array $dados): Imovel` no service, atualizando imóvel + `updateOrCreate` para características e dados comerciais + `sincronizarFotos()` + `sincronizarDocumentos()`
- [x] 4.3 Implementar método `alterarStatus(Imovel $imovel, string $status): void` com verificação de contrato ativo (retorna sempre `false` por ora — integração futura)
- [x] 4.4 Implementar método privado `gerarCodigo(): string` no formato `IMO-{YYYYMM}-{seq}` com seq auto-incrementado via `DB::selectOne`
- [x] 4.5 Implementar método privado `sincronizarFotos(Imovel $imovel, array $dados): void` que remove as fotos em `fotos_remover`, salva as `fotos_novas` no storage e atualiza `is_principal` conforme `foto_principal_id`
- [x] 4.6 Implementar método privado `sincronizarDocumentos(Imovel $imovel, array $dados): void` que remove os documentos em `documentos_remover` e salva os `documentos_novos` com tipo

## 5. Form Requests

- [x] 5.1 Criar `app/Http/Requests/Imoveis/StoreImovelRequest.php` com validação: `titulo` required, `codigo` nullable unique imoveis, `tipo` required enum, `finalidade` required enum, `status` required enum, `proprietario_id` required exists:clientes.id, `corretor_id` nullable exists:users.id, campos de endereço nullable, campos de características nullable numérico, campos de dados comerciais nullable numérico, `fotos_novas.*` nullable image max:5120, `documentos_novos.*` nullable file mimes:pdf,jpg,jpeg,png,docx max:20480
- [x] 5.2 Criar `app/Http/Requests/Imoveis/UpdateImovelRequest.php` com mesmas regras + `Rule::unique('imoveis', 'codigo')->ignore($imovel->id)` no campo código

## 6. Controller e Rotas

- [x] 6.1 Criar `app/Http/Controllers/Imoveis/ImovelController.php` com: `index()` (query com filtros + paginação, carregando `proprietario` para exibição), `create()` (passando lista de proprietários e usuários como props), `store()`, `show()` (carregando todos os relacionamentos), `edit()` (carregando imóvel com todos os relacionamentos + lista de proprietários e usuários), `update()`, `alterarStatus()`
- [x] 6.2 Adicionar rotas em `routes/web.php` dentro do grupo `auth`+`must.change.password`: `Route::resource('imoveis', ImovelController::class)->except(['destroy'])` e `Route::patch('imoveis/{imovel}/status', [ImovelController::class, 'alterarStatus'])->name('imoveis.alterar-status')`

## 7. Configuração de Storage

- [x] 7.1 Executar `docker compose exec app php artisan storage:link` para criar o symlink e permitir acesso público às fotos e documentos

## 8. Frontend: Tipos e Composables

- [x] 8.1 Criar `resources/js/types/imovel.ts` com: tipos `TipoImovel`, `FinalidadeImovel`, `StatusImovel`, `ResponsavelCusto`; interfaces `Imovel`, `ImovelCaracteristicas`, `ImovelDadosComerciais`, `ImovelFoto`, `ImovelDocumento`, `ImovelFiltros`, `ImovelPaginado`, `FormularioImovelData` (com campos agrupados por step incluindo `fotos_novas: File[]`, `fotos_remover: string[]`, `documentos_novos: File[]`, `documentos_remover: string[]`, `foto_principal_id: string`)
- [x] 8.2 Criar `resources/js/composables/useImovelStatus.ts` com funções `labelStatus(status)`, `corStatus(status)` (badge DaisyUI), `labelTipo(tipo)`, `labelFinalidade(finalidade)`

## 9. Frontend: Componentes Base

- [x] 9.1 Criar `resources/js/Components/Imoveis/BadgeStatus.vue` com badge DaisyUI colorido por status
- [x] 9.2 Criar `resources/js/Components/Imoveis/BadgeTipo.vue` com badge neutro para tipo do imóvel
- [x] 9.3 Criar `resources/js/Components/Imoveis/BadgeFinalidade.vue` com badge colorido por finalidade
- [x] 9.4 Criar `resources/js/Components/Imoveis/CardCaracteristicas.vue` para exibição na tela de detalhes (grid com ícones e valores)
- [x] 9.5 Criar `resources/js/Components/Imoveis/CardDadosComerciais.vue` para exibição de valores e responsabilidades na tela de detalhes
- [x] 9.6 Criar `resources/js/Components/Imoveis/GaleriaFotos.vue` para exibição de fotos na tela de detalhes (foto principal em destaque + grid de demais)

## 10. Frontend: Wizard

- [x] 10.1 Criar `resources/js/Components/Imoveis/WizardImovel.vue` com: progress bar DaisyUI steps 5 etapas, ref `etapaAtual`, mapa `CAMPO_PARA_ETAPA` (todos os campos mapeados para 1-5), watch em `errors` para redirect automático de etapa, emit `submit`, prop `:form` (sem v-model), prop `:proprietarios` e `:corretores` repassadas ao step 1
- [x] 10.2 Criar `resources/js/Components/Imoveis/WizardStep1DadosPrincipais.vue` com: campos título, código (input com placeholder de sugestão), tipo (select enum), finalidade (select enum), status (select enum), proprietário (select com busca nos proprietários passados via prop), corretor (select nullable), descrição (textarea); validação local de campos obrigatórios antes de emit('next')
- [x] 10.3 Criar `resources/js/Components/Imoveis/WizardStep2Endereco.vue` com campos CEP, logradouro, número, complemento, bairro, cidade, estado, ponto de referência; todos opcionais com v-model direto
- [x] 10.4 Criar `resources/js/Components/Imoveis/WizardStep3Caracteristicas.vue` com campos numéricos (área total, área construída, quartos, suítes, banheiros, vagas) e checkboxes (mobiliado, aceita pet, possui piscina, quintal, varanda) e textarea para outras características
- [x] 10.5 Criar `resources/js/Components/Imoveis/WizardStep4DadosComerciais.vue` com campos valor aluguel, valor venda (opcional), valor condomínio, valor IPTU, checkbox condomínio incluso, selects de responsável para IPTU/água/energia/condomínio (enum: proprietario/inquilino), valor caução sugerido, observações comerciais
- [x] 10.6 Criar `resources/js/Components/Imoveis/WizardStep5FotosDocumentos.vue` com: seção de fotos (input file múltiplo para `fotos_novas`, preview das fotos existentes em edição com botão remover que popula `fotos_remover`, seleção de foto principal via radio que seta `foto_principal_id`), seção de documentos (input file múltiplo para `documentos_novos` com campo tipo para cada arquivo, lista de documentos existentes em edição com botão remover), botão "Salvar Imóvel" com emit('submit')

## 11. Frontend: Páginas

- [x] 11.1 Criar `resources/js/Pages/Admin/Imoveis/Index.vue` com: tabela paginada, filtros reativos com debounce (busca, tipo, finalidade, status, cidade), coluna foto principal em miniatura, badges de status e tipo, ações (ver, editar, alterar status via SweetAlert), breadcrumb e botão "Novo Imóvel"
- [x] 11.2 Criar `resources/js/Pages/Admin/Imoveis/Create.vue` com `useForm<FormularioImovelData>` inicializado com valores padrão, recebendo props `proprietarios` e `corretores` do controller, chamando `form.post(route('imoveis.store'), { forceFormData: true })` no submit
- [x] 11.3 Criar `resources/js/Pages/Admin/Imoveis/Edit.vue` com `useForm<FormularioImovelData>` inicializado com dados do imóvel existente (incluindo características e dados comerciais), recebendo props `imovel`, `proprietarios` e `corretores`, chamando `form.put(route('imoveis.update', imovel.id), { forceFormData: true })` no submit
- [x] 11.4 Criar `resources/js/Pages/Admin/Imoveis/Show.vue` com todas as seções: cabeçalho com badge de status e ações, card de dados principais, card de endereço, `CardCaracteristicas`, `CardDadosComerciais`, `GaleriaFotos`, lista de documentos com link para download, seção de contratos vinculados (vazia com mensagem), SweetAlert para alteração de status
- [x] 11.5 Executar `docker compose exec app npm run build` e verificar build sem erros

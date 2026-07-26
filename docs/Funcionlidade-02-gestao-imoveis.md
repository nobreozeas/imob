# Funcionalidade — Gestão de Imóveis

## Sistema de Gestão Imobiliária

Este documento especifica a funcionalidade de **Gestão de Imóveis** para o MVP do Sistema de Gestão Imobiliária.

A funcionalidade será responsável por permitir o cadastro, consulta, edição, visualização, inativação e controle operacional dos imóveis administrados pela imobiliária.

---

# 1. Objetivo da funcionalidade

Permitir que a imobiliária mantenha uma base organizada de imóveis, vinculando cada imóvel ao seu proprietário, controlando seu status operacional e disponibilizando essas informações para os demais módulos do sistema, principalmente o módulo de contratos de locação.

A funcionalidade deve permitir:

- Cadastrar imóveis.
- Editar imóveis.
- Visualizar detalhes do imóvel.
- Listar imóveis com filtros, busca, ordenação e paginação.
- Excluir logicamente imóveis via soft delete.
- Restaurar imóveis excluídos, quando permitido.
- Controlar status do imóvel.
- Vincular imóvel a um proprietário.
- Fazer upload de fotos.
- Fazer upload de documentos.
- Consultar histórico básico de contratos vinculados ao imóvel.

---

# 2. Escopo do MVP

Para o MVP, a Gestão de Imóveis deverá contemplar:

- CRUD completo de imóveis.
- Vínculo obrigatório com proprietário.
- Status do imóvel.
- Dados de endereço.
- Dados financeiros básicos.
- Fotos do imóvel.
- Documentos do imóvel.
- Tela de listagem com filtros.
- Tela de cadastro em wizard/steps.
- Tela de detalhes com tabs.

Ficam fora do escopo inicial:

- Integração com portais imobiliários.
- Geolocalização/mapa.
- Avaliação automática de preço.
- Reserva com fluxo completo.
- Captação de imóvel por lead.
- Controle de vistorias.
- Venda de imóveis como fluxo comercial completo.

---

# 3. Regras de negócio

## 3.1 Cadastro

```text
1. Todo imóvel deve possuir um proprietário vinculado.
2. Todo imóvel deve possuir um código interno único.
3. O código interno será gerado automaticamente pelo sistema.
4. O imóvel deve possuir tipo, finalidade, endereço, valor de aluguel e status.
5. O valor de venda será opcional no MVP.
6. Imóveis podem possuir fotos e documentos anexados.
7. O cadastro deve usar UUID nas entidades principais.
8. Exclusões devem ser lógicas, usando soft delete.
```

---

## 3.2 Status do imóvel

Os status permitidos são:

```text
disponivel
reservado
alugado
inativo
```

### Disponível

Imóvel apto para ser vinculado a um novo contrato de locação.

```text
- Pode ser selecionado na criação de contrato.
- Pode ser editado.
- Pode ser inativado, desde que não exista contrato ativo.
```

### Reservado

Imóvel temporariamente bloqueado para negociação ou análise.

```text
- Não pode ser selecionado para contrato ativo.
- Pode voltar para disponível.
- Pode ser editado.
```

### Alugado

Imóvel com contrato de locação ativo.

```text
- Não pode ser selecionado em novo contrato.
- Não deve permitir exclusão.
- Não deve permitir alteração manual para disponível sem encerramento do contrato.
- Alteração para disponível deve ocorrer pelo fluxo de encerramento/rescisão do contrato.
```

### Inativo

Imóvel fora da operação da imobiliária.

```text
- Não aparece como disponível para contrato.
- Pode ser reativado como disponível, desde que não possua impedimentos.
```

---

## 3.3 Regras relacionadas ao contrato

```text
1. Apenas imóveis com status disponível podem ser usados na criação de contrato.
2. Ao ativar um contrato, o imóvel deve mudar para alugado.
3. Ao encerrar um contrato, o imóvel poderá voltar para disponível ou inativo.
4. Um imóvel alugado não pode possuir outro contrato ativo.
5. O proprietário do contrato deve ser o proprietário atual do imóvel no momento da criação do contrato.
```

---

## 3.4 Exclusão e restauração

```text
1. Imóveis não devem ser excluídos fisicamente.
2. A exclusão deve preencher deleted_at.
3. Imóveis com contrato ativo não podem ser excluídos.
4. Imóveis excluídos não devem aparecer nas listagens padrão.
5. Usuários com permissão específica poderão visualizar e restaurar imóveis excluídos.
```

---

# 4. Campos do imóvel

## 4.1 Dados principais

```text
Código interno
Tipo do imóvel
Finalidade
Proprietário
Status
Descrição
Observações
```

## 4.2 Endereço

```text
CEP
Logradouro
Número
Complemento
Bairro
Cidade
Estado
Ponto de referência
```

## 4.3 Características

```text
Área total
Área construída
Quantidade de quartos
Quantidade de suítes
Quantidade de banheiros
Quantidade de vagas de garagem
Possui quintal
Possui piscina
Possui área de serviço
Mobiliado
```

## 4.4 Valores

```text
Valor de aluguel
Valor de venda
Valor de condomínio
Valor de IPTU
```

## 4.5 Uploads

```text
Fotos do imóvel
Documentos do imóvel
```

---

# 5. Tipos sugeridos

```text
casa
apartamento
kitnet
sala_comercial
ponto_comercial
galpao
terreno
chacara
outro
```

---

# 6. Finalidades sugeridas

```text
residencial
comercial
misto
```

---

# 7. Estrutura de banco de dados

Todas as tabelas devem usar nomes em português, snake_case e sem acentos.

---

## 7.1 Tabela `imoveis`

```text
id
uuid
codigo_interno
proprietario_id
tipo
finalidade
status
descricao
cep
logradouro
numero
complemento
bairro
cidade
estado
ponto_referencia
area_total
area_construida
quantidade_quartos
quantidade_suites
quantidade_banheiros
quantidade_vagas_garagem
possui_quintal
possui_piscina
possui_area_servico
mobiliado
valor_aluguel
valor_venda
valor_condominio
valor_iptu
observacoes
criado_por
created_at
updated_at
deleted_at
```

### Observações

```text
- codigo_interno deve ser único.
- proprietario_id é obrigatório.
- status deve aceitar apenas os valores definidos.
- valor_aluguel deve ser obrigatório para imóveis disponíveis para locação.
- valor_venda é opcional no MVP.
```

---

## 7.2 Tabela `fotos_imoveis`

```text
id
uuid
imovel_id
nome_original
caminho
mime_type
tamanho
ordem
principal
created_at
updated_at
deleted_at
```

### Regras

```text
1. Um imóvel pode ter várias fotos.
2. Apenas uma foto deve ser marcada como principal.
3. Fotos devem ser armazenadas em disco configurado no Laravel.
4. Formatos sugeridos: jpg, jpeg, png, webp.
5. Tamanho máximo sugerido: 5MB por foto.
```

---

## 7.3 Tabela `documentos_imoveis`

```text
id
uuid
imovel_id
tipo_documento
nome_original
caminho
mime_type
tamanho
observacoes
created_at
updated_at
deleted_at
```

### Tipos de documento sugeridos

```text
matricula
iptu
condominio
contrato_administracao
procuracao
outro
```

### Regras

```text
1. Um imóvel pode ter vários documentos.
2. Documentos devem ficar vinculados ao imóvel.
3. Formatos sugeridos: pdf, jpg, jpeg, png.
4. Tamanho máximo sugerido: 10MB por documento.
```

---

# 8. Migration sugerida — `imoveis`

```php
Schema::create('imoveis', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();
    $table->string('codigo_interno')->unique();

    $table->foreignId('proprietario_id')
        ->constrained('proprietarios')
        ->restrictOnDelete();

    $table->string('tipo');
    $table->string('finalidade');
    $table->string('status')->default('disponivel');
    $table->text('descricao')->nullable();

    $table->string('cep', 20)->nullable();
    $table->string('logradouro');
    $table->string('numero', 30)->nullable();
    $table->string('complemento')->nullable();
    $table->string('bairro');
    $table->string('cidade');
    $table->string('estado', 2);
    $table->string('ponto_referencia')->nullable();

    $table->decimal('area_total', 10, 2)->nullable();
    $table->decimal('area_construida', 10, 2)->nullable();
    $table->unsignedSmallInteger('quantidade_quartos')->default(0);
    $table->unsignedSmallInteger('quantidade_suites')->default(0);
    $table->unsignedSmallInteger('quantidade_banheiros')->default(0);
    $table->unsignedSmallInteger('quantidade_vagas_garagem')->default(0);

    $table->boolean('possui_quintal')->default(false);
    $table->boolean('possui_piscina')->default(false);
    $table->boolean('possui_area_servico')->default(false);
    $table->boolean('mobiliado')->default(false);

    $table->decimal('valor_aluguel', 12, 2)->nullable();
    $table->decimal('valor_venda', 12, 2)->nullable();
    $table->decimal('valor_condominio', 12, 2)->nullable();
    $table->decimal('valor_iptu', 12, 2)->nullable();

    $table->text('observacoes')->nullable();
    $table->foreignId('criado_por')->nullable()->constrained('users')->nullOnDelete();

    $table->timestamps();
    $table->softDeletes();

    $table->index(['status', 'tipo', 'finalidade']);
    $table->index(['cidade', 'bairro']);
    $table->index('proprietario_id');
});
```

---

# 9. Migration sugerida — `fotos_imoveis`

```php
Schema::create('fotos_imoveis', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->foreignId('imovel_id')
        ->constrained('imoveis')
        ->cascadeOnDelete();

    $table->string('nome_original');
    $table->string('caminho');
    $table->string('mime_type', 100)->nullable();
    $table->unsignedBigInteger('tamanho')->nullable();
    $table->unsignedSmallInteger('ordem')->default(0);
    $table->boolean('principal')->default(false);

    $table->timestamps();
    $table->softDeletes();

    $table->index('imovel_id');
});
```

---

# 10. Migration sugerida — `documentos_imoveis`

```php
Schema::create('documentos_imoveis', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->foreignId('imovel_id')
        ->constrained('imoveis')
        ->cascadeOnDelete();

    $table->string('tipo_documento')->default('outro');
    $table->string('nome_original');
    $table->string('caminho');
    $table->string('mime_type', 100)->nullable();
    $table->unsignedBigInteger('tamanho')->nullable();
    $table->text('observacoes')->nullable();

    $table->timestamps();
    $table->softDeletes();

    $table->index(['imovel_id', 'tipo_documento']);
});
```

---

# 11. Models e relacionamentos

## 11.1 Model `Imovel`

```php
class Imovel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'imoveis';

    protected $fillable = [
        'uuid',
        'codigo_interno',
        'proprietario_id',
        'tipo',
        'finalidade',
        'status',
        'descricao',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'ponto_referencia',
        'area_total',
        'area_construida',
        'quantidade_quartos',
        'quantidade_suites',
        'quantidade_banheiros',
        'quantidade_vagas_garagem',
        'possui_quintal',
        'possui_piscina',
        'possui_area_servico',
        'mobiliado',
        'valor_aluguel',
        'valor_venda',
        'valor_condominio',
        'valor_iptu',
        'observacoes',
        'criado_por',
    ];

    protected $casts = [
        'area_total' => 'decimal:2',
        'area_construida' => 'decimal:2',
        'valor_aluguel' => 'decimal:2',
        'valor_venda' => 'decimal:2',
        'valor_condominio' => 'decimal:2',
        'valor_iptu' => 'decimal:2',
        'possui_quintal' => 'boolean',
        'possui_piscina' => 'boolean',
        'possui_area_servico' => 'boolean',
        'mobiliado' => 'boolean',
    ];

    public function proprietario(): BelongsTo
    {
        return $this->belongsTo(Proprietario::class, 'proprietario_id');
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(FotoImovel::class, 'imovel_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoImovel::class, 'imovel_id');
    }

    public function contratosLocacao(): HasMany
    {
        return $this->hasMany(ContratoLocacao::class, 'imovel_id');
    }
}
```

---

# 12. Enums sugeridos

## 12.1 `StatusImovelEnum`

```php
enum StatusImovelEnum: string
{
    case DISPONIVEL = 'disponivel';
    case RESERVADO = 'reservado';
    case ALUGADO = 'alugado';
    case INATIVO = 'inativo';
}
```

## 12.2 `TipoImovelEnum`

```php
enum TipoImovelEnum: string
{
    case CASA = 'casa';
    case APARTAMENTO = 'apartamento';
    case KITNET = 'kitnet';
    case SALA_COMERCIAL = 'sala_comercial';
    case PONTO_COMERCIAL = 'ponto_comercial';
    case GALPAO = 'galpao';
    case TERRENO = 'terreno';
    case CHACARA = 'chacara';
    case OUTRO = 'outro';
}
```

## 12.3 `FinalidadeImovelEnum`

```php
enum FinalidadeImovelEnum: string
{
    case RESIDENCIAL = 'residencial';
    case COMERCIAL = 'comercial';
    case MISTO = 'misto';
}
```

---

# 13. Backend sugerido

## 13.1 Controllers

```text
ImovelController
FotoImovelController
DocumentoImovelController
```

## 13.2 Form Requests

```text
StoreImovelRequest
UpdateImovelRequest
StoreFotoImovelRequest
StoreDocumentoImovelRequest
```

## 13.3 Services

```text
ImovelService
FotoImovelService
DocumentoImovelService
AlterarStatusImovelService
```

## 13.4 Policies

```text
ImovelPolicy
```

Permissões sugeridas:

```text
imoveis.visualizar
imoveis.criar
imoveis.editar
imoveis.excluir
imoveis.restaurar
imoveis.alterar_status
imoveis.gerenciar_fotos
imoveis.gerenciar_documentos
```

---

# 14. Rotas Laravel sugeridas

```php
Route::resource('imoveis', ImovelController::class);

Route::post('imoveis/{imovel}/fotos', [FotoImovelController::class, 'store'])
    ->name('imoveis.fotos.store');

Route::delete('imoveis/{imovel}/fotos/{foto}', [FotoImovelController::class, 'destroy'])
    ->name('imoveis.fotos.destroy');

Route::patch('imoveis/{imovel}/fotos/{foto}/principal', [FotoImovelController::class, 'definirPrincipal'])
    ->name('imoveis.fotos.principal');

Route::post('imoveis/{imovel}/documentos', [DocumentoImovelController::class, 'store'])
    ->name('imoveis.documentos.store');

Route::delete('imoveis/{imovel}/documentos/{documento}', [DocumentoImovelController::class, 'destroy'])
    ->name('imoveis.documentos.destroy');

Route::patch('imoveis/{imovel}/status', [ImovelController::class, 'alterarStatus'])
    ->name('imoveis.alterar-status');

Route::patch('imoveis/{imovel}/restaurar', [ImovelController::class, 'restore'])
    ->name('imoveis.restore');
```

---

# 15. Validações principais

## 15.1 Store/Update Imóvel

```text
codigo_interno: obrigatório, único
proprietario_id: obrigatório, existente em proprietarios
tipo: obrigatório, valor permitido
finalidade: obrigatório, valor permitido
status: obrigatório, valor permitido
logradouro: obrigatório
bairro: obrigatório
cidade: obrigatório
estado: obrigatório, tamanho 2
valor_aluguel: obrigatório quando finalidade for residencial, comercial ou misto para locação
valor_venda: opcional
quantidade_quartos: inteiro >= 0
quantidade_banheiros: inteiro >= 0
quantidade_vagas_garagem: inteiro >= 0
```

---

# 16. Tela de listagem de imóveis

## 16.1 Filtros

```text
Busca geral
Status
Tipo
Finalidade
Proprietário
Cidade
Bairro
Valor mínimo de aluguel
Valor máximo de aluguel
Imóveis excluídos
```

## 16.2 Colunas da tabela

```text
Código
Foto
Tipo
Endereço
Bairro/Cidade
Proprietário
Valor do aluguel
Status
Ações
```

## 16.3 Ações

```text
Visualizar
Editar
Alterar status
Gerenciar fotos
Gerenciar documentos
Excluir
Restaurar
```

## 16.4 Indicadores rápidos no topo

```text
Total de imóveis
Disponíveis
Alugados
Reservados
Inativos
```

---

# 17. Cadastro de imóvel

Como o cadastro possui múltiplos grupos de informações, recomenda-se utilizar página completa com wizard/steps.

## 17.1 Steps sugeridos

```text
1. Dados principais
2. Endereço
3. Características
4. Valores
5. Fotos e documentos
6. Revisão
```

---

## 17.2 Step 1 — Dados principais

Campos:

```text
Código interno
Proprietário
Tipo
Finalidade
Status
Descrição
```

Regras:

```text
- Proprietário é obrigatório.
- Código interno deve ser único.
- Status padrão deve ser disponível.
```

---

## 17.3 Step 2 — Endereço

Campos:

```text
CEP
Logradouro
Número
Complemento
Bairro
Cidade
Estado
Ponto de referência
```

Regras:

```text
- Logradouro, bairro, cidade e estado são obrigatórios.
- CEP pode ser obrigatório futuramente, mas no MVP pode ser opcional.
```

---

## 17.4 Step 3 — Características

Campos:

```text
Área total
Área construída
Quartos
Suítes
Banheiros
Vagas de garagem
Quintal
Piscina
Área de serviço
Mobiliado
```

---

## 17.5 Step 4 — Valores

Campos:

```text
Valor do aluguel
Valor de venda
Valor de condomínio
Valor de IPTU
```

Regras:

```text
- Valor de aluguel deve ser maior que zero quando o imóvel estiver disponível para locação.
- Valor de venda é opcional, pois venda de imóveis não faz parte do fluxo comercial do MVP.
```

---

## 17.6 Step 5 — Fotos e documentos

Campos/Ações:

```text
Upload de fotos
Definir foto principal
Remover foto
Upload de documentos
Informar tipo do documento
Remover documento
```

---

## 17.7 Step 6 — Revisão

Exibir resumo completo antes de salvar:

```text
Dados principais
Endereço
Características
Valores
Fotos
Documentos
```

Ações:

```text
Salvar imóvel
Voltar
Cancelar
```

---

# 18. Tela de detalhes do imóvel

A tela de detalhes deve usar tabs.

## Tabs sugeridas

```text
Resumo
Fotos
Documentos
Contratos
Histórico
```

---

## 18.1 Tab Resumo

Exibir:

```text
Código interno
Status
Tipo
Finalidade
Proprietário
Endereço completo
Características
Valores
Observações
```

---

## 18.2 Tab Fotos

Exibir:

```text
Galeria de fotos
Foto principal
Ações de upload/remover/definir principal
```

---

## 18.3 Tab Documentos

Colunas:

```text
Tipo
Nome do arquivo
Tamanho
Data de envio
Ações
```

---

## 18.4 Tab Contratos

Colunas:

```text
Código do contrato
Inquilino
Data início
Data fim
Valor aluguel
Status
Ações
```

---

## 18.5 Tab Histórico

Registrar eventos relevantes:

```text
Imóvel criado
Imóvel editado
Status alterado
Foto adicionada
Documento adicionado
Imóvel vinculado a contrato
Contrato encerrado
Imóvel excluído
Imóvel restaurado
```

---

# 19. Frontend Vue/Inertia

## 19.1 Páginas sugeridas

```text
resources/js/Pages/Imoveis/Index.vue
resources/js/Pages/Imoveis/Create.vue
resources/js/Pages/Imoveis/Edit.vue
resources/js/Pages/Imoveis/Show.vue
```

## 19.2 Componentes sugeridos

```text
resources/js/Components/Imoveis/ImovelFormWizard.vue
resources/js/Components/Imoveis/Steps/StepDadosPrincipais.vue
resources/js/Components/Imoveis/Steps/StepEndereco.vue
resources/js/Components/Imoveis/Steps/StepCaracteristicas.vue
resources/js/Components/Imoveis/Steps/StepValores.vue
resources/js/Components/Imoveis/Steps/StepMidias.vue
resources/js/Components/Imoveis/Steps/StepRevisao.vue
resources/js/Components/Imoveis/ImovelStatusBadge.vue
resources/js/Components/Imoveis/ImovelResumoCards.vue
resources/js/Components/Imoveis/ImovelFilters.vue
resources/js/Components/Imoveis/ImovelTable.vue
resources/js/Components/Imoveis/GaleriaFotosImovel.vue
resources/js/Components/Imoveis/TabelaDocumentosImovel.vue
resources/js/Components/Imoveis/TabsImovel.vue
```

## 19.3 Types TypeScript

```ts
export type StatusImovel = 'disponivel' | 'reservado' | 'alugado' | 'inativo';

export type TipoImovel =
  | 'casa'
  | 'apartamento'
  | 'kitnet'
  | 'sala_comercial'
  | 'ponto_comercial'
  | 'galpao'
  | 'terreno'
  | 'chacara'
  | 'outro';

export type FinalidadeImovel = 'residencial' | 'comercial' | 'misto';

export interface Imovel {
  id: number;
  uuid: string;
  codigo_interno: string;
  proprietario_id: number;
  tipo: TipoImovel;
  finalidade: FinalidadeImovel;
  status: StatusImovel;
  descricao?: string | null;
  cep?: string | null;
  logradouro: string;
  numero?: string | null;
  complemento?: string | null;
  bairro: string;
  cidade: string;
  estado: string;
  valor_aluguel?: number | null;
  valor_venda?: number | null;
  valor_condominio?: number | null;
  valor_iptu?: number | null;
  proprietario?: {
    id: number;
    nome: string;
  };
}
```

---

# 20. Fluxo de criação

```text
Usuário acessa Imóveis
   ↓
Clica em Novo Imóvel
   ↓
Preenche dados principais
   ↓
Informa endereço
   ↓
Informa características
   ↓
Informa valores
   ↓
Anexa fotos/documentos, se houver
   ↓
Revisa os dados
   ↓
Salva o imóvel
   ↓
Sistema registra imóvel com status definido
   ↓
Imóvel passa a aparecer na listagem
```

---

# 21. Fluxo de alteração de status

```text
Usuário acessa detalhes ou listagem
   ↓
Seleciona alterar status
   ↓
Sistema valida regras do imóvel
   ↓
Usuário confirma alteração via SweetAlert2
   ↓
Sistema altera status
   ↓
Sistema registra histórico
```

## Regras da alteração de status

```text
1. Imóvel alugado não pode mudar para disponível manualmente se possuir contrato ativo.
2. Imóvel disponível pode mudar para reservado ou inativo.
3. Imóvel reservado pode voltar para disponível ou inativo.
4. Imóvel inativo pode voltar para disponível.
5. Alteração para alugado deve ocorrer preferencialmente pelo fluxo de contrato.
```

---

# 22. Consultas e filtros no backend

A listagem deve usar eager loading para proprietário e foto principal.

```php
Imovel::query()
    ->with(['proprietario:id,nome', 'fotos' => function ($query) {
        $query->where('principal', true);
    }])
    ->when($request->search, function ($query, $search) {
        $query->where(function ($query) use ($search) {
            $query->where('codigo_interno', 'ilike', "%{$search}%")
                ->orWhere('logradouro', 'ilike', "%{$search}%")
                ->orWhere('bairro', 'ilike', "%{$search}%")
                ->orWhere('cidade', 'ilike', "%{$search}%");
        });
    })
    ->when($request->status, fn ($query, $status) => $query->where('status', $status))
    ->when($request->tipo, fn ($query, $tipo) => $query->where('tipo', $tipo))
    ->when($request->finalidade, fn ($query, $finalidade) => $query->where('finalidade', $finalidade))
    ->latest()
    ->paginate(15)
    ->withQueryString();
```

---

# 23. Ordem recomendada de implementação

```text
1. Criar enums de status, tipo e finalidade do imóvel.
2. Criar migrations de imoveis, fotos_imoveis e documentos_imoveis.
3. Criar models e relacionamentos.
4. Criar factories e seeders básicos.
5. Criar Form Requests.
6. Criar ImovelPolicy.
7. Criar ImovelService.
8. Criar ImovelController com index, create, store, show, edit, update, destroy e restore.
9. Criar FotoImovelController.
10. Criar DocumentoImovelController.
11. Criar rotas.
12. Criar types TypeScript.
13. Criar página Index com tabela, filtros e paginação.
14. Criar badges de status.
15. Criar wizard de cadastro.
16. Criar página de edição.
17. Criar página de detalhes com tabs.
18. Implementar upload de fotos.
19. Implementar upload de documentos.
20. Implementar alteração de status com SweetAlert2.
21. Implementar validação de impedimento para exclusão.
22. Implementar testes de regras principais.
```

---

# 24. Critérios de aceite

```text
1. O usuário consegue cadastrar um imóvel com proprietário vinculado.
2. O usuário consegue listar imóveis com paginação.
3. O usuário consegue pesquisar por código, endereço, bairro ou cidade.
4. O usuário consegue filtrar por status, tipo, finalidade e proprietário.
5. O usuário consegue editar os dados do imóvel.
6. O usuário consegue visualizar detalhes do imóvel em tela com tabs.
7. O usuário consegue anexar fotos ao imóvel.
8. O usuário consegue definir uma foto principal.
9. O usuário consegue anexar documentos ao imóvel.
10. O usuário consegue alterar status respeitando as regras de negócio.
11. O usuário não consegue excluir imóvel com contrato ativo.
12. A exclusão de imóvel usa soft delete.
13. Imóveis disponíveis ficam aptos para seleção no contrato de locação.
14. Imóveis alugados não aparecem na seleção de novo contrato.
15. Todas as listagens usam tabela, filtros, ordenação e paginação.
```

---

# 25. Testes sugeridos

## 25.1 Backend

```text
Deve cadastrar imóvel com dados válidos.
Não deve cadastrar imóvel sem proprietário.
Não deve cadastrar imóvel com código interno duplicado.
Deve listar imóveis paginados.
Deve filtrar imóveis por status.
Deve filtrar imóveis por tipo.
Deve filtrar imóveis por proprietário.
Deve atualizar imóvel.
Deve impedir exclusão de imóvel com contrato ativo.
Deve excluir imóvel com soft delete.
Deve restaurar imóvel excluído.
Deve impedir alteração manual de alugado para disponível quando houver contrato ativo.
```

## 25.2 Frontend

```text
Deve exibir tabela de imóveis.
Deve aplicar filtros.
Deve exibir badge de status correto.
Deve navegar entre steps do wizard.
Deve validar campos obrigatórios.
Deve exibir confirmação antes de excluir.
Deve exibir tabs na tela de detalhes.
```

---

# 26. Observações finais para o MVP

O cadastro de imóveis deve ser completo o suficiente para apoiar a operação de locação, mas simples o bastante para não atrasar o MVP.

A regra mais importante desta funcionalidade é o controle de status, pois ela impacta diretamente a criação, ativação e encerramento de contratos de locação.

## Context

O ImobGestor segue stack Laravel 13 + Inertia 3 + Vue 3 + TypeScript + Tailwind CSS 4 + DaisyUI 5 + PostgreSQL, rodando em Docker. O módulo de Gestão de Clientes já foi implementado e serve de referência direta de padrões: UUID em todos os PKs (`HasUuids`), SoftDeletes, Spatie Permissions com `App\Models\Permission` e `App\Models\Role` estendendo os modelos Spatie com `HasUuids`, serviço em `App\Services\`, Policy registrada em `AppServiceProvider`, wizard de múltiplas etapas (direto mutation via `v-model` sem spread), Inertia `useForm` como proxy reativo.

O proprietário do imóvel é um `Cliente` com papel `proprietario` (tabela `cliente_papeis`). O corretor responsável é um `User`.

## Goals / Non-Goals

**Goals:**
- CRUD completo de imóveis com wizard de 5 etapas
- Listagem com filtros e paginação
- Tela de detalhes do imóvel
- Controle de status com regras de negócio
- Upload de fotos (com definição de foto principal) e documentos
- Permissões granulares via Spatie
- Padrões de código idênticos ao módulo de clientes

**Non-Goals:**
- Integração com contratos (será feita no módulo de contratos)
- Vistoria de imóveis
- Portal público de imóveis / site
- Storage S3 ou CDN externo (usar `public` disk do Laravel)
- Geolocalização / mapa

## Decisions

### 1. Estrutura de tabelas: normalizada por responsabilidade

Cinco tabelas separadas em vez de uma única tabela monolítica:

| Tabela | Responsabilidade |
|---|---|
| `imoveis` | Dados principais + endereço (inline, como em `clientes`) |
| `imovel_caracteristicas` | Características físicas (OneToOne) |
| `imovel_dados_comerciais` | Valores e responsabilidades financeiras (OneToOne) |
| `imovel_fotos` | Fotos com flag `is_principal` e campo `ordem` |
| `imovel_documentos` | Documentos com tipo livre |

Alternativa descartada: JSONB para características — prejudica queries, índices e type safety.
Alternativa descartada: tabela única com ~50 colunas nullable — dificulta manutenção e leitura.

`imovel_caracteristicas` e `imovel_dados_comerciais` são criados junto com o imóvel (via `DB::transaction` no service), garantindo que sempre existam.

### 2. Upload de fotos e documentos: via `forceFormData` no wizard

O Step 5 do wizard aceita `File[]` no `useForm`. O submit usa `form.post(url, { forceFormData: true })` (create) ou `form.post(url + '?_method=PUT', { forceFormData: true })` para edição, já que Inertia não suporta `PUT` com multipart nativamente.

Para edição, o form carrega:
- `fotos_novas: File[]` — novas fotos a adicionar
- `fotos_remover: string[]` — IDs das fotos existentes a remover
- `documentos_novos: File[]` — novos documentos
- `documentos_remover: string[]` — IDs dos documentos a remover
- `foto_principal_id: string` — ID da foto a marcar como principal

Fotos existentes são exibidas no step 5 via prop `imovel.fotos` (relação carregada pelo controller).

Armazenamento: `storage/app/public/imoveis/{uuid}/fotos/` e `storage/app/public/imoveis/{uuid}/documentos/`. Servidos via symlink `php artisan storage:link`.

Alternativa descartada: endpoint separado para upload após criação — exige duas requisições e estado temporário.

### 3. Wizard: 5 steps, mesmo padrão do módulo de clientes

Componente `WizardImovel.vue` aceita `:form` (proxy Inertia reativo) e `:errors`. Cada step usa `v-model="form.campo"` diretamente. O submit único acontece no Step 5.

O `CAMPO_PARA_ETAPA` mapeia erros de validação de volta à etapa correspondente, para redirect automático quando a API retorna erros.

### 4. Status: enum controlado, sem transição automática nesta fase

Status possíveis: `disponivel`, `reservado`, `alugado`, `em_manutencao`, `inativo`.

A transição automática via contratos será implementada no módulo de contratos. Por ora, o usuário altera o status manualmente via ação dedicada (`PATCH /imoveis/{id}/status`). A única regra de negócio implementada nesta fase: **não permitir setar `disponivel` manualmente se houver contrato ativo** (verificação via `imovel.contratos()->ativo()->exists()` — a coluna de contratos ainda não existe, então a verificação é implementada mas retorna sempre `false` até o módulo de contratos existir).

### 5. Proprietário: apenas clientes com papel proprietário

O campo `proprietario_id` (FK -> `clientes.id`) só aceita clientes que tenham papel `proprietario` em `cliente_papeis`. O controller de seleção de proprietário faz `Cliente::with('papeis')->whereHas('papeis', fn($q) => $q->where('papel', 'proprietario'))`. Não é necessário criar uma tabela intermediária.

### 6. Código interno: geração automática com override manual

O código interno (`codigo`) é gerado automaticamente no service no formato `IMO-{YYYYMM}-{sequence}` se não fornecido pelo usuário. O campo é editável e único (`UNIQUE` constraint na tabela). O frontend mostra o campo em Step 1 com placeholder de sugestão.

### 7. Permissões: mesma estrutura do módulo de clientes

Permissões criadas via seeder: `imoveis.viewAny`, `imoveis.view`, `imoveis.create`, `imoveis.update`, `imoveis.alterar-status`. Atribuídas ao role `admin`. Policy `ImovelPolicy` registrada em `AppServiceProvider`.

## Risks / Trade-offs

- **Upload com `?_method=PUT`**: Inertia não envia `PUT` via multipart. Usar `form.post(url + '?_method=PUT', { forceFormData: true })` com `Method::spoofing()` no Laravel (já habilitado por padrão). → Mitigation: documentar no controller e testar.
- **Fotos órfãs ao deletar imóvel**: SoftDelete no imóvel não deleta arquivos físicos. → Mitigation: observer ou job de limpeza periódica (fora do escopo desta fase).
- **Foto principal exclusiva**: apenas uma foto pode ser `is_principal = true`. → Mitigation: o service usa `updateOrCreate` e zera `is_principal` das demais na mesma transação.
- **Geração de código duplicado em concorrência**: `IMO-{YYYYMM}-{sequence}` pode colidar em alta carga. → Mitigation: `UNIQUE constraint` + retry no service; aceitável para volume de imobiliárias pequenas/médias.

## Open Questions

- O módulo de contratos definirá o campo `status` como gerenciado automaticamente. Quando implementado, a action `alterar-status` deve validar as transições permitidas. Por ora, a verificação de "contrato ativo" retorna `false` (inexistente).
- O limite de fotos por imóvel não está definido nos docs. Implementar sem limite por ora; adicionar configuração futura se necessário.

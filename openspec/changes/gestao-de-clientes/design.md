## Context

O ImobGestor já possui layout administrativo (AdminLayout, sidebar, topbar), autenticação completa com Spatie permissions e modelos usando UUIDs + SoftDeletes. O módulo de Gestão de Clientes é o primeiro módulo de negócio do sistema e servirá de base para imóveis e contratos. O projeto usa Laravel 13, Inertia 3, Vue 3, TypeScript, Tailwind CSS 4, DaisyUI e PostgreSQL.

## Goals / Non-Goals

**Goals:**
- Criar entidade central `Cliente` que elimina duplicidade entre proprietários e inquilinos.
- Suportar múltiplos papéis por cliente (proprietário, inquilino ou ambos) em uma única entidade.
- Fornecer CRUD completo com listagem paginada, filtros e busca textual.
- Proteger contra exclusão física de clientes com vínculos ativos.
- Integrar com o sistema de permissões Spatie existente.
- Seguir convenções do projeto: UUIDs, SoftDeletes, nomenclatura em português, Inertia + Vue.

**Non-Goals:**
- Implementação do módulo de imóveis ou contratos (apenas preparar a base).
- Histórico de auditoria completo (campo simples de observações é suficiente nesta fase).
- Upload de documentos ou fotos do cliente.
- Integração com ViaCEP ou validação de CPF/CNPJ via API externa nesta fase.

## Decisions

### 1. Modelagem: tabelas separadas por papel

**Decisão**: Tabela principal `clientes` com tabelas auxiliares `cliente_dados_proprietario` e `cliente_dados_inquilino`. Os papéis são controlados por uma tabela `cliente_papeis` (relação N:N com uma enumeração de papéis).

**Alternativas consideradas**:
- Colunas na tabela principal com `nullable`: rejeitado — mistura dados de contextos distintos e dificulta queries de proprietários/inquilinos.
- Tabelas completamente separadas `proprietarios` e `inquilinos`: rejeitado — causa duplicidade cadastral, o principal problema a ser resolvido.

**Rationale**: A separação por tabelas mantém a tabela principal enxuta e permite que os dados de proprietário/inquilino sejam nulos sem colunas desnecessárias na entidade principal.

### 2. Papéis como tabela de junção (não enum em array)

**Decisão**: Tabela `cliente_papeis` com colunas `cliente_id` e `papel` (enum: `proprietario`, `inquilino`).

**Alternativas consideradas**:
- Array de enum no PostgreSQL: rejeitado — dificulta filtragem e indexação.
- Colunas booleanas `e_proprietario` e `e_inquilino`: rejeitado — não é extensível e quebra normalização.

**Rationale**: Tabela de junção permite queries eficientes por papel, é extensível para papéis futuros e segue o padrão Eloquent de relacionamentos.

### 3. UUIDs e SoftDeletes obrigatórios

**Decisão**: Todos os models usam `HasUuids` + `SoftDeletes`, seguindo o padrão do `User`.

**Rationale**: Consistência com o padrão do projeto. SoftDeletes atende à regra de negócio que proíbe exclusão física de clientes com vínculos.

### 4. Inertia + Vue sem API REST separada

**Decisão**: Controllers retornam `Inertia::render()` diretamente, sem camada de API separada.

**Rationale**: Toda a interface é SPA via Inertia. Não há necessidade de API REST pública nesta fase.

### 5. ClienteService para lógica de negócio

**Decisão**: Criar `App\Services\Clientes\ClienteService` para centralizar validações de negócio (verificar vínculos antes de remover papel, proteger inativação com vínculos, etc.).

**Rationale**: Mantém controllers enxutos e facilita testes unitários da lógica de negócio.

### 6. Permissões via Spatie

**Decisão**: Usar as permissões Spatie já instaladas: `clientes.ver`, `clientes.criar`, `clientes.editar`, `clientes.ativar-inativar`.

**Rationale**: Aproveita a infraestrutura existente sem criar sistema paralelo.

## Estrutura de Dados

### Tabela `clientes`
```
id (uuid, PK)
tipo_pessoa (enum: fisica, juridica)
nome / razao_social (string, NOT NULL)
nome_fantasia (string, nullable — PJ)
cpf (string 14, unique, nullable)
cnpj (string 18, unique, nullable)
rg (string, nullable)
data_nascimento (date, nullable — PF)
telefone_principal (string, nullable)
whatsapp (string, nullable)
telefone_secundario (string, nullable)
email_principal (string, nullable)
email_alternativo (string, nullable)
cep (string, nullable)
logradouro (string, nullable)
numero (string, nullable)
complemento (string, nullable)
bairro (string, nullable)
cidade (string, nullable)
estado (string 2, nullable)
ponto_referencia (string, nullable)
observacoes (text, nullable)
status (enum: ativo, inativo, default: ativo)
criado_por (uuid FK users.id, nullable)
deleted_at (timestamp)
timestamps
```

### Tabela `cliente_papeis`
```
id (uuid, PK)
cliente_id (uuid FK clientes.id)
papel (enum: proprietario, inquilino)
UNIQUE (cliente_id, papel)
timestamps
```

### Tabela `cliente_dados_proprietario`
```
id (uuid, PK)
cliente_id (uuid FK clientes.id, unique)
banco (string, nullable)
agencia (string, nullable)
conta (string, nullable)
tipo_conta (string, nullable)
chave_pix (string, nullable)
tipo_chave_pix (enum: cpf, cnpj, email, telefone, aleatoria, nullable)
percentual_administracao (decimal 5,2, nullable)
emite_nota_fiscal (boolean, default false)
preferencia_recebimento (string, nullable)
observacoes_repasse (text, nullable)
timestamps
```

### Tabela `cliente_dados_inquilino`
```
id (uuid, PK)
cliente_id (uuid FK clientes.id, unique)
profissao (string, nullable)
renda_mensal (decimal 10,2, nullable)
local_trabalho (string, nullable)
telefone_comercial (string, nullable)
contato_emergencia (string, nullable)
observacoes_cadastrais (text, nullable)
restricoes (text, nullable)
timestamps
```

## Estrutura de Arquivos

### Backend
```
app/
  Http/
    Controllers/
      Clientes/
        ClienteController.php
    Requests/
      Clientes/
        StoreClienteRequest.php
        UpdateClienteRequest.php
  Models/
    Cliente.php
    ClientePapel.php
    ClienteDadosProprietario.php
    ClienteDadosInquilino.php
  Services/
    Clientes/
      ClienteService.php
  Policies/
    ClientePolicy.php
database/migrations/
  ..._create_clientes_table.php
  ..._create_cliente_papeis_table.php
  ..._create_cliente_dados_proprietario_table.php
  ..._create_cliente_dados_inquilino_table.php
```

### Frontend
```
resources/js/
  Pages/
    Admin/
      Clientes/
        Index.vue
        Create.vue
        Edit.vue
        Show.vue
  Components/
    Clientes/
      FormularioCliente.vue
      CardDadosProprietario.vue
      CardDadosInquilino.vue
      BadgePapel.vue
  types/
    cliente.ts
```

## Fluxo de Navegação

```
/clientes              → Index (listagem)
/clientes/criar        → Create (formulário)
/clientes/{id}         → Show (detalhes)
/clientes/{id}/editar  → Edit (formulário preenchido)
```

Ações na listagem via modal SweetAlert:
- Ativar / Inativar (PATCH `/clientes/{id}/status`)
- Nenhuma exclusão física exposta na UI

## Risks / Trade-offs

- **Risco**: Remover papel com vínculo ativo (imóvel ou contrato). → **Mitigação**: `ClienteService` verifica vínculos antes de aceitar a atualização; `UpdateClienteRequest` delega a validação de vínculos ao service e retorna erro 422 com mensagem clara.
- **Trade-off**: Não usar API REST reduz reusabilidade externa, mas acelera o desenvolvimento e é suficiente para SPA Inertia.
- **Risco**: CPF/CNPJ duplicado em edição concorrente. → **Mitigação**: unique constraint no banco de dados + validação no Form Request com `Rule::unique()->ignore($id)`.
- **Trade-off**: Dados de proprietário e inquilino em tabelas separadas aumentam o número de joins, mas mantêm o schema limpo e evitam colunas nulas em excesso.

## Migration Plan

1. Criar as 4 migrations na ordem: `clientes` → `cliente_papeis` → `cliente_dados_proprietario` → `cliente_dados_inquilino`.
2. Adicionar permissões via seeder: `clientes.ver`, `clientes.criar`, `clientes.editar`, `clientes.ativar-inativar`.
3. Não há dados existentes a migrar (sistema novo).
4. Rollback: cada migration tem método `down()` completo com `dropTable`.

## Open Questions

- ViaCEP: integração automática de endereço por CEP será adicionada ao formulário Vue? (Recomendado para UX, mas não bloqueante para o MVP.)
- Validação de CPF/CNPJ por algoritmo: aplicar só no frontend (Vue) ou também no backend? (Recomendado no backend para consistência.)

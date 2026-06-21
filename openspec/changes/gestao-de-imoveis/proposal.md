## Why

O ImobGestor não possui a funcionalidade central de cadastro e gerenciamento de imóveis, tornando impossível registrar o portfólio da imobiliária, controlar disponibilidade e vincular imóveis a contratos de locação. Sem esse módulo, nenhuma das demais funcionalidades de negócio (contratos, financeiro, dashboard) pode operar.

## What Changes

- Criação do módulo de Gestão de Imóveis com CRUD completo
- Formulário de cadastro em 5 etapas via wizard: dados principais, endereço, características, dados comerciais e fotos/documentos
- Listagem paginada com filtros por código, tipo, finalidade, status, proprietário, cidade, bairro e valor de aluguel
- Tela de visualização detalhada do imóvel com dados, fotos e histórico
- Controle de status do imóvel: Disponível, Reservado, Alugado, Em Manutenção e Inativo
- Upload de fotos (com definição de foto principal) e documentos anexos
- Validação de regras de negócio: imóvel vinculado a proprietário, código único, restrições de status por contrato ativo
- Permissões via Spatie Permissions: administrador com acesso total, suporte a permissões granulares futuras
- Soft delete em todas as entidades (preferência por inativação em vez de exclusão física)

## Capabilities

### New Capabilities

- `imovel-crud`: CRUD de imóveis com wizard de 5 etapas, listagem filtrada, visualização detalhada e controle de status
- `imovel-fotos-documentos`: Upload e gerenciamento de fotos (com foto principal) e documentos anexos vinculados ao imóvel

### Modified Capabilities

<!-- Nenhuma capacidade existente tem requisitos alterados por esta mudança -->

## Impact

- **Banco de dados**: novas tabelas `imoveis`, `imovel_caracteristicas`, `imovel_dados_comerciais`, `imovel_fotos`, `imovel_documentos`
- **Backend**: novos Models, Migrations, Service, Controller, Policy e Form Requests sob `App\Models`, `App\Services\Imoveis`, `App\Http\Controllers\Imoveis`
- **Frontend**: novas pages `Admin/Imoveis/{Index,Create,Edit,Show}.vue`, wizard com 5 steps, componentes de badge e card
- **Rotas**: grupo protegido por middleware `auth` + `must.change.password` em `routes/web.php`
- **Permissões**: seeder com permissões `imoveis.viewAny`, `imoveis.view`, `imoveis.create`, `imoveis.update`, `imoveis.alterar-status` atribuídas ao role `admin`
- **Storage**: uso do driver `local`/`public` do Laravel para armazenar fotos e documentos
- **Dependência existente**: relacionamento FK com `clientes` (proprietário do imóvel), FK opcional com `users` (corretor responsável)

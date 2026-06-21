## 1. Banco de Dados: Migrations

- [x] 1.1 Criar migration `create_clientes_table` com todos os campos da entidade principal (tipo_pessoa, nome/razao_social, nome_fantasia, cpf, cnpj, rg, data_nascimento, contatos, endereço, observacoes, status, criado_por, timestamps, deleted_at)
- [x] 1.2 Criar migration `create_cliente_papeis_table` com cliente_id (FK), papel (enum: proprietario, inquilino) e unique constraint (cliente_id, papel)
- [x] 1.3 Criar migration `create_cliente_dados_proprietario_table` com cliente_id (FK unique), campos bancários (banco, agencia, conta, tipo_conta), chave_pix, tipo_chave_pix (enum), percentual_administracao, emite_nota_fiscal, preferencia_recebimento, observacoes_repasse
- [x] 1.4 Criar migration `create_cliente_dados_inquilino_table` com cliente_id (FK unique), profissao, renda_mensal, local_trabalho, telefone_comercial, contato_emergencia, observacoes_cadastrais, restricoes

## 2. Backend: Models e Relacionamentos

- [x] 2.1 Criar model `Cliente` com HasUuids, SoftDeletes, fillable, casts, scopes (ativo, porPapel) e constantes de status e tipo_pessoa
- [x] 2.2 Criar model `ClientePapel` com HasUuids, fillable e constantes de papel (PROPRIETARIO, INQUILINO)
- [x] 2.3 Criar model `ClienteDadosProprietario` com HasUuids, fillable e casts (emite_nota_fiscal boolean, percentual_administracao decimal)
- [x] 2.4 Criar model `ClienteDadosInquilino` com HasUuids, fillable e casts (renda_mensal decimal)
- [x] 2.5 Adicionar relacionamentos no model `Cliente`: hasMany(ClientePapel), hasOne(ClienteDadosProprietario), hasOne(ClienteDadosInquilino)

## 3. Backend: ClienteService

- [x] 3.1 Criar `App\Services\Clientes\ClienteService` com método `criar(array $dados): Cliente` — salva cliente, papéis e dados adicionais em transação
- [x] 3.2 Implementar método `atualizar(Cliente $cliente, array $dados): Cliente` — atualiza cliente e sincroniza papéis e dados adicionais
- [x] 3.3 Implementar método `verificarVinculosPapel(Cliente $cliente, string $papel): bool` — verifica se há imóveis ativos (proprietário) ou contratos ativos (inquilino) vinculados (retorna false enquanto módulos não existem)
- [x] 3.4 Implementar método `alterarStatus(Cliente $cliente, string $status): void` — ativa ou inativa o cliente
- [x] 3.5 Implementar método `sincronizarDadosAdicionais(Cliente $cliente, array $dados): void` — cria/atualiza/remove dados de proprietário e inquilino conforme papéis ativos

## 4. Backend: Form Requests

- [x] 4.1 Criar `StoreClienteRequest` com validações: nome/razao_social obrigatório conforme tipo_pessoa, cpf ou cnpj único, email formato válido, papeis obrigatório e não vazio, tipo_pessoa enum válido
- [x] 4.2 Criar `UpdateClienteRequest` com as mesmas validações, ignorando o próprio cliente no unique de cpf/cnpj (`Rule::unique()->ignore($id)`), e validação de remoção de papel via `ClienteService::verificarVinculosPapel`

## 5. Backend: Controller, Policy e Rotas

- [x] 5.1 Criar `ClienteController` com métodos: `index` (listagem paginada com filtros/busca), `create`, `store`, `show`, `edit`, `update`, `alterarStatus`
- [x] 5.2 Implementar `index` com suporte a query params: `busca`, `tipo_pessoa`, `papel`, `status`, `cidade`, `ordenar_por`, `direcao`
- [x] 5.3 Criar `ClientePolicy` com gates: `viewAny` (clientes.ver), `create` (clientes.criar), `update` (clientes.editar), `alterarStatus` (clientes.ativar-inativar)
- [x] 5.4 Registrar policy no `AuthServiceProvider` (ou `AppServiceProvider`)
- [x] 5.5 Adicionar rotas no `routes/web.php` sob middleware `auth`: resource `/clientes` (exceto destroy), rota PATCH `/clientes/{cliente}/status`
- [x] 5.6 Criar seeder `ClientePermissionsSeeder` adicionando as permissões `clientes.ver`, `clientes.criar`, `clientes.editar`, `clientes.ativar-inativar` e atribuindo ao role `admin`

## 6. Frontend: Types e Composables

- [x] 6.1 Criar `resources/js/types/cliente.ts` com interfaces: `Cliente`, `ClientePapel`, `ClienteDadosProprietario`, `ClienteDadosInquilino`, `ClienteFiltros`, `ClientePaginado`
- [x] 6.2 Criar composable `resources/js/composables/useClienteStatus.ts` com helper para label e cor de status e papel

## 7. Frontend: Componentes Reutilizáveis

- [x] 7.1 Criar `BadgePapel.vue` — exibe badge visual para papel do cliente (proprietário: azul, inquilino: verde)
- [x] 7.2 Criar `BadgeStatus.vue` — exibe badge de status ativo/inativo
- [x] 7.3 Criar `CardDadosProprietario.vue` — seção de dados bancários e de repasse
- [x] 7.4 Criar `CardDadosInquilino.vue` — seção de dados de inquilino
- [x] 7.5 Criar `FormularioCliente.vue` — formulário principal com seções: Dados Principais, Papéis, Contatos, Endereço, Dados de Proprietário (condicional), Dados de Inquilino (condicional). Recebe props `modelValue` e `errors`.

## 8. Frontend: Páginas

- [x] 8.1 Criar `Pages/Admin/Clientes/Index.vue` — tabela com colunas (nome, CPF/CNPJ, tipo, telefone, email, papéis, cidade, status, data cadastro, ações), barra de busca, selects de filtro, paginação e botão "Novo Cliente"
- [x] 8.2 Criar `Pages/Admin/Clientes/Create.vue` — usa `FormularioCliente.vue`, submete via `useForm` do Inertia, redireciona para Show após sucesso
- [x] 8.3 Criar `Pages/Admin/Clientes/Edit.vue` — carrega dados do cliente via prop Inertia, usa `FormularioCliente.vue`, submete via PUT
- [x] 8.4 Criar `Pages/Admin/Clientes/Show.vue` — exibe todos os dados do cliente organizados em cards, exibe `CardDadosProprietario` se tiver papel proprietário, `CardDadosInquilino` se tiver papel inquilino, botões de editar e ativar/inativar
- [x] 8.5 Implementar confirmações SweetAlert nos botões de ativar/inativar nas páginas Index e Show

## 9. Navegação e Integração

- [x] 9.1 Adicionar item "Clientes" no menu lateral `AppSidebar.vue` com ícone e rota `/clientes`
- [x] 9.2 Adicionar breadcrumbs nas páginas de clientes (Início > Clientes > [ação])
- [x] 9.3 Executar o seeder de permissões: `php artisan db:seed --class=ClientePermissionsSeeder`


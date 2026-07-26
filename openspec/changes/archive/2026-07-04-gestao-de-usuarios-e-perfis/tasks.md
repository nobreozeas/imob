## 1. Seeders de Perfis e Permissões

- [x] 1.1 Criar `UsuarioPermissionsSeeder` em `database/seeders/` com as permissões `usuarios.viewAny`, `usuarios.view`, `usuarios.create`, `usuarios.update`, `usuarios.alterar-status`, `usuarios.reenviar-acesso` — seguindo o padrão de `ImovelPermissionsSeeder`; atribuir todas ao role `admin`
- [x] 1.2 Criar `PerfilPermissionsSeeder` em `database/seeders/` com as permissões `perfis.viewAny`, `perfis.view`; atribuir ao role `admin`
- [x] 1.3 Criar `PerfisEPermissoesSeeder` em `database/seeders/` que garante a existência dos quatro roles (`admin`, `financeiro`, `atendente`, `corretor`) e atribui as permissões corretas de cada módulo (imoveis, clientes, contratos, usuarios, perfis) conforme a matriz do PRD seção 25.5
- [x] 1.4 Registrar `UsuarioPermissionsSeeder`, `PerfilPermissionsSeeder` e `PerfisEPermissoesSeeder` no `DatabaseSeeder`
- [x] 1.5 Executar `docker compose exec app php artisan db:seed --class=UsuarioPermissionsSeeder` e `PerfilPermissionsSeeder` e `PerfisEPermissoesSeeder` para validar sem erros

## 2. Backend — Service e Policy

- [x] 2.1 Criar `app/Services/Usuarios/UsuarioService.php` com métodos: `criar(array $dados): User`, `atualizar(User $user, array $dados): User`, `ativar(User $user): void`, `inativar(User $user): void`, `reenviarAcesso(User $user): void` — `reenviarAcesso` deve gerar nova senha temporária, atualizar hash e disparar `PrimeiroAcessoNotification`
- [x] 2.2 Criar `app/Policies/UsuarioPolicy.php` com métodos: `viewAny`, `view`, `create`, `update`, `alterarStatus`, `reenviarAcesso` — checar a permissão Spatie correspondente (`usuarios.<acao>`)
- [x] 2.3 Criar `app/Policies/PerfilPolicy.php` com métodos `viewAny` e `view` — checar `perfis.viewAny` e `perfis.view`
- [x] 2.4 Registrar `UsuarioPolicy` e `PerfilPolicy` no `AppServiceProvider` via `Gate::policy()`

## 3. Backend — Form Requests

- [x] 3.1 Criar `app/Http/Requests/Usuarios/StoreUsuarioRequest.php` com regras: `name` (obrigatório, min 3), `email` (obrigatório, email, único em users não deletados), `role` (obrigatório, deve existir em roles), `status` (obrigatório, enum: ativo, inativo)
- [x] 3.2 Criar `app/Http/Requests/Usuarios/UpdateUsuarioRequest.php` com regras: `name` (obrigatório, min 3), `role` (obrigatório), `status` (obrigatório, enum: ativo, inativo) — sem validação de email

## 4. Backend — Controllers e Rotas

- [x] 4.1 Criar `app/Http/Controllers/Usuarios/UsuarioController.php` com métodos: `index()` (lista paginada com filtros: nome/email, role, status, deve_alterar_senha), `create()`, `store()` (delegar a `UsuarioService::criar()`), `edit()`, `update()` (delegar a `UsuarioService::atualizar()`), `alterarStatus()` (ativar ou inativar), `reenviarAcesso()`
- [x] 4.2 Criar `app/Http/Controllers/Perfis/PerfilController.php` com métodos `index()` e `show()` — retornar roles com suas permissions via Spatie
- [x] 4.3 Adicionar rotas em `routes/web.php` dentro do grupo `auth + must.change.password`: `Route::resource('usuarios', UsuarioController::class)->except(['destroy'])`, `Route::patch('usuarios/{usuario}/status', ...)`, `Route::post('usuarios/{usuario}/reenviar-acesso', ...)`, `Route::resource('perfis', PerfilController::class)->only(['index', 'show'])`

## 5. Frontend — TypeScript Types

- [x] 5.1 Criar ou atualizar `resources/js/types/usuario.ts` com interfaces: `Usuario` (id, name, email, status, deve_alterar_senha, ultimo_acesso_em, roles[], criado_por), `UsuarioForm` (name, email, role, status), `UsuarioFiltros` (busca, role, status, deve_alterar_senha)
- [x] 5.2 Criar `resources/js/types/perfil.ts` com interfaces: `Perfil` (id, name, guard_name, permissions[]), `Permissao` (id, name)

## 6. Frontend — Componentes

- [x] 6.1 Criar `resources/js/Components/Usuarios/BadgeStatus.vue` — badge colorido para os status `ativo` (verde), `inativo` (vermelho), `bloqueado` (laranja); reutilizar padrão dos badges de clientes/imóveis
- [x] 6.2 Criar `resources/js/Components/Usuarios/BadgePrimeiroAcesso.vue` — badge "Pendente" (amarelo) ou "Concluído" (verde) baseado em `deve_alterar_senha`

## 7. Frontend — Página de Listagem de Usuários

- [x] 7.1 Criar `resources/js/Pages/Admin/Usuarios/Index.vue` com tabela paginada, filtros (busca, role, status, primeiro acesso pendente), colunas: Nome, Email, Perfil, Status, Primeiro Acesso, Último Acesso, Ações
- [x] 7.2 Adicionar botão "Novo usuário" visível apenas para quem tem permissão `usuarios.create` (checar via `$page.props.auth.permissions`)
- [x] 7.3 Adicionar ações por linha: Editar (`usuarios.update`), Ativar/Inativar (`usuarios.alterar-status`), Reenviar acesso (`usuarios.reenviar-acesso`) — condicionais por permissão e estado do usuário
- [x] 7.4 Confirmações de ativar/inativar e reenvio de acesso devem usar SweetAlert2

## 8. Frontend — Formulários de Usuário

- [x] 8.1 Criar `resources/js/Pages/Admin/Usuarios/Create.vue` com campos: Nome, Email, Perfil (select com roles), Status (select ativo/inativo); ao submeter, exibir toast de sucesso e redirecionar para listagem
- [x] 8.2 Criar `resources/js/Pages/Admin/Usuarios/Edit.vue` com campos: Nome (editável), Email (somente leitura), Perfil (select), Status (select); ao submeter, exibir toast de sucesso

## 9. Frontend — Páginas de Perfis

- [x] 9.1 Criar `resources/js/Pages/Admin/Perfis/Index.vue` com tabela simples listando os quatro perfis padrão (Nome, Descrição, Total de Usuários, ação Visualizar)
- [x] 9.2 Criar `resources/js/Pages/Admin/Perfis/Show.vue` exibindo o nome do perfil e uma tabela de permissões organizadas por módulo (colunas: Módulo, Permissões concedidas)

## 10. Navegação e Integração

- [x] 10.1 Adicionar item "Usuários" no menu lateral (`AppSidebar.vue`) com link para `/usuarios`, visível apenas para quem tem `usuarios.viewAny`
- [x] 10.2 Adicionar item "Perfis" no menu lateral abaixo de Usuários, visível para quem tem `perfis.viewAny`
- [x] 10.3 Atualizar `HandleInertiaRequests` para compartilhar as permissões do usuário autenticado via `auth.permissions` (array de nomes de permissões) e `auth.roles` (array de nomes de roles), permitindo controle condicional no frontend
- [x] 10.4 Testar fluxo completo: criar usuário, verificar email de acesso, fazer login com senha temporária, trocar senha e acessar dashboard
- [x] 10.5 Testar reenvio de acesso: reenviar, fazer login com nova senha temporária, concluir primeiro acesso
- [x] 10.6 Testar permissões: logar como usuário `financeiro` e verificar que a rota `/usuarios` retorna 403

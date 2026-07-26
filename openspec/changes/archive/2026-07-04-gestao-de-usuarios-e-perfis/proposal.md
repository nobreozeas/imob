## Why

A infraestrutura de autenticação (login, logout, primeiro acesso, recuperação de senha, middleware) já está implementada, mas o sistema ainda não possui a interface administrativa para gerenciar os próprios usuários e seus perfis. Sem o CRUD de usuários, somente o admin criado via seeder pode existir, o que inviabiliza o uso real da imobiliária por múltiplos operadores com perfis diferentes.

## What Changes

- Criar CRUD completo de usuários internos: listar, cadastrar, editar, ativar/inativar e reenviar acesso inicial.
- Criar tela de visualização de perfis com suas permissões (consulta, sem edição no MVP).
- Criar `UsuarioController`, `UsuarioService`, `UsuarioPolicy` e Form Requests correspondentes.
- Criar `PerfilController` e `PerfilPolicy` para listar e visualizar perfis.
- Criar `UsuarioPermissionsSeeder` com permissões do módulo `usuarios`.
- Criar `PerfilPermissionsSeeder` com permissões do módulo `perfis`.
- Criar `PerfisEPermissoesSeeder` com a matriz completa de permissões por perfil (Administrador, Financeiro, Atendente, Corretor) para todos os módulos já existentes.
- Adicionar rotas de ativar/inativar e reenviar acesso inicial.
- Criar páginas Vue: `Usuarios/Index`, `Usuarios/Create`, `Usuarios/Edit` e `Perfis/Index`.

## Capabilities

### New Capabilities

- `user-management`: Gestão de usuários internos — listar com filtros, cadastrar com envio de acesso inicial, editar perfil e status, ativar/inativar e reenviar acesso por email.
- `role-management`: Visualização de perfis padrão e suas permissões — tela somente leitura com a matriz de acesso por módulo.

### Modified Capabilities

<!-- Nenhuma capability existente tem seus requisitos alterados -->

## Impact

- **Backend**: novos controllers (`UsuarioController`, `PerfilController`), service (`UsuarioService`), policies (`UsuarioPolicy`, `PerfilPolicy`), Form Requests, seeders e rotas em `routes/web.php`.
- **Frontend**: novas páginas Vue (`Usuarios/Index`, `Usuarios/Create`, `Usuarios/Edit`, `Perfis/Index`), componentes `BadgeStatus` para usuários, integração com Inertia.
- **Banco de dados**: sem novas migrations — os campos necessários já existem em `users` e as tabelas de roles/permissions do Spatie já foram criadas.
- **Seeds**: requer execução dos novos seeders para criar perfis e permissões completos em todos os módulos.

## Context

O projeto já possui:
- Spatie `laravel-permission` instalado com migrations de roles e permissions.
- Model `User` com `HasRoles`, campos de status e `deve_alterar_senha`.
- Seeders de permissões para imóveis, clientes e contratos seguindo o padrão `<modulo>.<acao>`.
- Padrão de controller Resource com Policy Gate + `$this->authorize()`.
- Padrão de service layer (`ImovelService`, `ClienteService`) para regras de negócio.
- `PrimeiroAcessoNotification` que envia senha temporária por email.

O que falta é a camada de gestão de usuários propriamente dita: controller, service, policy, seeders de usuários/perfis e páginas Vue.

## Goals / Non-Goals

**Goals:**
- CRUD de usuários internos com ativação/inativação e reenvio de acesso.
- Tela de listagem de perfis com suas permissões (somente leitura).
- Seeders completos dos quatro perfis padrão com matriz de permissões de todos os módulos.
- Permissões do módulo `usuarios` e `perfis` criadas e atribuídas ao admin.

**Non-Goals:**
- Edição granular de permissões por perfil via interface (MVP: somente leitura).
- Token-based first access (manter abordagem atual de senha temporária no MVP).
- Multiempresa / multitenant.
- Auditoria detalhada de mudanças de permissão.

## Decisions

### D1: Reutilizar `PrimeiroAcessoNotification` no reenvio de acesso
O reenvio de acesso inicial utilizará o mesmo `PrimeiroAcessoNotification` existente, apenas gerando uma nova senha temporária antes de notificar. Não há necessidade de criar uma notificação separada para o reenvio no MVP.

**Alternativa considerada**: criar `ReenvioAcessoNotification` — descartada por adicionar complexidade sem benefício real para o MVP.

### D2: UsuarioService encapsula toda lógica de negócio
Criar `app/Services/Usuarios/UsuarioService` com métodos: `criar()`, `atualizar()`, `ativar()`, `inativar()`, `reenviarAcesso()`. O controller deve ser fino e delegar ao service.

**Alternativa considerada**: colocar lógica diretamente no controller — descartada para manter consistência com o padrão do projeto.

### D3: Permissões do módulo usuarios seguem o padrão existente
Nomear as permissões como: `usuarios.viewAny`, `usuarios.view`, `usuarios.create`, `usuarios.update`, `usuarios.alterar-status`, `usuarios.reenviar-acesso`. Igual ao padrão de `imoveis.*` e `clientes.*`.

### D4: Seed de perfis usa `firstOrCreate` para idempotência
Todos os seeders usarão `Role::firstOrCreate()` e `Permission::firstOrCreate()` para serem seguros de executar múltiplas vezes sem duplicar dados.

### D5: Email não editável no MVP
Após criação, o email do usuário não pode ser alterado. O formulário de edição exibirá o email em modo somente leitura. Regra de negócio: evitar conflitos de acesso com tokens de sessão e notificações pendentes.

### D6: Página de Perfis é somente leitura no MVP
`PerfilController` terá apenas `index()` e `show()`. Não haverá criação, edição ou exclusão de perfis via interface no MVP.

## Risks / Trade-offs

- **[Risco] Permissões faltando em módulos futuros**: à medida que novos módulos forem adicionados, os seeders de cada perfil precisarão ser atualizados. → Mitigação: manter um seeder central `PerfisEPermissoesSeeder` que seja fácil de atualizar.
- **[Risco] Senha temporária enviada em texto no email**: a abordagem atual expõe a senha no email. → Mitigação: aceito para o MVP; roadmap futuro inclui migração para token-based first access.
- **[Trade-off] Email imutável**: impede correção de erros de digitação no email. → Aceito para o MVP; a alternativa seria um fluxo de troca de email com verificação, que está fora do escopo.

## Migration Plan

1. Criar seeders: `UsuarioPermissionsSeeder`, `PerfilPermissionsSeeder`, `PerfisEPermissoesSeeder`.
2. Adicionar seeders ao `DatabaseSeeder`.
3. Implementar backend: `UsuarioService`, `UsuarioController`, `PerfilController`, policies, requests, rotas.
4. Implementar frontend: páginas Vue e componentes necessários.
5. Executar `php artisan db:seed` (ou seeders específicos) no ambiente de desenvolvimento.

## Open Questions

- O admin deve poder editar seu próprio usuário? (Por ora: sim, mas não pode se inativar.)
- Haverá paginação na tela de perfis? (Por ora: não — são apenas 4 perfis.)

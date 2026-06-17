## Why

O sistema já possui autenticação funcional mas carece de um layout administrativo estruturado — o Dashboard atual é uma página isolada sem navegação, sidebar ou componentes de shell reutilizáveis. Sem essa estrutura, cada novo módulo (imóveis, contratos, clientes) precisará duplicar lógica de navegação e layout, tornando o crescimento inconsistente e custoso.

## What Changes

- Criação do `AdminLayout.vue` como layout Inertia para todas as páginas administrativas
- Sidebar responsivo: expandido em desktop, colapsável (drawer) em mobile
- `AppSidebar.vue`: navegação principal com grupos de menu, ícones e indicador de rota ativa
- `AppTopbar.vue`: barra superior com toggle do sidebar, nome do usuário e logout
- `AppBreadcrumb.vue`: breadcrumb dinâmico baseado na rota atual
- `SidebarNavItem.vue`: item de navegação atômico e reutilizável
- `SidebarNavGroup.vue`: agrupador de itens com título de seção
- Migração do `Dashboard.vue` para usar o novo `AdminLayout`
- Remoção da lógica inline de navbar/logout do `Dashboard.vue`

## Capabilities

### New Capabilities

- `admin-layout`: Layout shell administrativo com sidebar responsivo, topbar e slot de conteúdo para todas as páginas do painel

### Modified Capabilities

- *(nenhuma)*

## Impact

- `resources/js/Layouts/`: novo `AdminLayout.vue`
- `resources/js/Components/Admin/`: novos componentes `AppSidebar.vue`, `AppTopbar.vue`, `AppBreadcrumb.vue`, `SidebarNavItem.vue`, `SidebarNavGroup.vue`
- `resources/js/Pages/Admin/Dashboard.vue`: migrado para `AdminLayout`, lógica de navbar removida
- Sem impacto no backend, rotas PHP ou banco de dados

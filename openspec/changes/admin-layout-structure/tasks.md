## 1. Composables

- [x] 1.1 Criar `resources/js/composables/useSidebar.ts` com estado reativo `isOpen` e métodos `toggle`, `open`, `close`
- [x] 1.2 Criar `resources/js/composables/useNavigation.ts` com array `NAV_ITEMS` contendo grupos, labels, ícones Lucide e nomes de rota

## 2. Componentes atômicos do sidebar

- [x] 2.1 Criar `resources/js/Components/Admin/SidebarNavItem.vue` — recebe `label`, `icon`, `href`, `active` via props e renderiza link com estilo ativo condicional
- [x] 2.2 Criar `resources/js/Components/Admin/SidebarNavGroup.vue` — recebe `title` via prop e expõe slot default para os itens

## 3. Componentes de shell

- [x] 3.1 Criar `resources/js/Components/Admin/AppSidebar.vue` — logo ImobGestor no topo, usar as imagens disponiveis em public/assets/images, a logo.png para quando o drawer for isOpen e a logo1.png quando tiver fechado, lista de grupos/itens via `useNavigation`, detecta rota ativa via `usePage().url`
- [x] 3.2 Criar `resources/js/Components/Admin/AppTopbar.vue` — botão hamburguer (mobile), nome do usuário via `page.props.auth.user.name`, botão logout com POST para rota `logout`
- [x] 3.3 Criar `resources/js/Components/Admin/AppBreadcrumb.vue` — segmenta `usePage().url`, mapeia segmentos para labels em pt-BR, exibe "Início" como item raiz com link para rota `dashboard`

## 4. AdminLayout

- [x] 4.1 Criar `resources/js/Layouts/AdminLayout.vue` usando o drawer do DaisyUI (`drawer` + `drawer-mobile`), integrando `AppSidebar`, `AppTopbar`, `AppBreadcrumb` e `<slot />` para o conteúdo

## 5. Migração do Dashboard

- [x] 5.1 Refatorar `resources/js/Pages/Admin/Dashboard.vue` para usar `AdminLayout` via `defineOptions({ layout: AdminLayout })` e remover toda a lógica inline de navbar e logout

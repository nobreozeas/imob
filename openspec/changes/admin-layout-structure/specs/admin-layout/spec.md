## ADDED Requirements

### Requirement: Layout administrativo com sidebar responsivo
O sistema SHALL fornecer um `AdminLayout.vue` como layout Inertia que envolve todas as páginas do painel administrativo, contendo sidebar, topbar e área de conteúdo.

#### Scenario: Desktop — sidebar visível e conteúdo ao lado
- **WHEN** o usuário acessa qualquer página admin em viewport `lg` (≥1024px)
- **THEN** o sidebar SHALL estar sempre visível à esquerda, empurrando o conteúdo principal para a direita

#### Scenario: Mobile — sidebar como drawer
- **WHEN** o usuário acessa qualquer página admin em viewport menor que `lg`
- **THEN** o sidebar SHALL estar oculto por padrão com overlay ao ser aberto

#### Scenario: Toggle do sidebar em mobile
- **WHEN** o usuário clica no botão de menu na topbar em mobile
- **THEN** o drawer SHALL abrir ou fechar alternando o estado

### Requirement: Sidebar com navegação agrupada
O `AppSidebar.vue` SHALL renderizar itens de navegação organizados em grupos com título de seção, ícone e label para cada item.

#### Scenario: Rota ativa destacada
- **WHEN** a URL atual corresponde ao `href` de um item de navegação
- **THEN** esse item SHALL receber estilo visual de ativo (classe `active` do DaisyUI menu)

#### Scenario: Navegação para outra página
- **WHEN** o usuário clica em um item do sidebar
- **THEN** o Inertia SHALL navegar para a rota correspondente sem recarregar a página

#### Scenario: Sidebar exibe logo e nome do sistema
- **WHEN** o sidebar é renderizado
- **THEN** SHALL exibir o ícone `Building2` e o texto "ImobGestor" no topo

### Requirement: Topbar com informações do usuário
O `AppTopbar.vue` SHALL exibir o nome do usuário autenticado e um botão de logout.

#### Scenario: Exibição do nome do usuário
- **WHEN** o usuário está autenticado e acessa qualquer página admin
- **THEN** a topbar SHALL exibir o nome do usuário via `page.props.auth.user.name`

#### Scenario: Logout
- **WHEN** o usuário clica no botão "Sair"
- **THEN** SHALL realizar POST para a rota `logout` via Inertia router

#### Scenario: Botão de toggle apenas em mobile
- **WHEN** a viewport é menor que `lg`
- **THEN** a topbar SHALL exibir o botão hamburguer para abrir o drawer

### Requirement: Breadcrumb dinâmico
O `AppBreadcrumb.vue` SHALL exibir a hierarquia de navegação baseada na URL atual, com "Início" como item fixo inicial.

#### Scenario: Rota de primeiro nível
- **WHEN** o usuário está em `/admin/dashboard`
- **THEN** o breadcrumb SHALL exibir: Início > Dashboard

#### Scenario: Link na raiz
- **WHEN** o usuário clica em "Início" no breadcrumb
- **THEN** SHALL navegar para a rota `dashboard`

### Requirement: Componentes atômicos de navegação reutilizáveis
O sistema SHALL fornecer `SidebarNavItem.vue` e `SidebarNavGroup.vue` como componentes independentes e reutilizáveis.

#### Scenario: SidebarNavItem recebe props e renderiza link ativo
- **WHEN** `SidebarNavItem` recebe `label`, `icon`, `href` e `active: true`
- **THEN** SHALL renderizar com classe de ativo aplicada

#### Scenario: SidebarNavGroup renderiza título e lista de itens via slot
- **WHEN** `SidebarNavGroup` recebe `title` e itens via slot default
- **THEN** SHALL renderizar o título da seção acima dos itens

### Requirement: Dashboard migrado para AdminLayout
O `Dashboard.vue` SHALL usar `AdminLayout` como layout Inertia, removendo qualquer navbar ou lógica de logout inline.

#### Scenario: Dashboard sem duplicação de navbar
- **WHEN** o Dashboard é renderizado
- **THEN** NOT SHALL conter navbar, botão de logout ou lógica de autenticação própria — isso SHALL ser responsabilidade do AdminLayout

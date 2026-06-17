## Context

O ImobGestor usa Laravel + Inertia.js + Vue 3 + TailwindCSS/DaisyUI. Já existe um `AuthLayout.vue` como referência de componentização. O `Dashboard.vue` atual tem navbar e logout inline — é a única página admin existente. O objetivo é criar uma estrutura de shell reutilizável antes que novos módulos sejam adicionados.

## Goals / Non-Goals

**Goals:**
- Layout administrativo com sidebar responsivo (drawer em mobile, fixo em desktop)
- Componentização máxima: cada parte do shell é um componente independente
- Indicação visual de rota ativa no sidebar
- Topbar com toggle do sidebar, nome do usuário e botão de logout
- Breadcrumb dinâmico baseado na rota Inertia atual
- Dashboard migrado para usar o novo layout sem duplicação de código

**Non-Goals:**
- Implementação de módulos específicos (imóveis, contratos, clientes)
- Sistema de permissões ou roles no menu
- Notificações, busca global ou dark mode toggle
- Animações complexas além do transition padrão do DaisyUI drawer

## Decisions

### 1. DaisyUI Drawer para o sidebar mobile

**Decisão:** Usar o componente `drawer` do DaisyUI com `drawer-mobile` para o comportamento responsivo.

**Rationale:** Já usamos DaisyUI no projeto (AuthLayout, formulários). O drawer do DaisyUI gerencia todo o comportamento de overlay e z-index nativamente, sem JS adicional. Em desktop (`lg:`), o sidebar fica sempre visível e empurra o conteúdo; em mobile, vira um drawer com overlay.

**Alternativa considerada:** Implementar sidebar customizado com Headless UI — rejeitado por adicionar dependência e complexidade desnecessárias quando o DaisyUI já oferece a primitiva.

### 2. Estado do sidebar via composable `useSidebar`

**Decisão:** Criar `composables/useSidebar.ts` com estado reativo compartilhado (`isOpen`) entre `AppTopbar` e `AppSidebar`.

**Rationale:** Evita prop drilling entre layout → topbar → sidebar. O composable é simples (toggle/open/close) e não precisa de Pinia para esse escopo.

**Alternativa considerada:** Pinia store — desnecessário para estado tão local e efêmero.

### 3. Navegação declarativa via constante

**Decisão:** Definir o array de navegação (`NAV_ITEMS`) em `composables/useNavigation.ts`, com grupos, ícones Lucide e nomes de rota.

**Rationale:** Centraliza o menu num lugar só, facilitando adição de módulos futuros sem alterar os componentes de UI. `SidebarNavGroup` e `SidebarNavItem` apenas renderizam o que recebem via props.

### 4. Breadcrumb baseado em `usePage().url`

**Decisão:** `AppBreadcrumb` resolve os segmentos da URL atual e mapeia para labels legíveis em português via um mapa estático.

**Rationale:** Inertia não expõe hierarquia de rotas por padrão. Um mapa simples `{ '/admin/dashboard': 'Dashboard' }` cobre 100% dos casos iniciais sem biblioteca extra.

## Risks / Trade-offs

- **[Risco] Drawer DaisyUI usa checkbox hidden para controle** → O estado do checkbox deve ser sincronizado com o composable `useSidebar` para evitar dessincronização. Mitigação: bind `:checked` no input e controlar via `v-model`.
- **[Trade-off] Mapa de breadcrumb estático** → Exige atualização manual ao adicionar rotas. Aceitável no curto prazo; pode ser substituído por meta de rota quando necessário.
- **[Risco] Migração do Dashboard.vue** → A remoção da navbar inline pode causar regressão visual se o novo layout não cobrir os mesmos elementos. Mitigação: testar a página de Dashboard após a migração.

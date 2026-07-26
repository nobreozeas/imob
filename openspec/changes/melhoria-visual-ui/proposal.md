## Why

O sistema hoje usa os componentes padrão do DaisyUI de forma bem literal (steps genéricos, navbar simples só com breadcrumb e botão de sair, cards sem hierarquia visual clara), o que passa uma impressão de protótipo, não de produto profissional. O usuário forneceu referências visuais (`docs/design-ui/`) de telas de login e do wizard de cadastro de imóvel/contrato com um padrão consistente — layout organizado, hierarquia tipográfica clara, indicador de etapas customizado, painéis de resumo, avatar/notificações no topo — que deve guiar o refinamento visual do sistema.

## What Changes

- Ajustar a tela de login (`AuthLayout`/`Login.vue`) para usar inputs com ícone, botão primário com ícone, divisor "ou" e ação secundária, card com elevação — o painel de marketing à esquerda já está próximo do modelo e não muda de estrutura.
- Ajustar a barra superior (`AppTopbar`) para incluir sino de notificações (com badge), ícone de ajuda e bloco de usuário (avatar com iniciais, nome, papel, chevron) — hoje só existe nome + botão sair.
- Ajustar o destaque do item ativo do menu lateral (`SidebarNavItem`) para o padrão claro (fundo azul suave + texto azul + indicador lateral) da referência.
- Criar um componente de indicador de etapas compartilhado (`StepWizard`/stepper com círculos numerados, check verde nas etapas concluídas, círculo azul na etapa atual, linhas conectoras) para substituir o `steps` genérico do DaisyUI usado nos wizards de Imóveis e Contratos.
- Aplicar o novo padrão visual de painéis nas etapas dos wizards de Imóveis (Endereço, Valores/Dados Comerciais, Fotos e Documentos, Revisão) e Contratos (Valores, com painel lateral "Resumo Financeiro" e "Ações"), incluindo: cards com título + subtítulo, grade de campos responsiva, painel de dicas com lista de checks, dropzone de arquivos com preview em grade e miniaturas removíveis, lista de documentos com ícone por tipo de arquivo, painel de resumo com totais destacados por cor, e tela de revisão em grade de cards com badges de status.

## Capabilities

### New Capabilities
- `design-visual-shell`: aparência da barra superior e do item ativo do menu lateral (avatar/papel do usuário, notificações, ajuda, destaque do item ativo).
- `design-auth-screen`: aparência da tela de login (inputs com ícone, ações primária/secundária, card).
- `design-wizard-stepper`: componente compartilhado de indicador de etapas usado pelos wizards.
- `design-wizard-panels`: convenções visuais de cards/painéis usadas dentro das etapas dos wizards (dicas, resumo financeiro/valores, dropzone de arquivos, revisão em grade).

### Modified Capabilities
(nenhuma — são ajustes visuais sobre telas já existentes, sem specs formais arquivadas para essas telas)

## Impact

- **Frontend apenas**: `resources/js/Layouts/AuthLayout.vue`, `resources/js/Pages/Auth/Login.vue`, `resources/js/Components/Admin/AppTopbar.vue`, `resources/js/Components/Admin/SidebarNavItem.vue`, novo `resources/js/Components/Admin/StepWizard.vue` (ou nome similar), e os componentes de step do wizard de Imóveis (`WizardImovel.vue` e seus `WizardStepN*.vue`) e de Contratos (`WizardContrato.vue` e seus `WizardStepN*.vue`).
- Nenhuma mudança de backend, rotas, banco de dados ou regra de negócio.
- Sem novas dependências de npm — usa DaisyUI onde encaixa e Tailwind puro (utilitários) onde o DaisyUI não tiver um componente equivalente (ex.: o stepper customizado, o avatar circular com iniciais, os painéis de resumo com cores de destaque).
- Escopo desta mudança: tela de login, casca do layout autenticado (topbar/sidebar) e os dois wizards já existentes (Imóveis e Contratos), que são exatamente o que as referências mostram. Telas de listagem/detalhe dos demais módulos (Clientes, Financeiro, Usuários etc.) não são tocadas nesta mudança — ficam para uma extensão futura do mesmo padrão visual, depois que os componentes-base estiverem validados aqui.

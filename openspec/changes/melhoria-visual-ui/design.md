## Context

O frontend já usa Vue 3 + Inertia + Tailwind CSS 4 + DaisyUI + `lucide-vue-next` para ícones (nenhuma dependência nova é necessária). A estrutura de navegação (`AppSidebar.vue` com `SidebarNavGroup`/`SidebarNavItem`) já segue o agrupamento em seções (CADASTROS, FINANCEIRO, ADMINISTRAÇÃO) visto nas referências — o gap ali é só de estilo do item ativo, não de estrutura. Os maiores gaps estão em: `AppTopbar.vue` (hoje só tem breadcrumb + nome + botão sair, sem notificações/ajuda/avatar), `Login.vue` (inputs sem ícone, sem ação secundária, sem card elevado) e os steps dos wizards de Imóveis/Contratos, que usam o componente `steps` genérico do DaisyUI e cards simples sem hierarquia visual (título/subtítulo, painéis de apoio, dropzone com preview, resumo com totais destacados).

As imagens de referência (`docs/design-ui/*.png`) mostram, além de estilo, algumas funcionalidades que não existem hoje: busca de CEP automática, mapa interativo de localização, e um toggle Locação/Venda no step de valores. Essas são features de produto, não de estilo — tratadas como não-objetivo nesta mudança (ver abaixo).

## Goals / Non-Goals

**Goals:**
- Elevar a percepção de acabamento do sistema replicando o padrão visual das referências: hierarquia tipográfica clara (título + subtítulo em cada card), espaçamento consistente, indicador de etapas customizado, painéis de apoio (dicas, resumo), badges de status coloridos, dropzone de arquivo com miniaturas.
- Fazer isso com DaisyUI sempre que houver um componente equivalente; usar Tailwind puro (utilitários) apenas onde o DaisyUI não cobrir (stepper customizado com círculos/checks/linhas conectoras, avatar circular com iniciais, painel de resumo com totais coloridos, dropzone com grade de miniaturas).
- Criar um único componente de stepper reutilizável pelos dois wizards existentes (Imóveis e Contratos), em vez de duplicar o markup em cada um.
- Manter 100% do comportamento e das regras de negócio atuais — é uma mudança de apresentação, os dados e submits continuam os mesmos.
- Adicionar uma 6ª etapa "Revisão" ao wizard de Imóveis (hoje ele tem 5 etapas e o botão final de "Fotos e Documentos" já dispara o submit) — o wizard de Contratos já tem uma etapa de revisão (`WizardStep9Revisao.vue`), então essa etapa só recebe o retoque visual. Para Imóveis, a etapa de "Fotos e Documentos" passa a emitir `next` em vez de `submit`, e a nova etapa de Revisão exibe os dados agrupados (como em `imovel-resumo.png`) e é quem de fato dispara o `submit` já existente — o endpoint e o payload de `imoveis.store` não mudam.

**Non-Goals:**
- Não implementar busca automática de CEP (integração com API dos Correios/ViaCEP) nem mapa interativo (Google Maps/Leaflet) no step de Endereço — são features novas com dependências externas, fora do pedido ("layout ajustado e organizado"). O step de Endereço ganha o layout de duas colunas (formulário + painel de dicas) da referência, mas sem o mapa; o painel de dicas substitui o espaço do mapa com orientações estáticas de preenchimento.
- Não adicionar um toggle Locação/Venda com novo estado — a finalidade do imóvel já é escolhida na etapa 1 (Dados Principais) e não muda dentro da etapa de Valores. A etapa de Dados Comerciais reorganiza os campos existentes em blocos "Locação"/"Venda" lado a lado (como a referência), mas o bloco correspondente à finalidade não escolhida aparece esmaecido/desabilitado usando o dado que já existe, sem inventar comportamento novo.
- Não redesenha telas de listagem/detalhe fora dos dois wizards e da casca (Clientes, Financeiro, Usuários, Perfis, Relatórios) — fica para uma mudança futura, depois que os componentes-base (stepper, painéis, avatar/topbar) estiverem validados aqui.
- Não muda paleta de cores do tema DaisyUI (`daisy.config`/tema atual) além de usar os tons semânticos já existentes (`primary`, `success`, `error`, `warning`, `base-100/200/300`) — não é uma repaginação de marca.
- Sino de notificações: exibe contagem estática (badge) sem um sistema de notificações real por trás — não existe módulo de notificações implementado ainda (PRD seção 26, fora de escopo). O sino fica visualmente pronto para quando esse módulo existir; por ora pode abrir um dropdown vazio com estado "Nenhuma notificação" em vez de dado fictício.

## Decisions

### 1. Stepper compartilhado como novo componente `resources/js/Components/Admin/StepWizard.vue`
Substitui o `<ul class="steps">` do DaisyUI usado hoje em `WizardImovel.vue` e o indicador próprio de `WizardContrato.vue`. Recebe `etapas: { label: string }[]` e `etapaAtual: number`, emite `@ir-para(n)` quando uma etapa concluída é clicada. Renderiza círculo numerado (borda cinza) para etapas futuras, círculo azul preenchido para a atual, círculo verde com check para as concluídas, e uma linha conectora entre eles — tudo com Tailwind puro (o `steps` do DaisyUI não suporta esse visual sem sobrescrever quase todo o CSS dele, então não vale a pena partir dele). Alternativa descartada: estender o `steps` do DaisyUI via classes utilitárias — descartada porque o componente do DaisyUI usa `counter-increment`/pseudo-elementos que dificultam customizar cor por estado (concluído/atual/futuro) de forma limpa.

### 2. Painéis de apoio como convenção de classes, não um novo componente genérico
"Dicas", "Resumo dos valores", "Resumo Financeiro" e os cards da tela de Revisão são visualmente parecidos (card com header + lista de linhas rótulo/valor) mas têm conteúdo e contexto diferentes o suficiente (uma é lista de dicas com ícone de check, outra é par rótulo/valor com destaque de total) que criar um componente genérico agora seria abstração prematura. Cada wizard implementa o painel localmente reutilizando as mesmas classes utilitárias (`card bg-base-100 border border-base-200 rounded-lg`, `text-sm font-semibold`, `text-base-content/60`), documentadas como convenção no design mas não extraídas em componente.

### 3. Avatar do usuário com iniciais via Tailwind puro
DaisyUI tem um componente `avatar`, mas para as iniciais (ex.: "AD", "JS") sem depender de foto de perfil (que não existe no sistema hoje), a implementação mais simples é uma `div` circular com Tailwind (`w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm font-semibold`) calculando as iniciais a partir de `user.name` no próprio `AppTopbar.vue`.

### 4. Sino de notificações e ajuda como elementos visuais "prontos para o futuro"
Adicionados em `AppTopbar.vue` como botões com badge (contagem 0 por padrão) e dropdown (DaisyUI `dropdown`) com estado vazio — não criam lógica de backend nem tabela de notificações. Ícone de ajuda abre um link estático (ex.: para documentação) ou um dropdown simples "Central de ajuda em breve".

### 5. Dropzone de arquivos com preview em grade
`GerenciadorFotosImovel.vue`/`GerenciadorDocumentosImovel.vue` (Imóveis) e o equivalente em Contratos já implementam upload — o ajuste é puramente visual: área tracejada maior com ícone central, texto "Arraste e solte" + botão "Selecionar arquivos", grade de miniaturas 4 colunas com botão "x" no canto para remover, e lista de documentos com ícone colorido por extensão (PDF vermelho, imagem azul) + nome + tamanho + ícone de lixeira. Nenhuma mudança na lógica de upload/remoção já existente.

## Risks / Trade-offs

- [Aplicar um visual novo em 2 wizards com várias etapas cada é um volume grande de arquivos tocados só nesta mudança] → mitigado dividindo em tasks por etapa/arquivo, permitindo revisar e mesclar incrementalmente; nenhuma etapa depende de outra estar pronta (são visualmente independentes).
- [Esmaecer o bloco Locação ou Venda na etapa de Dados Comerciais pode confundir se o usuário quiser ver os dois valores ao mesmo tempo, ex. imóvel que aceita venda e locação] → manter os campos do bloco não correspondente à finalidade visíveis mas com opacidade reduzida e sem obrigatoriedade, não escondidos — o dado continua editável se o usuário realmente precisar (`finalidade` pode ser "ambos" no schema atual; nesse caso nenhum bloco fica esmaecido).
- [Novo stepper customizado pode não ficar acessível (leitores de tela, navegação por teclado) tão bem quanto o `steps` nativo do DaisyUI] → manter marcação semântica (`<button>`/`<ol>`/`aria-current="step"`) no novo componente em vez de `<div>`s soltas.

## Migration Plan

Mudança puramente de frontend, sem dado ou schema envolvido — não há passos de migração de dados. Ordem sugerida de implementação (cada item é independente e pode ser revisado isoladamente):
1. `StepWizard.vue` (componente compartilhado) — sem consumidores ainda, mais fácil de revisar isolado.
2. `AppTopbar.vue` + `SidebarNavItem.vue` (casca) — afeta todas as páginas autenticadas, então vale validar cedo.
3. `AuthLayout.vue`/`Login.vue` — independente do resto.
4. Wizard de Imóveis: trocar o stepper, depois ajustar cada etapa (Endereço, Dados Comerciais, Fotos e Documentos, ficando a Revisão — se existir — por último).
5. Wizard de Contratos: trocar o stepper, depois a etapa de Valores com o painel "Resumo Financeiro"/"Ações".

## Open Questions

- O ícone de ajuda deve linkar para alguma documentação/central de ajuda real, ou basta um placeholder "em breve"? Assumido como placeholder nesta mudança; ajustar quando houver conteúdo de ajuda de fato.

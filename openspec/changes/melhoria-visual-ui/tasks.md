## 1. Componente de stepper compartilhado

- [ ] 1.1 Criar `resources/js/Components/Admin/StepWizard.vue`: props `etapas: { label: string }[]`, `etapaAtual: number`; emite `ir-para(n)`; renderiza círculo numerado com 3 estados (futuro: contorno neutro; atual: preenchido `primary`; concluído: preenchido verde com ícone de check do `lucide-vue-next`) conectados por uma linha horizontal; marcação semântica (`<ol>`, `<button>`, `aria-current="step"` na etapa atual); clique só funciona em etapas concluídas (`n < etapaAtual`)
- [ ] 1.2 Substituir o `<ul class="steps">` em `resources/js/Components/Imoveis/WizardImovel.vue` pelo novo `StepWizard`
- [ ] 1.3 Substituir o indicador de etapas atual em `resources/js/Components/Contratos/WizardContrato.vue` pelo novo `StepWizard`

## 2. Casca do layout autenticado

- [ ] 2.1 Atualizar `resources/js/Components/Admin/AppTopbar.vue`: adicionar bloco de usuário (avatar circular com iniciais calculadas a partir de `user.name`, nome, papel/role do usuário, ícone chevron), sino de notificações com badge (dropdown DaisyUI com estado vazio "Nenhuma notificação"), ícone de ajuda (dropdown ou link estático); manter a ação de logout já existente (pode ficar dentro do dropdown do usuário)
- [ ] 2.2 Atualizar `resources/js/Components/Admin/SidebarNavItem.vue`: destaque do item ativo com fundo em tom suave da cor primária (`bg-primary/10`), texto `text-primary`, indicador lateral (barra ou borda esquerda colorida)

## 3. Tela de login

- [ ] 3.1 Atualizar `resources/js/Pages/Auth/Login.vue`: adicionar ícone (envelope) dentro do campo de e-mail e usar o ícone de cadeado já disponível em `InputSenha.vue` (ou adicioná-lo se ainda não existir); manter o alternador de mostrar/ocultar senha já implementado
- [ ] 3.2 Ajustar o botão de submissão para largura total, destaque visual (cor primária sólida) e ícone, mantendo o estado de carregamento já existente
- [ ] 3.3 Envolver o formulário em um card com fundo `base-100`, `rounded-xl`, sombra (`shadow-lg`/`shadow-xl`) dentro do painel direito de `AuthLayout.vue`

## 4. Wizard de Imóveis — etapas existentes

- [ ] 4.1 `WizardStep2Endereco.vue`: adicionar título + subtítulo no topo do card; reorganizar em duas colunas (formulário à esquerda, painel "Dicas" com lista de checks à direita, sem mapa nem busca de CEP)
- [ ] 4.2 `WizardStep4DadosComerciais.vue`: agrupar os campos em dois blocos visuais "Locação" e "Venda"; esmaecer (opacidade reduzida, mas editável) o bloco que não corresponde à `finalidade` escolhida na etapa 1, quando a finalidade for exclusivamente locação ou exclusivamente venda
- [ ] 4.3 `WizardStep5FotosDocumentos.vue` / `GerenciadorFotosImovel.vue` / `GerenciadorDocumentosImovel.vue`: ajustar visual da dropzone (ícone central maior, texto instrutivo, botão "Selecionar arquivos") e da grade de miniaturas (fotos) e lista de documentos (ícone colorido por extensão, nome, tamanho, ação de remover); sem mudanças na lógica de upload
- [ ] 4.4 `WizardStep1DadosPrincipais.vue` e `WizardStep3Caracteristicas.vue`: aplicar o mesmo padrão de título + subtítulo no topo do card e espaçamento consistente com as demais etapas (sem mudança de campos)

## 5. Wizard de Imóveis — nova etapa de Revisão

- [ ] 5.1 Criar `resources/js/Components/Imoveis/WizardStep6Revisao.vue`: exibe os dados preenchidos agrupados em cards (Dados Principais, Endereço, Valores), badge de status, miniaturas das fotos anexadas (com "+N fotos" se houver mais que o exibido) e lista de documentos anexados; botões "Anterior" e "Finalizar cadastro" (este último dispara o `submit`)
- [ ] 5.2 Atualizar `WizardImovel.vue`: adicionar a 6ª etapa "Revisão" à lista `ETAPAS`; `WizardStep5FotosDocumentos.vue` passa a emitir `next` (não mais `submit`); o `submit` final agora vem de `WizardStep6Revisao`
- [ ] 5.3 Conferir que `imoveis.store` continua recebendo exatamente o mesmo payload de antes (nenhuma mudança de campo, só a etapa visual extra antes do envio)

## 6. Wizard de Contratos — retoque visual

- [ ] 6.1 Aplicar título + subtítulo no topo de cada etapa (`WizardStep1ImovelPartes.vue` até `WizardStep8Documentos.vue`) seguindo o mesmo padrão do wizard de Imóveis
- [ ] 6.2 `WizardStep3Valores.vue`: adicionar painel lateral "Resumo Financeiro" com os valores informados (aluguel, condomínio, IPTU) e destaque colorido para o total mensal previsto e o valor de repasse ao proprietário calculado (reaproveitando os valores/fórmulas já calculados no form, sem nova lógica de cálculo)
- [ ] 6.3 `WizardStep9Revisao.vue`: atualizar os cards de `bg-base-200` para o padrão visual dos demais cards (`bg-base-100 border border-base-200 rounded-lg`, título + subtítulo), mantendo os mesmos dados exibidos

## 7. Verificação final

- [ ] 7.1 Rodar `npx vue-tsc --noEmit` e confirmar que nenhum arquivo tocado nesta mudança introduz erro de tipo novo (fora do padrão pré-existente de `route()` já documentado em mudanças anteriores)
- [ ] 7.2 Percorrer manualmente (via `/run` ou navegador) o fluxo de login, o cadastro completo de um imóvel (incluindo a nova etapa de Revisão) e o cadastro completo de um contrato, confirmando que os dados são salvos corretamente e nenhum comportamento funcional mudou
- [ ] 7.3 Conferir responsividade (mobile/tablet) das telas alteradas — painéis laterais (dicas, resumo) devem empilhar abaixo do formulário em telas estreitas

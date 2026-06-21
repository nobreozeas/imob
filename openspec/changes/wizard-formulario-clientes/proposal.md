## Por que

O formulário de cadastro e edição de clientes concentra muitos campos em uma única tela longa, o que pode confundir usuários e aumentar a taxa de erro durante o preenchimento. Um step wizard organiza os campos em etapas sequenciais, reduz a carga cognitiva e oferece feedback de progresso.

## O que muda

- **BREAKING**: `FormularioCliente.vue` é substituído por uma experiência de step wizard com etapas independentes.
- Criação do componente `WizardCliente.vue` — orquestra as etapas e a barra de progresso.
- Criação de componentes por etapa: `WizardStep1DadosPrincipais.vue`, `WizardStep2Papeis.vue`, `WizardStep3Contatos.vue`, `WizardStep4Endereco.vue`, `WizardStep5DadosAdicionais.vue`.
- As páginas `Create.vue` e `Edit.vue` passam a usar o `WizardCliente` em vez do `FormularioCliente`.
- Validação por etapa antes de avançar: o usuário não avança para a próxima etapa se a atual tiver erros obrigatórios.
- Exibição de resumo na última etapa antes do envio.
- Possibilidade de navegar para etapas anteriores sem perder dados.
- O componente `FormularioCliente.vue` existente é removido (sem mais uso).

## Capacidades

### Novas Capacidades

- `wizard-formulario-clientes`: Step wizard multi-etapa para cadastro e edição de clientes, com validação por etapa, barra de progresso e resumo antes do envio.

### Capacidades Modificadas

- `gestao-de-clientes`: O fluxo de cadastro e edição de cliente passa a usar wizard em vez de formulário longo em página única. Os requisitos de campos e validações permanecem iguais; apenas a experiência de entrada muda.

## Impacto

- **Frontend**: Remoção de `FormularioCliente.vue`; criação de `WizardCliente.vue` e 5 componentes de etapa; atualização de `Create.vue` e `Edit.vue`.
- **Backend**: Sem alteração — controller, requests e service permanecem intactos.
- **Dependências**: Sem novas dependências; usa Vue 3, DaisyUI e Tailwind CSS 4 já disponíveis.

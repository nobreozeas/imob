## ADDED Requirements

### Requirement: Formulário de login com inputs identificados por ícone
Os campos de e-mail e senha da tela de login SHALL exibir um ícone indicativo (envelope para e-mail, cadeado para senha) dentro do campo, e o campo de senha SHALL manter o alternador de visibilidade (mostrar/ocultar) já existente.

#### Scenario: Campos exibem ícone correspondente
- **WHEN** a tela de login é carregada
- **THEN** o campo de e-mail exibe um ícone de envelope e o campo de senha exibe um ícone de cadeado

### Requirement: Ação primária de login com destaque visual
O botão de submissão do login SHALL ser o elemento de maior destaque visual do formulário (largura total, cor primária sólida, ícone), mantendo o estado de carregamento já existente durante o envio.

#### Scenario: Botão exibe estado de carregamento
- **WHEN** o formulário de login é submetido
- **THEN** o botão de entrar exibe um indicador de carregamento e permanece desabilitado até a resposta do servidor

### Requirement: Formulário de login apresentado em card com elevação
O formulário de login SHALL ser apresentado dentro de um card com fundo branco/base, bordas arredondadas e sombra sutil, distinguindo-se do restante do painel de fundo.

#### Scenario: Card visualmente destacado do fundo
- **WHEN** a tela de login é carregada em uma viewport desktop
- **THEN** o formulário aparece dentro de um card com sombra e cantos arredondados, separado visualmente da lateral de marketing

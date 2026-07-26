## ADDED Requirements

### Requirement: Componente de indicador de etapas compartilhado
O sistema SHALL possuir um componente de indicador de etapas (stepper) reutilizável, usado por todo wizard multi-etapas, que exibe um círculo numerado por etapa conectado por uma linha, com três estados visuais distintos: etapa futura (contorno neutro com número), etapa atual (preenchida na cor primária) e etapa concluída (preenchida em verde com ícone de check).

#### Scenario: Etapa concluída exibe check verde
- **WHEN** o usuário avança da etapa 1 para a etapa 2 de um wizard
- **THEN** o círculo da etapa 1 passa a exibir um ícone de check sobre fundo verde

#### Scenario: Etapa atual destacada
- **WHEN** o usuário está na etapa 3 de um wizard de 5 etapas
- **THEN** o círculo da etapa 3 aparece preenchido na cor primária e as etapas 4 e 5 aparecem com contorno neutro

### Requirement: Navegação para etapas já concluídas
O stepper SHALL permitir que o usuário clique em uma etapa já concluída para retornar a ela, mas SHALL impedir o clique em etapas futuras ainda não alcançadas.

#### Scenario: Clique em etapa concluída retorna a ela
- **WHEN** o usuário está na etapa 3 e clica no círculo da etapa 1 (já concluída)
- **THEN** o wizard exibe o conteúdo da etapa 1

#### Scenario: Clique em etapa futura é ignorado
- **WHEN** o usuário está na etapa 2 e clica no círculo da etapa 5 (ainda não alcançada)
- **THEN** o wizard permanece na etapa 2

### Requirement: Stepper reutilizado pelos wizards existentes
Os wizards de cadastro de Imóveis e de Contratos de Locação SHALL usar o mesmo componente de stepper, com os rótulos de etapa específicos de cada um.

#### Scenario: Wizard de imóveis usa o stepper compartilhado
- **WHEN** o usuário acessa o cadastro de um novo imóvel
- **THEN** as 5 etapas do cadastro são exibidas usando o componente de stepper compartilhado

#### Scenario: Wizard de contratos usa o stepper compartilhado
- **WHEN** o usuário acessa o cadastro de um novo contrato de locação
- **THEN** as etapas do cadastro são exibidas usando o mesmo componente de stepper compartilhado

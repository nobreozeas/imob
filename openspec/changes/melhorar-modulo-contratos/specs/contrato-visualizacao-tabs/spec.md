## ADDED Requirements

### Requirement: Detalhes do contrato organizados em abas
A tela de detalhes do contrato SHALL organizar suas informações em abas: Resumo, Parcelas, Encargos, Caução, Repasses, Documentos e Histórico, exibindo em cada uma exatamente os dados já disponíveis para o contrato (sem novos campos).

#### Scenario: Aba Resumo é exibida por padrão
- **WHEN** o usuário acessa os detalhes de um contrato sem especificar uma aba
- **THEN** a aba "Resumo" é exibida, contendo os dados gerais, o resumo financeiro e as informações de vigência do contrato

#### Scenario: Cada aba mostra sua seção correspondente
- **WHEN** o usuário clica na aba "Parcelas"
- **THEN** a tabela de parcelas do contrato é exibida, e o conteúdo das demais abas fica oculto

### Requirement: Aba selecionável por parâmetro de URL
A tela de detalhes do contrato SHALL permitir selecionar a aba ativa por meio de um parâmetro de URL, permitindo que outras telas do sistema linkem diretamente para uma aba específica.

#### Scenario: Link direto para a aba de repasses
- **WHEN** o usuário acessa a URL de um contrato com o parâmetro indicando a aba "Repasses"
- **THEN** a tela abre já com a aba "Repasses" selecionada

#### Scenario: Trocar de aba atualiza a URL
- **WHEN** o usuário clica em uma aba diferente da atual
- **THEN** a URL é atualizada para refletir a aba selecionada, sem recarregar a página

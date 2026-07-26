## ADDED Requirements

### Requirement: Cada etapa do wizard tem título e subtítulo explicativo
Toda etapa de um wizard SHALL exibir um título curto e uma frase de apoio explicando o propósito daquela etapa, no topo do card de conteúdo.

#### Scenario: Etapa de endereço exibe título e subtítulo
- **WHEN** o usuário está na etapa de Endereço do cadastro de imóvel
- **THEN** o card exibe o título "Endereço do imóvel" e o subtítulo "Informe o endereço completo do imóvel."

### Requirement: Etapa de Endereço exibe painel de dicas de preenchimento
A etapa de Endereço do wizard de imóveis SHALL exibir, ao lado do formulário, um painel com dicas de preenchimento em formato de lista com ícone de confirmação.

#### Scenario: Painel de dicas visível na etapa de endereço
- **WHEN** o usuário está na etapa de Endereço
- **THEN** um painel "Dicas" com pelo menos uma orientação de preenchimento é exibido ao lado do formulário

### Requirement: Etapa de valores comerciais separa campos de Locação e Venda
A etapa de valores comerciais do wizard de imóveis SHALL apresentar os campos de locação (aluguel, condomínio, IPTU) e os campos de venda em blocos visualmente separados; o bloco que não corresponde à finalidade escolhida na etapa de Dados Principais SHALL aparecer com opacidade reduzida quando a finalidade for exclusivamente locação ou exclusivamente venda.

#### Scenario: Imóvel de locação esmaece o bloco de venda
- **WHEN** o imóvel foi cadastrado com finalidade "locação" na etapa de Dados Principais
- **THEN** o bloco de campos de venda aparece com opacidade reduzida na etapa de valores comerciais, mas continua editável

#### Scenario: Imóvel para locação e venda mantém ambos os blocos com destaque igual
- **WHEN** o imóvel foi cadastrado com finalidade "locação e venda"
- **THEN** nenhum dos dois blocos aparece esmaecido

### Requirement: Upload de arquivos exibe dropzone com prévia em grade
As etapas de upload de fotos e documentos SHALL exibir uma área de arraste-e-solte com ícone central, texto instrutivo e botão alternativo de seleção de arquivo; arquivos já anexados SHALL ser exibidos em miniaturas (fotos) ou em lista com ícone por tipo de arquivo e opção de remoção (documentos).

#### Scenario: Fotos anexadas aparecem em grade de miniaturas
- **WHEN** o usuário anexa 3 fotos ao imóvel
- **THEN** as 3 fotos aparecem como miniaturas em grade, cada uma com um botão de remoção

#### Scenario: Documento PDF exibe ícone correspondente
- **WHEN** o usuário anexa um arquivo PDF
- **THEN** o item da lista de documentos exibe um ícone identificando o tipo PDF, o nome do arquivo e o tamanho

### Requirement: Etapa de revisão apresenta resumo em cards agrupados
A etapa final de revisão de um wizard SHALL apresentar os dados preenchidos organizados em cards agrupados por seção (ex.: dados principais, endereço, valores), cada um com pares rótulo/valor, incluindo um badge de status quando aplicável.

#### Scenario: Revisão do cadastro de imóvel agrupa por seção
- **WHEN** o usuário chega à etapa de revisão do cadastro de imóvel
- **THEN** os dados aparecem organizados em cards separados para dados principais, endereço e valores, cada um com seus respectivos campos

### Requirement: Painel de resumo financeiro destaca o total calculado
Etapas de wizard que envolvem valores financeiros SHALL exibir um painel lateral de resumo com os valores informados e o total calculado destacado visualmente (cor diferenciada) em relação aos demais itens do resumo.

#### Scenario: Resumo financeiro do contrato destaca total e repasse
- **WHEN** o usuário preenche os valores do contrato de locação (aluguel, condomínio, IPTU, taxa de administração)
- **THEN** o painel de resumo financeiro exibe cada valor informado e destaca o total mensal previsto e o valor de repasse ao proprietário com cores distintas dos demais itens

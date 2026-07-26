## ADDED Requirements

### Requirement: Geração de visualização imprimível do contrato
O sistema SHALL disponibilizar uma visualização imprimível do contrato de locação, gerada a partir dos dados já cadastrados (partes, imóvel, vigência, valores, encargos, multas e caução), acessível a partir da tela de detalhes do contrato.

#### Scenario: Botão de impressão disponível nos detalhes do contrato
- **WHEN** o usuário com permissão de visualizar o contrato acessa seus detalhes
- **THEN** um botão "Imprimir contrato" está disponível e abre a visualização imprimível em uma nova aba

#### Scenario: Visualização imprimível não exibe o menu do sistema
- **WHEN** a visualização imprimível do contrato é aberta
- **THEN** ela é exibida sem a barra lateral e sem a barra superior do sistema, apenas o conteúdo do contrato formatado para impressão

### Requirement: Conteúdo do contrato reflete os dados cadastrados
A visualização imprimível SHALL incluir, quando aplicável: dados das partes (locador, locatário e corretor), endereço do imóvel, vigência do contrato, valor do aluguel e regra de reajuste, encargos e seus responsáveis, regras de multa por atraso e por rescisão antecipada, dados da caução/garantia, e um espaço de assinaturas. Seções referentes a dados não configurados no contrato (ex.: sem caução, sem corretor) SHALL ser omitidas.

#### Scenario: Contrato sem caução omite a cláusula de garantia
- **WHEN** o contrato não possui caução configurada
- **THEN** a visualização imprimível não exibe a seção de caução/garantia

#### Scenario: Contrato com corretor exibe seus dados
- **WHEN** o contrato tem um corretor responsável vinculado
- **THEN** a visualização imprimível inclui o nome do corretor na seção de partes

### Requirement: Acesso à impressão exige permissão de visualização do contrato
O sistema SHALL exigir a mesma permissão de visualização do contrato para acessar sua visualização imprimível.

#### Scenario: Usuário sem permissão de visualizar contratos não acessa a impressão
- **WHEN** um usuário sem a permissão de visualizar contratos tenta acessar a URL de impressão de um contrato
- **THEN** o acesso é negado

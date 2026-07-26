## ADDED Requirements

### Requirement: Barra superior exibe identidade do usuário logado
A barra superior (`AppTopbar`) SHALL exibir um avatar circular com as iniciais do nome do usuário logado, o nome completo e o papel/perfil do usuário, com um indicador (chevron) de que o bloco é clicável.

#### Scenario: Iniciais calculadas a partir do nome
- **WHEN** o usuário logado se chama "João Silva"
- **THEN** o avatar exibe as iniciais "JS"

#### Scenario: Nome e papel exibidos
- **WHEN** a barra superior é renderizada para um usuário administrador chamado "Ana Souza"
- **THEN** o bloco de usuário exibe "Ana Souza" e o papel do usuário abaixo do nome

### Requirement: Barra superior exibe atalhos de notificações e ajuda
A barra superior SHALL exibir um ícone de notificações com contador (badge) e um ícone de ajuda, ambos com um menu/dropdown ao serem clicados.

#### Scenario: Nenhuma notificação pendente
- **WHEN** não há notificações registradas para o usuário
- **THEN** o ícone de notificações não exibe badge, e o dropdown mostra um estado vazio ("Nenhuma notificação")

### Requirement: Item ativo do menu lateral tem destaque visual claro
O item do menu lateral correspondente à rota atual SHALL ser exibido com fundo em tom suave da cor primária, texto na cor primária e um indicador lateral, distinguindo-se claramente dos itens inativos.

#### Scenario: Navegação para Imóveis destaca o item correspondente
- **WHEN** o usuário está em uma página do módulo de Imóveis
- **THEN** o item "Imóveis" do menu lateral aparece com o destaque visual de item ativo e os demais itens não

## ADDED Requirements

### Requirement: Listar perfis
O sistema SHALL exibir a lista de perfis padrão do sistema com nome, descrição e quantidade de usuários vinculados.

#### Scenario: Listagem de perfis
- **WHEN** um usuário com permissão `perfis.viewAny` acessa `/perfis`
- **THEN** o sistema exibe os quatro perfis padrão (Administrador, Financeiro, Atendente, Corretor) com suas descrições

#### Scenario: Acesso sem permissão
- **WHEN** um usuário sem permissão `perfis.viewAny` tenta acessar `/perfis`
- **THEN** o sistema retorna erro 403

---

### Requirement: Visualizar permissões de um perfil
O sistema SHALL exibir a matriz de permissões de um perfil específico, organizada por módulo, em modo somente leitura.

#### Scenario: Visualização do perfil Administrador
- **WHEN** um usuário com permissão `perfis.view` acessa os detalhes do perfil Administrador
- **THEN** o sistema exibe todas as permissões deste perfil organizadas por módulo

#### Scenario: Edição não disponível no MVP
- **WHEN** o usuário tenta modificar as permissões de um perfil
- **THEN** o sistema não disponibiliza essa ação (interface somente leitura no MVP)

#### Scenario: Acesso sem permissão
- **WHEN** um usuário sem permissão `perfis.view` tenta visualizar um perfil
- **THEN** o sistema retorna erro 403

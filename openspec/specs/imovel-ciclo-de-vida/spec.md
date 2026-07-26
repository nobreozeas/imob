# imovel-ciclo-de-vida Specification

## Purpose

Gerenciar o ciclo de vida de um imóvel após o cadastro: exclusão lógica (soft delete), restauração, filtragem de imóveis excluídos e indicadores rápidos de contagem na listagem de imóveis.

## Requirements

### Requirement: Exclusão lógica de imóvel
O sistema SHALL permitir a exclusão lógica (soft delete) de um imóvel apenas para usuários com a permissão `imoveis.destroy`, e SHALL impedir a exclusão quando o imóvel possuir contrato de locação com status ativo.

#### Scenario: Excluir imóvel sem contrato ativo
- **WHEN** um usuário com permissão `imoveis.destroy` solicita a exclusão de um imóvel sem contrato ativo vinculado
- **THEN** o sistema preenche `deleted_at` do imóvel e ele deixa de aparecer nas listagens padrão

#### Scenario: Bloquear exclusão de imóvel com contrato ativo
- **WHEN** um usuário solicita a exclusão de um imóvel que possui contrato de locação com status `ativo`
- **THEN** o sistema rejeita a exclusão e retorna mensagem de erro explicando o motivo

#### Scenario: Usuário sem permissão não pode excluir
- **WHEN** um usuário sem a permissão `imoveis.destroy` tenta excluir um imóvel
- **THEN** o sistema nega a ação

---

### Requirement: Restauração de imóvel excluído
O sistema SHALL permitir restaurar um imóvel excluído logicamente apenas para usuários com a permissão `imoveis.restore`.

#### Scenario: Restaurar imóvel excluído
- **WHEN** um usuário com permissão `imoveis.restore` restaura um imóvel previamente excluído
- **THEN** o sistema limpa o `deleted_at` do imóvel e ele volta a aparecer nas listagens padrão

#### Scenario: Usuário sem permissão não pode restaurar
- **WHEN** um usuário sem a permissão `imoveis.restore` tenta restaurar um imóvel excluído
- **THEN** o sistema nega a ação

---

### Requirement: Filtro de imóveis excluídos na listagem
A listagem de imóveis SHALL oferecer um filtro para exibir apenas imóveis excluídos logicamente, disponível somente para usuários com permissão `imoveis.restore`.

#### Scenario: Listar imóveis excluídos
- **WHEN** um usuário com permissão `imoveis.restore` ativa o filtro "imóveis excluídos"
- **THEN** o sistema lista apenas os imóveis com `deleted_at` preenchido, com ação de restaurar disponível por linha

#### Scenario: Imóveis excluídos não aparecem na listagem padrão
- **WHEN** o filtro de excluídos não está ativo
- **THEN** a listagem não exibe imóveis com `deleted_at` preenchido

---

### Requirement: Indicadores rápidos na listagem de imóveis
A tela de listagem de imóveis SHALL exibir indicadores com a contagem total de imóveis e a contagem por status (disponível, alugado, reservado, em manutenção, inativo), refletindo os filtros de escopo aplicados (ex.: proprietário).

#### Scenario: Indicadores refletem os dados cadastrados
- **WHEN** a listagem de imóveis é carregada
- **THEN** os indicadores exibem a contagem total e por status calculada sobre os imóveis não excluídos visíveis ao usuário

#### Scenario: Indicadores não contam imóveis excluídos
- **WHEN** existem imóveis excluídos logicamente no sistema
- **THEN** eles não SHALL ser somados aos indicadores da listagem padrão

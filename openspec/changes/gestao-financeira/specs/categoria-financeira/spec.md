## ADDED Requirements

### Requirement: Cadastro de categoria financeira
O sistema SHALL permitir criar, editar, listar e inativar (soft delete) categorias financeiras, cada uma com `nome`, `tipo` (`entrada` ou `saida`), `slug` único, `descricao` opcional e `ativa`.

#### Scenario: Criação de categoria válida
- **WHEN** um administrador cadastra uma categoria com nome "Serviço de vistoria", tipo `entrada`
- **THEN** a categoria é criada com `ativa = true` e um `slug` único gerado a partir do nome

#### Scenario: Slug duplicado é rejeitado
- **WHEN** o usuário tenta cadastrar uma categoria cujo slug já existe
- **THEN** o sistema rejeita a operação com erro de validação

### Requirement: Categorias padrão seedadas
O sistema SHALL seedar, na migração inicial, as categorias padrão de entrada (`aluguel`, `receita_diversa`, `taxa_administracao`, `multa_atraso`, `juros_atraso`, `caucao`, `ajuste_positivo`) e de saída (`repasse_proprietario`, `despesa_operacional`, `despesa_administrativa`, `fornecedor`, `devolucao_caucao`, `manutencao_imovel`, `comissao_corretor`, `ajuste_negativo`).

#### Scenario: Categorias automáticas disponíveis após migração
- **WHEN** as migrations e seeders do módulo financeiro são executados
- **THEN** todas as categorias padrão de entrada e saída existem e estão `ativa = true`

### Requirement: Categoria em uso não pode ser excluída
O sistema SHALL impedir a exclusão (soft delete) de uma categoria financeira vinculada a algum lançamento financeiro.

#### Scenario: Tentativa de excluir categoria com lançamentos
- **WHEN** o usuário tenta excluir uma categoria financeira que possui ao menos um lançamento vinculado
- **THEN** o sistema rejeita a operação e informa que a categoria está em uso

### Requirement: Categoria inativa não pode ser usada em novo lançamento
O sistema SHALL impedir que uma categoria com `ativa = false` seja selecionada em um novo lançamento financeiro.

#### Scenario: Seleção de categoria inativa
- **WHEN** o usuário tenta criar um lançamento financeiro usando uma categoria inativa
- **THEN** o sistema rejeita a operação com erro de validação

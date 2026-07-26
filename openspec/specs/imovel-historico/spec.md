# imovel-historico Specification

## Purpose

Registrar e exibir o histórico de eventos relevantes ocorridos em um imóvel (criação, atualização, alteração de status, mídias, exclusão e restauração), fornecendo rastreabilidade e auditoria das ações realizadas.

## Requirements

### Requirement: Registro de histórico de eventos do imóvel
O sistema SHALL registrar um evento de histórico, com tipo, descrição, usuário responsável e data, sempre que ocorrer: criação do imóvel, atualização de dados, alteração de status, adição/remoção de foto, adição/remoção de documento, exclusão lógica ou restauração.

#### Scenario: Histórico ao criar imóvel
- **WHEN** um imóvel é cadastrado
- **THEN** o sistema registra um evento de histórico do tipo criação, com o usuário responsável e a data/hora

#### Scenario: Histórico ao alterar status
- **WHEN** o status de um imóvel é alterado
- **THEN** o sistema registra um evento de histórico do tipo alteração de status, contendo o status anterior e o novo status

#### Scenario: Histórico ao excluir e restaurar
- **WHEN** um imóvel é excluído logicamente ou restaurado
- **THEN** o sistema registra o evento correspondente (exclusão ou restauração) com o usuário responsável

#### Scenario: Histórico ao adicionar ou remover mídia
- **WHEN** uma foto ou documento é adicionado ou removido de um imóvel
- **THEN** o sistema registra o evento correspondente identificando o arquivo afetado

---

### Requirement: Exibição do histórico na tela de detalhes do imóvel
A tela de detalhes do imóvel SHALL exibir uma aba "Histórico" listando os eventos registrados em ordem cronológica decrescente, paginados.

#### Scenario: Visualizar histórico do imóvel
- **WHEN** um usuário com permissão de visualizar o imóvel acessa a aba "Histórico"
- **THEN** o sistema exibe os eventos do imóvel ordenados do mais recente para o mais antigo, com data, tipo de evento, descrição e usuário responsável

#### Scenario: Imóvel sem eventos registrados
- **WHEN** um imóvel não possui eventos de histórico (estado impossível após a criação, pois todo imóvel deve ter ao menos o evento de criação)
- **THEN** a aba exibe um estado vazio informativo

## ADDED Requirements

### Requirement: Anexar documento ao contrato pela interface
A aba Documentos dos detalhes do contrato SHALL permitir que um usuário autorizado envie um novo arquivo (com tipo classificado, incluindo "Contrato Assinado") e associá-lo ao contrato, refletindo imediatamente o novo documento na lista.

#### Scenario: Upload do contrato assinado
- **WHEN** o usuário autorizado seleciona um arquivo PDF do contrato assinado e escolhe o tipo "Contrato Assinado"
- **THEN** o documento é anexado ao contrato e passa a aparecer na lista de documentos com esse tipo

#### Scenario: Usuário sem permissão não vê a opção de anexar
- **WHEN** um usuário sem a permissão de gerenciar documentos do contrato acessa a aba Documentos
- **THEN** a opção de anexar novo documento não é exibida

### Requirement: Remover documento do contrato pela interface
A aba Documentos SHALL permitir que um usuário autorizado remova um documento já anexado ao contrato, mediante confirmação explícita.

#### Scenario: Remoção exige confirmação
- **WHEN** o usuário aciona a remoção de um documento anexado
- **THEN** o sistema solicita confirmação antes de remover o arquivo

#### Scenario: Documento removido não aparece mais na lista
- **WHEN** o usuário confirma a remoção de um documento
- **THEN** o documento deixa de aparecer na lista de documentos do contrato

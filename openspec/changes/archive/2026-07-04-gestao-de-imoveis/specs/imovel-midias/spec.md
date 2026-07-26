## ADDED Requirements

### Requirement: Upload incremental de fotos do imóvel
O sistema SHALL permitir o upload de uma ou mais fotos a um imóvel já cadastrado, sem exigir o preenchimento do formulário completo de edição, restrito a usuários com permissão `imoveis.gerenciar-fotos`.

#### Scenario: Adicionar foto a imóvel existente
- **WHEN** um usuário com permissão `imoveis.gerenciar-fotos` envia um arquivo de imagem válido (jpg, jpeg, png ou webp, até 5MB) para um imóvel
- **THEN** o sistema armazena o arquivo, cria o registro da foto vinculado ao imóvel e a exibe na galeria

#### Scenario: Rejeitar arquivo inválido
- **WHEN** um usuário envia um arquivo que não é imagem ou excede 5MB
- **THEN** o sistema rejeita o upload e retorna mensagem de validação

---

### Requirement: Remoção de foto do imóvel
O sistema SHALL permitir remover uma foto de um imóvel, restrito a usuários com permissão `imoveis.gerenciar-fotos`.

#### Scenario: Remover foto existente
- **WHEN** um usuário com permissão `imoveis.gerenciar-fotos` remove uma foto vinculada a um imóvel
- **THEN** o sistema apaga o arquivo do storage e o registro da foto, e ela deixa de aparecer na galeria

---

### Requirement: Definir foto principal do imóvel
O sistema SHALL permitir marcar uma foto do imóvel como principal, garantindo que apenas uma foto por imóvel seja principal a qualquer momento.

#### Scenario: Definir nova foto principal
- **WHEN** um usuário define uma foto diferente da atual como principal
- **THEN** o sistema desmarca a foto principal anterior e marca a nova foto como principal

#### Scenario: Foto principal automática
- **WHEN** um imóvel não possui nenhuma foto marcada como principal e recebe sua primeira foto
- **THEN** o sistema define automaticamente essa foto como principal

---

### Requirement: Upload incremental de documentos do imóvel
O sistema SHALL permitir o upload de documentos a um imóvel já cadastrado, informando o tipo do documento, restrito a usuários com permissão `imoveis.gerenciar-documentos`.

#### Scenario: Adicionar documento a imóvel existente
- **WHEN** um usuário com permissão `imoveis.gerenciar-documentos` envia um arquivo válido (pdf, jpg, jpeg, png ou docx, até 20MB) com um tipo de documento
- **THEN** o sistema armazena o arquivo e cria o registro do documento vinculado ao imóvel

#### Scenario: Rejeitar arquivo de documento inválido
- **WHEN** um usuário envia um arquivo de documento que excede 20MB ou possui formato não suportado
- **THEN** o sistema rejeita o upload e retorna mensagem de validação

---

### Requirement: Remoção de documento do imóvel
O sistema SHALL permitir remover um documento de um imóvel, restrito a usuários com permissão `imoveis.gerenciar-documentos`.

#### Scenario: Remover documento existente
- **WHEN** um usuário com permissão `imoveis.gerenciar-documentos` remove um documento vinculado a um imóvel
- **THEN** o sistema apaga o arquivo do storage e o registro do documento, e ele deixa de aparecer na lista de documentos

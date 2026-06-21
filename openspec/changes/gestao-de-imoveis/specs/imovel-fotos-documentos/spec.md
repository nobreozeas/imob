## ADDED Requirements

### Requirement: Upload de fotos do imóvel no wizard

O sistema SHALL permitir o upload de múltiplas fotos durante o cadastro e edição do imóvel (Step 5 do wizard). As fotos SHALL ser armazenadas em `storage/app/public/imoveis/{uuid}/fotos/`. Formatos aceitos: JPEG, PNG, WebP. Tamanho máximo por arquivo: 5 MB. Uma foto SHALL poder ser marcada como principal (`is_principal`).

#### Scenario: Upload de fotos no cadastro

- **WHEN** o usuário seleciona arquivos no step 5 e clica em "Salvar Imóvel"
- **THEN** o sistema armazena as fotos no storage, cria registros em `imovel_fotos` e define a primeira foto como principal caso nenhuma tenha sido explicitamente marcada

#### Scenario: Upload com formato inválido

- **WHEN** o usuário tenta enviar um arquivo PDF como foto
- **THEN** o backend retorna erro de validação e nenhuma foto é armazenada

#### Scenario: Upload com tamanho excedido

- **WHEN** o usuário tenta enviar uma foto maior que 5 MB
- **THEN** o backend retorna erro de validação indicando o limite de tamanho

#### Scenario: Sem fotos no cadastro

- **WHEN** o usuário salva o imóvel sem enviar fotos
- **THEN** o imóvel é criado normalmente e a tela de detalhes exibe placeholder de "sem fotos"

---

### Requirement: Gerenciamento de fotos na edição

O sistema SHALL permitir, durante a edição do imóvel (Step 5 do wizard), visualizar as fotos existentes, remover fotos selecionadas e adicionar novas fotos. A operação SHALL ser feita em transação: as fotos marcadas para remoção são deletadas e as novas são adicionadas em um único submit.

#### Scenario: Remoção de foto existente na edição

- **WHEN** o usuário marca uma foto existente para remoção e salva
- **THEN** o sistema deleta o arquivo físico do storage e remove o registro de `imovel_fotos`

#### Scenario: Adição de novas fotos na edição

- **WHEN** o usuário seleciona novas fotos no step 5 durante a edição e salva
- **THEN** o sistema armazena as novas fotos e mantém as fotos existentes não marcadas para remoção

#### Scenario: Troca de foto principal

- **WHEN** o usuário seleciona uma foto diferente como principal e salva
- **THEN** o sistema atualiza `is_principal = true` na foto selecionada e `is_principal = false` em todas as demais

---

### Requirement: Definição de foto principal

O sistema SHALL garantir que no máximo uma foto por imóvel tenha `is_principal = true`. A foto principal SHALL ser exibida em destaque na tela de detalhes e na listagem.

#### Scenario: Exclusividade da foto principal

- **WHEN** uma foto é marcada como principal
- **THEN** o sistema atualiza `is_principal = false` em todas as demais fotos do mesmo imóvel dentro da mesma transação

#### Scenario: Imóvel sem foto principal definida

- **WHEN** o imóvel tem fotos mas nenhuma marcada como principal
- **THEN** o sistema exibe a primeira foto (por ordem de criação) como destaque e na listagem

---

### Requirement: Upload de documentos do imóvel

O sistema SHALL permitir o upload de documentos anexos durante o cadastro e edição do imóvel (Step 5 do wizard). Formatos aceitos: PDF, JPEG, PNG, DOCX. Tamanho máximo por arquivo: 20 MB. Cada documento SHALL ter um campo `tipo` (texto livre, ex: matrícula, laudo de vistoria, contrato anterior) e o nome original do arquivo. Os documentos SHALL ser armazenados em `storage/app/public/imoveis/{uuid}/documentos/`.

#### Scenario: Upload de documentos no cadastro

- **WHEN** o usuário seleciona documentos no step 5 e salva
- **THEN** o sistema armazena os arquivos e cria registros em `imovel_documentos` com nome original e tipo informado

#### Scenario: Upload de documento com tipo opcional

- **WHEN** o usuário faz upload de um documento sem preencher o campo tipo
- **THEN** o documento é salvo com `tipo = null` e listado com o nome original

#### Scenario: Remoção de documento na edição

- **WHEN** o usuário marca um documento para remoção e salva
- **THEN** o sistema deleta o arquivo físico e remove o registro de `imovel_documentos`

#### Scenario: Limite de tamanho de documento excedido

- **WHEN** o usuário tenta enviar um documento maior que 20 MB
- **THEN** o backend retorna erro de validação indicando o limite e o documento não é armazenado

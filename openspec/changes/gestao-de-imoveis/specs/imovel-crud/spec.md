## ADDED Requirements

### Requirement: Listagem paginada de imóveis com filtros

O sistema SHALL exibir todos os imóveis cadastrados em tabela paginada (20 por página), ordenada por data de cadastro (mais recentes primeiro) por padrão. A listagem SHALL suportar filtros combinados por: busca textual (título, código), tipo do imóvel, finalidade, status, proprietário (nome ou razão social), cidade, bairro e faixa de valor de aluguel. Imóveis com SoftDelete ativo não SHALL aparecer.

#### Scenario: Listagem sem filtros

- **WHEN** o usuário acessa `/imoveis` sem parâmetros
- **THEN** o sistema exibe a lista paginada com os 20 imóveis mais recentes, mostrando código, título, tipo, proprietário, cidade/bairro, valor do aluguel, status e ações

#### Scenario: Filtro por status

- **WHEN** o usuário seleciona o filtro `status=disponivel`
- **THEN** apenas imóveis com status `disponivel` são exibidos, mantendo paginação e demais filtros

#### Scenario: Busca textual

- **WHEN** o usuário digita no campo de busca
- **THEN** o sistema filtra por título (ilike) ou código (ilike) em tempo real com debounce de 400ms

#### Scenario: Filtro por proprietário

- **WHEN** o usuário seleciona um proprietário no filtro
- **THEN** apenas imóveis cujo `proprietario_id` corresponde ao cliente selecionado são exibidos

---

### Requirement: Cadastro de imóvel via wizard de 5 etapas

O sistema SHALL permitir cadastrar um novo imóvel por meio de formulário em 5 etapas. O imóvel SHALL ser salvo apenas ao submeter a etapa 5. Erros de validação do backend SHALL redirecionar o usuário à etapa correspondente ao campo com erro. O código interno SHALL ser gerado automaticamente no formato `IMO-{YYYYMM}-{seq}` caso o campo seja deixado em branco. O imóvel SHALL estar vinculado a um proprietário (Cliente com papel `proprietario`).

#### Scenario: Cadastro completo com sucesso

- **WHEN** o usuário preenche todas as etapas e clica em "Salvar Imóvel"
- **THEN** o sistema cria o imóvel com status inicial `disponivel`, cria os registros de características e dados comerciais, armazena arquivos e redireciona para a tela de detalhes com mensagem de sucesso

#### Scenario: Validação de campo obrigatório no step 1

- **WHEN** o usuário tenta avançar do step 1 sem informar o título
- **THEN** o sistema exibe erro de validação local no campo título e impede o avanço

#### Scenario: Código interno duplicado

- **WHEN** o usuário informa um código interno já existente
- **THEN** o backend retorna erro de validação e o wizard redireciona para o step 1 onde o campo código está

#### Scenario: Geração automática de código

- **WHEN** o usuário deixa o campo código em branco ao salvar
- **THEN** o service gera automaticamente um código no padrão `IMO-{YYYYMM}-{seq}` antes de persistir

#### Scenario: Proprietário obrigatório

- **WHEN** o usuário tenta salvar sem selecionar proprietário
- **THEN** o backend retorna erro de validação e o wizard redireciona para o step 1

---

### Requirement: Edição de imóvel

O sistema SHALL permitir editar todos os dados de um imóvel existente por meio do mesmo wizard de 5 etapas, com os campos pré-preenchidos com os dados atuais. As regras de validação SHALL ser as mesmas do cadastro, exceto que o código único SHALL ignorar o próprio imóvel na verificação (`Rule::unique()->ignore`).

#### Scenario: Edição com dados pré-preenchidos

- **WHEN** o usuário acessa `/imoveis/{id}/edit`
- **THEN** o wizard exibe todos os campos preenchidos com os dados atuais do imóvel, incluindo características e dados comerciais

#### Scenario: Edição de código para valor já existente em outro imóvel

- **WHEN** o usuário altera o código para um valor que já pertence a outro imóvel
- **THEN** o backend retorna erro de validação único e o wizard redireciona para o step 1

#### Scenario: Edição salva com sucesso

- **WHEN** o usuário altera dados e clica em "Salvar Imóvel"
- **THEN** o sistema atualiza o imóvel e todos os relacionamentos em transação e redireciona para a tela de detalhes

---

### Requirement: Visualização detalhada do imóvel

O sistema SHALL exibir uma tela de detalhes com todas as informações do imóvel: dados principais, endereço, características, dados comerciais, galeria de fotos e lista de documentos. A tela SHALL mostrar também os contratos vinculados (lista vazia enquanto o módulo de contratos não existir) e permitir as ações de editar e alterar status.

#### Scenario: Acesso à tela de detalhes

- **WHEN** o usuário acessa `/imoveis/{id}`
- **THEN** o sistema exibe todos os dados do imóvel em seções organizadas com badges de status e tipo

#### Scenario: Galeria de fotos na tela de detalhes

- **WHEN** o imóvel tem fotos cadastradas
- **THEN** a foto principal é exibida em destaque e as demais em grid; imóvel sem fotos exibe placeholder

---

### Requirement: Controle de status do imóvel

O sistema SHALL permitir alterar o status do imóvel para: `disponivel`, `reservado`, `alugado`, `em_manutencao` ou `inativo`. A alteração SHALL ser feita via ação dedicada `PATCH /imoveis/{id}/status`. O sistema SHALL impedir a transição manual para `disponivel` caso exista contrato ativo vinculado ao imóvel.

#### Scenario: Alteração de status com confirmação

- **WHEN** o usuário clica em "Alterar Status" na tela de detalhes e confirma via SweetAlert
- **THEN** o sistema atualiza o status e exibe mensagem de sucesso

#### Scenario: Tentativa de setar disponivel com contrato ativo

- **WHEN** o usuário tenta alterar para `disponivel` e existe contrato ativo vinculado
- **THEN** o sistema retorna erro 422 com mensagem explicativa e o status não é alterado

#### Scenario: Apenas usuário com permissão pode alterar status

- **WHEN** um usuário sem a permissão `imoveis.alterar-status` acessa a action
- **THEN** o sistema retorna 403 Forbidden

---

### Requirement: Permissões de acesso ao módulo de imóveis

O sistema SHALL controlar o acesso ao módulo via Spatie Permissions: `imoveis.viewAny` (listagem), `imoveis.view` (detalhes), `imoveis.create` (cadastro), `imoveis.update` (edição), `imoveis.alterar-status` (mudança de status). O role `admin` SHALL ter todas as permissões atribuídas via seeder.

#### Scenario: Admin acessa o módulo

- **WHEN** um usuário com role `admin` acessa qualquer rota de imóveis
- **THEN** o sistema concede acesso normalmente

#### Scenario: Usuário sem permissão é bloqueado

- **WHEN** um usuário sem permissão `imoveis.viewAny` acessa `/imoveis`
- **THEN** o sistema retorna 403 Forbidden

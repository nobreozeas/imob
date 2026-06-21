Criar a área de Gestão de Clientes do sistema ImobGestor.

O sistema ImobGestor é um sistema de gestão imobiliária desenvolvido com Laravel 13, Inertia 3, Vue 3, TypeScript, Tailwind CSS 4, DaisyUI e PostgreSQL.

A funcionalidade deve permitir o cadastro e gerenciamento centralizado dos clientes da imobiliária. Um cliente representa uma pessoa física ou jurídica que pode se relacionar com a imobiliária como proprietário, inquilino ou ambos.

A gestão de clientes será usada como base para imóveis, contratos de aluguel, financeiro, dashboard e demais módulos do sistema.

## Objetivo

Criar uma área única de Clientes, evitando cadastros duplicados de proprietários e inquilinos.

Em vez de criar cadastros separados para “Proprietários” e “Inquilinos”, o sistema deve possuir uma entidade central chamada Cliente. Esse cliente poderá possuir um ou mais papéis no sistema:

* Proprietário;
* Inquilino.

Dessa forma, o mesmo cliente poderá ser proprietário de um imóvel e também inquilino de outro, sem necessidade de duplicar dados cadastrais.

## Funcionalidades esperadas

### 1. Listagem de clientes

A área de clientes deve possuir uma tela de listagem em tabela.

A listagem deve conter:

* nome ou razão social;
* CPF ou CNPJ;
* tipo de pessoa;
* telefone;
* e-mail;
* papéis do cliente;
* cidade;
* status;
* data de cadastro;
* ações.

A listagem deve possuir:

* paginação;
* busca textual;
* filtros por tipo de pessoa;
* filtros por papel;
* filtros por status;
* filtros por cidade;
* ordenação por nome, data de cadastro e status.

Os papéis devem ser exibidos visualmente, por exemplo:

* Proprietário;
* Inquilino;
* Proprietário e Inquilino.

### 2. Cadastro de cliente

O cadastro de cliente deve ser feito em formulário organizado e intuitivo.

O formulário deve conter:

#### Dados principais

* tipo de pessoa: física ou jurídica;
* nome completo, quando pessoa física;
* razão social, quando pessoa jurídica;
* nome fantasia, quando pessoa jurídica;
* CPF, quando pessoa física;
* CNPJ, quando pessoa jurídica;
* RG ou documento equivalente;
* data de nascimento, quando pessoa física;
* observações gerais.

#### Papéis do cliente

O cliente poderá ser marcado como:

* proprietário;
* inquilino.

A seleção deve permitir múltiplos papéis.

Exemplo:

* Um cliente marcado apenas como proprietário poderá ser vinculado a imóveis.
* Um cliente marcado apenas como inquilino poderá ser vinculado a contratos como locatário.
* Um cliente marcado como proprietário e inquilino poderá exercer ambos os papéis no sistema.

#### Contatos

* telefone principal;
* WhatsApp;
* telefone secundário;
* e-mail principal;
* e-mail alternativo.

#### Endereço

* CEP;
* logradouro;
* número;
* complemento;
* bairro;
* cidade;
* estado;
* ponto de referência.

#### Dados adicionais para proprietário

Quando o cliente possuir o papel de proprietário, o sistema poderá armazenar informações específicas relacionadas à propriedade e repasse financeiro:

* dados bancários para repasse;
* chave PIX;
* tipo de chave PIX;
* percentual padrão de administração, se aplicável;
* observações para repasse;
* indicação se emite nota fiscal ou recibo;
* preferência de recebimento.

#### Dados adicionais para inquilino

Quando o cliente possuir o papel de inquilino, o sistema poderá armazenar informações específicas relacionadas à locação:

* profissão;
* renda mensal aproximada;
* local de trabalho;
* telefone comercial;
* contato de emergência;
* observações para análise cadastral;
* restrições ou informações relevantes.

### 3. Edição de cliente

O sistema deve permitir editar os dados do cliente.

A edição deve respeitar vínculos existentes.

Não deve ser permitido remover o papel de proprietário se o cliente possuir imóveis ativos vinculados.

Não deve ser permitido remover o papel de inquilino se o cliente possuir contratos ativos vinculados.

Caso o usuário tente remover um papel que esteja em uso, o sistema deve exibir uma mensagem clara explicando o motivo.

### 4. Visualização detalhada do cliente

Criar uma tela de detalhes do cliente.

A tela deve exibir:

* dados principais;
* contatos;
* endereço;
* papéis;
* dados específicos de proprietário;
* dados específicos de inquilino;
* imóveis vinculados, quando for proprietário;
* contratos vinculados, quando for inquilino;
* histórico de alterações ou eventos relevantes.

### 5. Status do cliente

O cliente deve possuir status:

* ativo;
* inativo.

Clientes inativos não devem aparecer como opção em novos cadastros de imóveis ou contratos.

A exclusão física deve ser evitada. O sistema deve priorizar a inativação do cliente.

### 6. Regras de negócio

* Todo cliente deve possuir nome ou razão social.
* Todo cliente deve possuir CPF ou CNPJ.
* CPF e CNPJ devem ser únicos no sistema.
* Um cliente pode ser proprietário, inquilino ou ambos.
* Um cliente proprietário pode ser vinculado a um ou mais imóveis.
* Um cliente inquilino pode ser vinculado a um ou mais contratos.
* Um cliente inativo não pode ser usado em novos contratos ou novos imóveis.
* Um cliente com vínculos ativos não deve ser excluído fisicamente.
* O sistema deve evitar duplicidade de cadastro por CPF ou CNPJ.
* O sistema deve permitir que um cliente inicialmente cadastrado como inquilino possa posteriormente também ser marcado como proprietário, e vice-versa.

### 7. Modelagem sugerida

A modelagem deve considerar uma entidade principal de clientes.

Sugestão inicial:

* clientes;
* cliente_papeis;
* cliente_contatos, se necessário;
* cliente_enderecos, se necessário;
* cliente_dados_proprietario;
* cliente_dados_inquilino.

A tabela clientes deve armazenar os dados comuns.

Os papéis podem ser controlados por uma tabela auxiliar ou estrutura equivalente, permitindo que o mesmo cliente possua mais de um papel.

Os dados específicos de proprietário e inquilino devem ser separados dos dados comuns para evitar campos desnecessários e manter o cadastro organizado.

### 8. Integração com imóveis

Na criação ou edição de imóveis, o campo proprietário deve listar apenas clientes ativos que possuam o papel de proprietário.

Um imóvel deve estar vinculado obrigatoriamente a um cliente proprietário.

### 9. Integração com contratos

Na criação de contratos de aluguel, o campo inquilino deve listar apenas clientes ativos que possuam o papel de inquilino.

Um contrato deve estar vinculado obrigatoriamente a um cliente inquilino.

O mesmo cliente não deve ser impedido de ser proprietário de um imóvel e inquilino de outro.

### 10. Interface

A interface deve seguir o layout administrativo padrão do ImobGestor.

A área de clientes deve possuir:

* tela de listagem;
* tela de cadastro;
* tela de edição;
* tela de detalhes;
* filtros;
* ações de ativar e inativar;
* confirmação com SweetAlert;
* componentes Vue reutilizáveis;
* formulário com boa organização visual.

Usar DaisyUI e Tailwind CSS 4 para os componentes visuais.

A experiência deve ser simples, intuitiva e adequada para uso por imobiliárias pequenas e médias.

### 11. Backend

Criar as estruturas necessárias no Laravel:

* migrations;
* models;
* relacionamentos;
* controllers;
* form requests;
* services, se necessário;
* rotas;
* policies ou estrutura preparada para permissões;
* validações;
* seeders básicos, se necessário.

As validações devem considerar:

* obrigatoriedade de nome ou razão social;
* obrigatoriedade de CPF ou CNPJ;
* unicidade de CPF ou CNPJ;
* formato válido de e-mail;
* obrigatoriedade de pelo menos um papel;
* impedimento de remoção de papel em uso.

### 12. Permissões

A funcionalidade deve estar preparada para controle de permissões.

Inicialmente considerar:

* Administrador pode cadastrar, editar, visualizar, ativar e inativar clientes.
* Usuários com permissão específica podem visualizar clientes.
* Usuários com permissão específica podem cadastrar ou editar clientes.

### 13. Entregáveis esperados no OpenSpec

Gerar:

* proposal.md explicando o que será criado e por quê;
* design.md explicando a modelagem, fluxo, telas, regras e decisões técnicas;
* tasks.md com as etapas de implementação;
* specs necessárias para a capacidade de gestão de clientes.

A proposta deve ser escrita em português do Brasil.

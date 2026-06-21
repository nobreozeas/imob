Criar a área de Gestão de Imóveis do sistema ImobGestor.

O sistema ImobGestor é um sistema de gestão imobiliária desenvolvido com Laravel 13, Inertia 3, Vue 3, TypeScript, Tailwind CSS 4, DaisyUI e PostgreSQL.

A funcionalidade deve permitir o gerenciamento completo dos imóveis da imobiliária, servindo como base para contratos de locação, dashboard, financeiro e controle operacional.

A área de Gestão de Imóveis deve conter:

1. Listagem de imóveis

* Exibir os imóveis em tabela.
* Ter paginação.
* Ter filtros por:

  * código do imóvel;
  * tipo do imóvel;
  * finalidade;
  * status;
  * proprietário;
  * cidade;
  * bairro;
  * valor do aluguel.
* Ter campo de busca textual.
* Exibir informações principais:

  * código;
  * título/nome do imóvel;
  * tipo;
  * proprietário;
  * cidade/bairro;
  * valor do aluguel;
  * status;
  * data de cadastro;
  * ações.

2. Cadastro de imóvel
   O cadastro deve ser dividido em etapas para melhorar a experiência do usuário.

Etapa 1 — Dados principais:

* título do imóvel;
* código interno;
* tipo do imóvel;
* finalidade;
* status;
* proprietário;
* corretor responsável;
* descrição.

Etapa 2 — Endereço:

* CEP;
* logradouro;
* número;
* complemento;
* bairro;
* cidade;
* estado;
* ponto de referência.

Etapa 3 — Características:

* área total;
* área construída;
* quantidade de quartos;
* quantidade de suítes;
* quantidade de banheiros;
* quantidade de vagas de garagem;
* imóvel mobiliado ou não;
* aceita pet;
* possui piscina;
* possui quintal;
* possui varanda;
* outras características relevantes.

Etapa 4 — Dados comerciais:

* valor do aluguel;
* valor de venda, quando aplicável;
* valor do condomínio;
* valor do IPTU;
* indicação se o condomínio está incluso no aluguel;
* indicação de quem paga IPTU;
* indicação de quem paga água, energia e condomínio;
* valor de caução sugerido;
* observações comerciais.

Etapa 5 — Fotos e documentos:

* permitir upload de fotos do imóvel;
* definir foto principal;
* permitir documentos anexos, como matrícula, laudo de vistoria, contrato anterior ou documentos diversos.

3. Edição de imóvel

* Permitir alterar todos os dados cadastrados.
* Manter consistência com contratos ativos.
* Não permitir alterar status manualmente para “Disponível” caso exista contrato ativo vinculado ao imóvel.

4. Visualização detalhada do imóvel

* Criar uma tela de detalhes do imóvel.
* Exibir dados principais, endereço, características, dados comerciais, fotos, documentos e histórico.
* Exibir contratos vinculados ao imóvel.
* Exibir situação atual do imóvel.

5. Status do imóvel
   O imóvel deve possuir status controlado pelo sistema:

* Disponível;
* Reservado;
* Alugado;
* Em manutenção;
* Inativo.

Apenas imóveis com status “Disponível” poderão ser usados para criação de novos contratos de aluguel.

Quando um contrato for criado para o imóvel, o status deve passar para “Alugado”.

Quando um contrato for encerrado, o imóvel poderá voltar para “Disponível” ou “Em manutenção”, conforme decisão do usuário.

6. Regras de negócio

* Todo imóvel deve estar vinculado a um proprietário.
* O código interno do imóvel deve ser único.
* Imóveis inativos não devem aparecer como opção para novos contratos.
* Imóveis alugados não podem ser vinculados a outro contrato ativo.
* A listagem deve priorizar imóveis ativos.
* A exclusão física deve ser evitada; preferir inativação.
* O sistema deve permitir futura integração com vistoria, contratos, financeiro e dashboard.

7. Permissões
   A funcionalidade deve respeitar perfis e permissões do sistema:

* Administrador pode cadastrar, editar, visualizar, inativar e gerenciar imóveis.
* Usuários com permissão específica podem apenas visualizar ou cadastrar, conforme configuração futura.
* A estrutura deve estar preparada para controle de permissões.

8. Interface

* Usar layout administrativo padrão do ImobGestor.
* Usar componentes Vue reutilizáveis.
* Usar tabela com filtros, paginação e ações.
* Usar formulário em etapas.
* Usar SweetAlert para confirmações.
* Usar DaisyUI e Tailwind CSS 4 para os componentes visuais.
* A experiência deve ser simples, intuitiva e adequada para uso por imobiliárias pequenas e médias.

9. Backend

* Criar as entidades, migrations, models, controllers, requests, services e rotas necessárias.
* Utilizar PostgreSQL.
* Seguir a estrutura padrão do Laravel, mantendo separação suficiente para facilitar manutenção.
* Criar validações com Form Requests.
* Preparar relacionamentos com proprietários, corretores, contratos, fotos e documentos.

10. Entregáveis esperados no OpenSpec
    Gerar:

* proposal.md explicando o que será criado e por quê;
* design.md explicando a modelagem, fluxo, telas, regras e decisões técnicas;
* tasks.md com as etapas de implementação;
* specs necessárias para a capacidade de gestão de imóveis.

A proposta deve ser escrita em português do Brasil.

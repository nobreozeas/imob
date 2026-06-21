# Módulo de Contratos de Locação — ImobGestor

## 1. Visão Geral

O módulo de **Contratos de Locação** será responsável por gerenciar todo o ciclo de vida dos contratos de aluguel dentro do sistema ImobGestor.

Esse módulo será integrado diretamente com:

* Gestão de Imóveis;
* Gestão de Clientes;
* Gestão Financeira;
* Dashboard;
* Notificações;
* Controle de permissões;
* Documentos e anexos.

O contrato de locação representa o vínculo formal entre um **imóvel disponível**, um **proprietário** e um **inquilino**.

No ImobGestor, proprietários e inquilinos não serão cadastros separados. Ambos serão registros da entidade **Cliente**, diferenciados pelos seus papéis no sistema.

Um cliente poderá ser:

* apenas proprietário;
* apenas inquilino;
* proprietário e inquilino ao mesmo tempo.

## 2. Objetivo do Módulo

Criar uma área para cadastro, acompanhamento e controle dos contratos de aluguel da imobiliária, permitindo que a equipe gerencie:

* dados do contrato;
* imóvel locado;
* proprietário;
* inquilino;
* período de vigência;
* valor do aluguel;
* caução;
* multas;
* encargos;
* repasses ao proprietário;
* vencimentos;
* situação do contrato;
* histórico de eventos;
* geração futura de financeiro e notificações.

## 3. Premissas

O módulo deve considerar as seguintes premissas:

* Apenas imóveis com status **Disponível** poderão ser usados em novos contratos.
* Um imóvel não pode possuir mais de um contrato ativo ao mesmo tempo.
* Todo contrato deve estar vinculado a um imóvel.
* Todo contrato deve estar vinculado a um proprietário.
* Todo contrato deve estar vinculado a um inquilino.
* O proprietário será obtido a partir do imóvel selecionado.
* O inquilino deverá ser um cliente ativo com papel de **Inquilino**.
* O proprietário deverá ser um cliente ativo com papel de **Proprietário**.
* A ativação do contrato deve alterar o status do imóvel para **Alugado**.
* O encerramento do contrato deve permitir definir se o imóvel voltará para **Disponível** ou irá para **Em manutenção**.
* A exclusão física de contratos deve ser evitada. O sistema deve priorizar cancelamento, encerramento ou rescisão.

## 4. Conceitos Principais

### 4.1 Contrato

Representa o acordo de locação de um imóvel entre proprietário e inquilino, intermediado pela imobiliária.

### 4.2 Imóvel

Bem disponível para locação. Deve estar previamente cadastrado no sistema.

### 4.3 Proprietário

Cliente que possui o papel de proprietário e está vinculado ao imóvel.

### 4.4 Inquilino

Cliente que possui o papel de inquilino e será vinculado ao contrato.

### 4.5 Caução

Garantia financeira vinculada ao contrato. Pode ser usada como segurança em caso de inadimplência, danos ao imóvel ou outras obrigações previstas.

### 4.6 Encargos

Valores adicionais relacionados à locação, como:

* condomínio;
* IPTU;
* água;
* energia;
* internet;
* taxa de lixo;
* outros encargos.

### 4.7 Repasse

Valor que será repassado ao proprietário após o recebimento do aluguel e desconto da taxa de administração da imobiliária.

## 5. Status do Contrato

O contrato deve possuir status controlado pelo sistema.

Status sugeridos:

| Status                | Descrição                                                       |
| --------------------- | --------------------------------------------------------------- |
| Rascunho              | Contrato em elaboração, ainda sem validade operacional          |
| Aguardando Assinatura | Contrato revisado, mas ainda pendente de assinatura             |
| Ativo                 | Contrato vigente e válido                                       |
| Vencido               | Contrato chegou ao fim da vigência, mas ainda não foi encerrado |
| Encerrado             | Contrato finalizado normalmente                                 |
| Rescindido            | Contrato finalizado antes do prazo                              |
| Cancelado             | Contrato cancelado antes de entrar em vigor                     |

## 6. Fluxo Principal do Contrato

O fluxo principal será:

1. Usuário acessa a área de contratos.
2. Clica em “Novo Contrato”.
3. Seleciona um imóvel disponível.
4. O sistema carrega automaticamente o proprietário vinculado ao imóvel.
5. Usuário seleciona o inquilino.
6. Usuário informa dados da locação.
7. Usuário informa valores comerciais.
8. Usuário informa dados de caução.
9. Usuário informa regras de multa.
10. Usuário informa regras de repasse.
11. Usuário revisa os dados.
12. Usuário salva o contrato como rascunho ou ativa o contrato.
13. Ao ativar, o imóvel passa para o status “Alugado”.
14. O sistema poderá gerar as previsões financeiras do contrato.

## 7. Cadastro de Contrato

O cadastro deve ser dividido em etapas para melhorar a experiência do usuário.

## 7.1 Etapa 1 — Imóvel e Partes do Contrato

Campos:

* imóvel;
* proprietário;
* inquilino;
* corretor responsável;
* data de criação;
* observações iniciais.

Regras:

* O campo imóvel deve listar apenas imóveis com status **Disponível**.
* Ao selecionar o imóvel, o sistema deve preencher automaticamente o proprietário.
* O proprietário não deve ser alterado manualmente no contrato.
* O campo inquilino deve listar apenas clientes ativos com papel de **Inquilino**.

## 7.2 Etapa 2 — Dados da Locação

Campos:

* data de início do contrato;
* data de término do contrato;
* prazo em meses;
* dia de vencimento do aluguel;
* finalidade da locação;
* tipo de contrato;
* permite renovação automática;
* observações contratuais.

Finalidades possíveis:

* residencial;
* comercial;
* temporada;
* rural;
* outro.

Tipos possíveis:

* contrato novo;
* renovação;
* aditivo;
* contrato temporário.

## 7.3 Etapa 3 — Valores do Contrato

Campos:

* valor do aluguel;
* valor do condomínio;
* condomínio incluso no aluguel;
* valor do IPTU;
* IPTU incluso no aluguel;
* valor de outras taxas;
* valor total previsto mensal;
* percentual da taxa de administração da imobiliária;
* valor fixo da taxa de administração, se aplicável;
* forma de cobrança;
* forma de pagamento preferencial.

Regras:

* O valor do aluguel deve ser obrigatório.
* O dia de vencimento deve ser obrigatório.
* O sistema deve permitir definir se condomínio e IPTU serão pagos pelo inquilino ou pelo proprietário.
* A taxa de administração pode ser percentual ou valor fixo.
* O valor de repasse ao proprietário deve considerar a regra de administração definida.

## 7.4 Etapa 4 — Encargos e Responsabilidades

O contrato deve permitir definir quem será responsável por cada encargo.

Encargos sugeridos:

| Encargo               | Responsável               |
| --------------------- | ------------------------- |
| IPTU                  | Proprietário ou Inquilino |
| Condomínio            | Proprietário ou Inquilino |
| Água                  | Proprietário ou Inquilino |
| Energia               | Proprietário ou Inquilino |
| Internet              | Proprietário ou Inquilino |
| Taxa de lixo          | Proprietário ou Inquilino |
| Seguro incêndio       | Proprietário ou Inquilino |
| Manutenção ordinária  | Proprietário ou Inquilino |
| Manutenção estrutural | Proprietário              |

Regras:

* O sistema deve armazenar a responsabilidade de cada encargo.
* Essas informações poderão ser usadas futuramente para geração financeira, notificações e relatórios.

## 7.5 Etapa 5 — Caução

Campos:

* possui caução;
* tipo de caução;
* valor da caução;
* quantidade de meses de caução;
* data de recebimento;
* forma de recebimento;
* status da caução;
* observações.

Tipos de caução:

* dinheiro;
* PIX;
* transferência;
* depósito bancário;
* cheque;
* outro.

Status da caução:

* pendente;
* recebida;
* utilizada;
* devolvida;
* parcialmente devolvida.

Regras:

* A caução deve ser opcional.
* Quando houver caução, o valor deve ser obrigatório.
* O sistema deve permitir informar se a caução corresponde a uma quantidade de meses de aluguel.
* A caução deve possuir controle de recebimento e devolução.
* Ao encerrar o contrato, o sistema deve permitir registrar se a caução foi devolvida, retida ou parcialmente utilizada.

## 7.6 Etapa 6 — Multas e Penalidades

O contrato deve permitir configurar regras de multa.

### Multa por atraso

Campos:

* possui multa por atraso;
* percentual da multa;
* valor fixo da multa, se aplicável;
* juros por dia;
* tolerância em dias;
* observações.

Exemplo:

* multa de 2% após o vencimento;
* juros de 1% ao mês ou proporcional ao dia;
* tolerância de 0, 1, 3 ou 5 dias.

### Multa por quebra de contrato

Campos:

* possui multa por quebra de contrato;
* tipo de cálculo;
* quantidade de aluguéis;
* percentual;
* valor fixo;
* cálculo proporcional ao tempo restante;
* observações.

Tipos de cálculo possíveis:

* valor fixo;
* percentual sobre o contrato;
* quantidade de aluguéis;
* proporcional ao período restante.

Regras:

* A multa por atraso será usada futuramente no financeiro.
* A multa por quebra será usada no processo de rescisão do contrato.
* O sistema deve permitir registrar a regra, mesmo que o cálculo financeiro automático seja implementado posteriormente.

## 7.7 Etapa 7 — Repasses ao Proprietário

Campos:

* percentual de administração da imobiliária;
* valor fixo de administração;
* forma de repasse;
* dia previsto para repasse;
* dados bancários do proprietário;
* chave PIX;
* observações de repasse.

Regras:

* O repasse deve ser calculado com base no valor recebido do aluguel.
* O sistema deve considerar a taxa de administração da imobiliária.
* O repasse pode ser feito por PIX, transferência ou outro meio.
* Futuramente, o módulo financeiro poderá gerar contas a pagar para o proprietário.

## 7.8 Etapa 8 — Documentos e Anexos

O contrato deve permitir anexar documentos.

Documentos possíveis:

* contrato assinado;
* documentos do inquilino;
* documentos do proprietário;
* laudo de vistoria inicial;
* laudo de vistoria final;
* comprovante de caução;
* comprovantes diversos;
* aditivos contratuais.

Campos do anexo:

* nome do arquivo;
* tipo do documento;
* arquivo;
* data de envio;
* observações.

## 7.9 Etapa 9 — Revisão e Ativação

Antes de ativar o contrato, o sistema deve exibir uma tela de revisão com:

* imóvel;
* proprietário;
* inquilino;
* período de vigência;
* valor do aluguel;
* dia de vencimento;
* caução;
* multas;
* encargos;
* repasse;
* observações.

A ativação deve exigir confirmação.

Ao ativar:

* o contrato passa para o status **Ativo**;
* o imóvel passa para o status **Alugado**;
* o sistema poderá gerar previsões financeiras;
* o evento deve ser registrado no histórico.

## 8. Listagem de Contratos

A tela de listagem deve exibir os contratos em tabela.

Colunas sugeridas:

* número do contrato;
* imóvel;
* proprietário;
* inquilino;
* data de início;
* data de término;
* valor do aluguel;
* status;
* vencimento;
* ações.

Filtros:

* status;
* imóvel;
* proprietário;
* inquilino;
* período de início;
* período de término;
* contratos vencendo;
* contratos ativos;
* contratos encerrados;
* contratos rescindidos.

Ações:

* visualizar;
* editar;
* ativar;
* cancelar;
* encerrar;
* rescindir;
* anexar documentos;
* visualizar financeiro;
* imprimir ou gerar PDF futuramente.

## 9. Tela de Detalhes do Contrato

A tela de detalhes deve exibir:

* dados principais;
* dados do imóvel;
* dados do proprietário;
* dados do inquilino;
* valores;
* caução;
* multas;
* encargos;
* repasses;
* documentos;
* histórico;
* situação financeira futura;
* ações disponíveis conforme status.

A tela deve deixar claro o status atual do contrato.

## 10. Edição de Contrato

Regras de edição:

* Contratos em rascunho podem ser editados livremente.
* Contratos aguardando assinatura podem ser editados com restrições.
* Contratos ativos podem ter edição limitada.
* Contratos encerrados, rescindidos ou cancelados não devem permitir edição direta dos dados principais.
* Alterações importantes em contratos ativos devem gerar histórico.

Campos sensíveis:

* imóvel;
* inquilino;
* data de início;
* data de término;
* valor do aluguel;
* caução;
* multa;
* taxa de administração.

Esses campos devem exigir confirmação ao serem alterados em contrato ativo.

## 11. Encerramento de Contrato

O encerramento será usado quando o contrato terminar normalmente.

Campos:

* data de encerramento;
* motivo;
* situação da caução;
* valor devolvido da caução;
* valor retido da caução;
* observações;
* novo status do imóvel.

Opções para novo status do imóvel:

* Disponível;
* Em manutenção;
* Inativo.

Regras:

* Ao encerrar o contrato, ele passa para o status **Encerrado**.
* O imóvel deixa de estar alugado.
* O usuário deve informar o novo status do imóvel.
* O sistema deve registrar histórico do encerramento.

## 12. Rescisão de Contrato

A rescisão será usada quando o contrato for encerrado antes do prazo.

Campos:

* data da rescisão;
* responsável pela rescisão;
* motivo da rescisão;
* aplica multa;
* valor da multa;
* observações;
* situação da caução;
* novo status do imóvel.

Responsável pela rescisão:

* inquilino;
* proprietário;
* imobiliária;
* acordo entre as partes.

Regras:

* Ao rescindir, o contrato passa para o status **Rescindido**.
* O sistema deve permitir calcular ou registrar manualmente a multa.
* O imóvel deve sair do status **Alugado**.
* O usuário deve definir se o imóvel ficará **Disponível**, **Em manutenção** ou **Inativo**.

## 13. Integração com Financeiro

O módulo de contratos deve preparar dados para o financeiro.

Ao ativar um contrato, o sistema poderá gerar:

* contas a receber de aluguel;
* previsões mensais;
* cobrança de caução;
* cobranças de encargos;
* contas a pagar de repasse ao proprietário;
* receitas da imobiliária;
* multas por atraso;
* multa por rescisão.

Regras financeiras futuras:

* Gerar parcelas mensais conforme vigência do contrato.
* Gerar vencimentos com base no dia de vencimento.
* Permitir atualização de pagamento.
* Permitir baixa manual.
* Permitir envio de cobrança por e-mail.
* Permitir envio futuro de PIX para pagamento.
* Permitir controle de inadimplência.

## 14. Integração com Dashboard

O módulo deve fornecer dados para o dashboard, como:

* contratos ativos;
* contratos vencendo;
* contratos vencidos;
* imóveis alugados;
* receita mensal prevista;
* receita recebida;
* inadimplência;
* valores a repassar;
* cauções recebidas;
* contratos rescindidos.

## 15. Integração com Notificações

O sistema deve estar preparado para notificar:

* vencimento de aluguel;
* atraso no pagamento;
* contrato próximo do vencimento;
* necessidade de renovação;
* caução pendente;
* contrato aguardando assinatura;
* rescisão ou encerramento registrado.

Inicialmente, as notificações poderão ser feitas por e-mail.

Futuramente, poderão ser integradas com WhatsApp ou outros canais.

## 16. Permissões

A funcionalidade deve respeitar o controle de permissões do sistema.

Permissões sugeridas:

* visualizar contratos;
* criar contratos;
* editar contratos;
* ativar contratos;
* cancelar contratos;
* encerrar contratos;
* rescindir contratos;
* gerenciar caução;
* gerenciar documentos;
* visualizar dados financeiros do contrato;
* gerar financeiro do contrato.

Perfis sugeridos:

### Administrador

Pode realizar todas as ações.

### Gestor

Pode criar, editar, visualizar, ativar, encerrar e rescindir contratos.

### Corretor

Pode visualizar contratos relacionados e criar rascunhos, conforme permissão.

### Financeiro

Pode visualizar dados financeiros, caução, cobranças e repasses.

## 17. Modelagem Sugerida

As tabelas devem usar nomes em português.

### contratos_locacao

Campos sugeridos:

* id;
* numero;
* imovel_id;
* proprietario_id;
* inquilino_id;
* corretor_id;
* status;
* finalidade;
* tipo_contrato;
* data_inicio;
* data_fim;
* prazo_meses;
* dia_vencimento;
* valor_aluguel;
* valor_condominio;
* condominio_incluso;
* valor_iptu;
* iptu_incluso;
* valor_outras_taxas;
* valor_total_mensal;
* taxa_administracao_percentual;
* taxa_administracao_valor;
* permite_renovacao;
* observacoes;
* criado_por;
* atualizado_por;
* created_at;
* updated_at;
* deleted_at.

### contrato_encargos

Campos sugeridos:

* id;
* contrato_id;
* tipo_encargo;
* responsavel;
* valor_estimado;
* incluso_no_aluguel;
* observacoes;
* created_at;
* updated_at.

### contrato_caucoes

Campos sugeridos:

* id;
* contrato_id;
* possui_caucao;
* tipo_caucao;
* valor;
* quantidade_meses;
* data_recebimento;
* forma_recebimento;
* status;
* valor_devolvido;
* valor_retido;
* data_devolucao;
* observacoes;
* created_at;
* updated_at.

### contrato_multas

Campos sugeridos:

* id;
* contrato_id;
* possui_multa_atraso;
* multa_atraso_percentual;
* multa_atraso_valor;
* juros_dia_percentual;
* tolerancia_dias;
* possui_multa_rescisao;
* tipo_calculo_rescisao;
* quantidade_alugueis_rescisao;
* percentual_rescisao;
* valor_fixo_rescisao;
* proporcional_periodo_restante;
* observacoes;
* created_at;
* updated_at.

### contrato_documentos

Campos sugeridos:

* id;
* contrato_id;
* nome;
* tipo_documento;
* caminho_arquivo;
* mime_type;
* tamanho;
* observacoes;
* enviado_por;
* created_at;
* updated_at.

### contrato_historicos

Campos sugeridos:

* id;
* contrato_id;
* usuario_id;
* tipo_evento;
* descricao;
* dados_anteriores;
* dados_novos;
* created_at.

## 18. Relacionamentos

### Contrato pertence a:

* imóvel;
* proprietário;
* inquilino;
* corretor;
* usuário criador.

### Contrato possui:

* encargos;
* caução;
* multas;
* documentos;
* histórico;
* lançamentos financeiros futuros.

### Imóvel possui:

* vários contratos ao longo do tempo;
* apenas um contrato ativo por vez.

### Cliente pode possuir:

* contratos como inquilino;
* imóveis como proprietário;
* contratos relacionados como proprietário do imóvel.

## 19. Validações

Validações principais:

* imóvel é obrigatório;
* imóvel deve estar disponível para novo contrato;
* proprietário é obrigatório;
* proprietário deve possuir papel de proprietário;
* inquilino é obrigatório;
* inquilino deve possuir papel de inquilino;
* data de início é obrigatória;
* data de término deve ser maior que a data de início;
* valor do aluguel é obrigatório e maior que zero;
* dia de vencimento é obrigatório;
* status deve ser válido;
* caução, quando informada, deve possuir valor;
* multa, quando informada, deve possuir regra de cálculo;
* não permitir contrato ativo duplicado para o mesmo imóvel.

## 20. Regras de Negócio

* Apenas imóveis disponíveis podem iniciar novo contrato.
* Ao ativar contrato, o imóvel deve ficar alugado.
* Ao encerrar ou rescindir contrato, o imóvel deve sair do status alugado.
* Contrato em rascunho não deve gerar financeiro.
* Contrato cancelado não deve gerar financeiro.
* Contrato ativo pode gerar previsões financeiras.
* Cliente inativo não pode ser selecionado como inquilino.
* Imóvel inativo não pode ser selecionado para contrato.
* Proprietário inativo não pode ser usado em novo contrato.
* A caução deve ser controlada separadamente do aluguel mensal.
* A multa por atraso deve ser usada em cobranças vencidas.
* A multa por rescisão deve ser usada no encerramento antecipado.
* Toda mudança relevante no contrato deve gerar histórico.

## 21. Telas Necessárias

### 21.1 Listagem de Contratos

Tela com tabela, filtros, paginação e ações.

### 21.2 Cadastro de Contrato

Tela em etapas para criação do contrato.

### 21.3 Edição de Contrato

Tela para alteração dos dados conforme status.

### 21.4 Detalhes do Contrato

Tela completa de visualização.

### 21.5 Encerramento de Contrato

Tela ou modal para encerramento normal.

### 21.6 Rescisão de Contrato

Tela ou modal para rescisão antecipada.

### 21.7 Documentos do Contrato

Área para anexar e visualizar documentos.

### 21.8 Histórico do Contrato

Área para visualizar eventos e alterações.

## 22. Componentes Frontend Sugeridos

Componentes Vue sugeridos:

* `ContratosTable.vue`;
* `ContratoForm.vue`;
* `ContratoStepImovelPartes.vue`;
* `ContratoStepDadosLocacao.vue`;
* `ContratoStepValores.vue`;
* `ContratoStepEncargos.vue`;
* `ContratoStepCaucao.vue`;
* `ContratoStepMultas.vue`;
* `ContratoStepRevisao.vue`;
* `ContratoStatusBadge.vue`;
* `ContratoDetalhesCard.vue`;
* `ContratoDocumentos.vue`;
* `ContratoHistorico.vue`;
* `EncerrarContratoModal.vue`;
* `RescindirContratoModal.vue`.

## 23. Rotas Sugeridas

Rotas administrativas:

* `GET /contratos`
* `GET /contratos/create`
* `POST /contratos`
* `GET /contratos/{contrato}`
* `GET /contratos/{contrato}/edit`
* `PUT /contratos/{contrato}`
* `POST /contratos/{contrato}/ativar`
* `POST /contratos/{contrato}/cancelar`
* `POST /contratos/{contrato}/encerrar`
* `POST /contratos/{contrato}/rescindir`
* `POST /contratos/{contrato}/documentos`
* `DELETE /contratos/{contrato}/documentos/{documento}`

## 24. Backend Laravel

Estrutura sugerida:

* `ContratoLocacaoController`;
* `StoreContratoLocacaoRequest`;
* `UpdateContratoLocacaoRequest`;
* `AtivarContratoRequest`;
* `EncerrarContratoRequest`;
* `RescindirContratoRequest`;
* `ContratoLocacaoService`;
* `ContratoFinanceiroService`;
* `ContratoStatusService`;
* `ContratoDocumentoService`;
* `ContratoHistoricoService`.

Models sugeridos:

* `ContratoLocacao`;
* `ContratoEncargo`;
* `ContratoCaucao`;
* `ContratoMulta`;
* `ContratoDocumento`;
* `ContratoHistorico`.

## 25. Observações de Implementação

* Usar Soft Delete para contratos.
* Usar transações de banco ao ativar, encerrar ou rescindir contratos.
* Ao ativar contrato, atualizar imóvel e registrar histórico na mesma transação.
* Ao encerrar contrato, atualizar contrato, imóvel, caução e histórico na mesma transação.
* Evitar regras importantes diretamente no controller.
* Usar Form Requests para validação.
* Usar Services para regras de ativação, encerramento, rescisão e geração financeira.
* Preparar a estrutura para geração futura de PDF do contrato.

## 26. Critérios de Aceite

A funcionalidade será considerada concluída quando:

* for possível listar contratos;
* for possível cadastrar contrato em etapas;
* for possível selecionar apenas imóveis disponíveis;
* o proprietário for carregado automaticamente a partir do imóvel;
* for possível selecionar apenas clientes com papel de inquilino;
* for possível configurar valores, caução, multas e encargos;
* for possível salvar contrato como rascunho;
* for possível ativar contrato;
* ao ativar, o imóvel mudar para alugado;
* não for possível criar dois contratos ativos para o mesmo imóvel;
* for possível visualizar detalhes do contrato;
* for possível anexar documentos;
* for possível encerrar contrato;
* ao encerrar, o imóvel mudar para disponível, manutenção ou inativo;
* for possível rescindir contrato;
* toda ação relevante gerar histórico;
* as permissões básicas forem respeitadas.

## 27. Próximas Evoluções

Funcionalidades futuras:

* geração automática de PDF do contrato;
* assinatura digital;
* integração com cobrança PIX;
* envio automático de boleto ou cobrança;
* notificações por WhatsApp;
* renovação automática;
* aditivos contratuais;
* vistoria inicial e final;
* integração com inadimplência;
* relatório de contratos vencendo;
* relatório de repasses;
* relatório de cauções;
* dashboard financeiro por contrato.

## 28. Resumo

O módulo de Contratos de Locação será uma das áreas centrais do ImobGestor.

Ele conectará imóveis, clientes, financeiro e notificações, permitindo que a imobiliária controle todo o ciclo de vida de uma locação, desde o cadastro inicial até o encerramento ou rescisão do contrato.

A estrutura deve ser simples para o MVP, mas preparada para evoluções futuras como assinatura digital, cobrança automatizada, PIX, vistoria, geração de PDF e controle financeiro completo.

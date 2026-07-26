# PRD — Sistema de Gestão Imobiliária

**Produto:** Sistema de Gestão Imobiliária  
**Versão:** 1.0 — MVP  
**Data:** 29/06/2026  
**Formato:** Aplicação web responsiva  
**Status:** Documento de produto consolidado  

---

## 1. Resumo Executivo

O Sistema de Gestão Imobiliária tem como objetivo permitir que imobiliárias realizem a gestão completa da operação de locação de imóveis, incluindo cadastro de imóveis, proprietários, inquilinos, corretores, contratos, recebimentos, repasses, caução, movimentações financeiras, usuários, permissões, notificações e relatórios gerenciais.

O MVP deve priorizar simplicidade operacional, produtividade da equipe e facilidade de aprendizado. A primeira versão deve permitir que uma imobiliária controle toda a administração de imóveis de aluguel em uma plataforma web moderna, intuitiva, segura e responsiva.

O contrato de locação será o núcleo operacional do sistema, pois conecta imóvel, proprietário, inquilino, corretor, encargos, caução, parcelas de aluguel, recebimentos, multas, repasses ao proprietário e movimentações financeiras.

---

## 2. Problema

Imobiliárias que administram imóveis de aluguel precisam controlar diversas informações em paralelo:

- Imóveis disponíveis, alugados, reservados ou inativos.
- Proprietários e seus dados bancários.
- Inquilinos e histórico de locações.
- Contratos com regras específicas de pagamento, multas, encargos e caução.
- Parcelas mensais de aluguel.
- Recebimentos, inadimplência e cobranças.
- Repasses aos proprietários.
- Receitas e despesas da imobiliária.
- Usuários internos com diferentes níveis de acesso.
- Relatórios de operação e financeiro.

Sem um sistema centralizado, a operação tende a depender de planilhas, controles manuais, comunicação informal e baixa rastreabilidade, aumentando o risco de erro financeiro, atraso em repasses, perda de histórico e dificuldade de acompanhamento.

---

## 3. Objetivos do Produto

### 3.1 Objetivo principal

Criar uma plataforma web para gestão completa da operação de locação de imóveis, permitindo controle centralizado, rastreável e seguro da jornada de administração imobiliária.

### 3.2 Objetivos do MVP

O MVP deve permitir que a imobiliária consiga:

- Cadastrar imóveis.
- Cadastrar proprietários.
- Cadastrar inquilinos.
- Cadastrar corretores.
- Gerenciar contratos de locação.
- Controlar encargos contratuais.
- Controlar caução e garantias.
- Gerar parcelas de aluguel.
- Registrar pagamentos.
- Calcular multa e juros por atraso.
- Calcular multa por rescisão antecipada.
- Gerar repasses aos proprietários.
- Controlar receitas e despesas.
- Controlar usuários, perfis e permissões.
- Notificar inquilinos por email sobre vencimentos e cobranças.
- Emitir relatórios gerenciais básicos.

### 3.3 Objetivos secundários

- Reduzir erros manuais no controle financeiro.
- Aumentar a rastreabilidade da operação.
- Facilitar o acompanhamento de contratos vencendo ou vencidos.
- Melhorar a previsibilidade de recebimentos e repasses.
- Preparar a arquitetura para integrações futuras.

---

## 4. Não Objetivos do MVP

As seguintes funcionalidades não fazem parte da primeira versão:

- Portal do proprietário.
- Portal do inquilino.
- Assinatura digital.
- Vistorias digitais.
- Integração com Asaas.
- PIX automático.
- Boleto automático.
- Integração com WhatsApp.
- CRM imobiliário.
- Venda de imóveis.
- Aplicativo mobile.
- Multiempresa.
- Multiidioma.
- Correção monetária automática.
- Reajuste automático por índice.
- Contabilidade completa.
- Conciliação bancária automática.

---

## 5. Personas

### 5.1 Administrador da imobiliária

Responsável pela gestão geral do sistema, configuração de usuários, permissões, acompanhamento de indicadores e validação da operação.

**Necessidades:**

- Visualizar indicadores gerais.
- Gerenciar usuários e permissões.
- Acompanhar contratos, inadimplência e repasses.
- Garantir que a operação esteja correta e auditável.

### 5.2 Atendente / Operador administrativo

Responsável por cadastros, contratos, atendimento de inquilinos e apoio operacional.

**Necessidades:**

- Cadastrar imóveis, proprietários e inquilinos.
- Criar contratos de locação.
- Consultar dados rapidamente.
- Anexar documentos.
- Acompanhar status dos contratos.

### 5.3 Financeiro

*Responsável por recebimentos, despesas, repasses, controle de caução e relatórios financeiros.

**Necessidades:**

- Registrar pagamentos de aluguel.
- Verificar parcelas pendentes e vencidas.
- Gerar e pagar repasses.
- Controlar entradas e saídas.
- Emitir relatórios financeiros.

### 5.4 Corretor

Responsável por intermediação de locações e relacionamento comercial.

**Necessidades:**

- Consultar imóveis disponíveis.
- Consultar contratos vinculados.
- Ver dados básicos de inquilinos e proprietários quando autorizado.
- Acompanhar contratos sob sua responsabilidade.

---

## 6. Escopo do MVP

### 6.1 Módulos incluídos

1. Autenticação.
2. Dashboard.
3. Gestão de imóveis.
4. Gestão de proprietários.
5. Gestão de inquilinos.
6. Gestão de corretores.
7. Gestão de contratos de locação.
8. Gestão de parcelas de aluguel.
9. Gestão de pagamentos.
10. Gestão de repasses aos proprietários.
11. Gestão de caução e garantias.
12. Gestão financeira.
13. Usuários, perfis e permissões.
14. Notificações por email.
15. Relatórios básicos.

### 6.2 Priorização

| Prioridade | Descrição |
|---|---|
| P0 | Essencial para operar o MVP. Sem isso o produto não cumpre o objetivo principal. |
| P1 | Importante para eficiência operacional, mas pode ser refinado após a primeira entrega. |
| P2 | Desejável, pode entrar em fase posterior. |

---

## 7. Stack Tecnológica

### 7.1 Backend

- Laravel 13.
- PHP 8.4+.
- PostgreSQL.
- Laravel Queues.
- Laravel Scheduler.
- Laravel Notifications.

### 7.2 Frontend

- Vue 3.
- Inertia.js.
- TypeScript.
- Tailwind CSS 4.
- DaisyUI.

### 7.3 Componentes auxiliares

- SweetAlert2 para confirmações.
- Storage para uploads de fotos e documentos.
- Email transacional para notificações e primeiro acesso.

---

## 8. Diretrizes de Produto e UX

### 8.1 Princípios de UX

- Simplicidade operacional.
- Clareza de status.
- Poucos cliques para tarefas recorrentes.
- Feedback visual após ações importantes.
- Prevenção de erro em operações financeiras.
- Rastreamento de histórico em eventos críticos.

### 8.2 Listagens

Todas as listagens devem possuir:

- Tabela.
- Paginação.
- Ordenação.
- Pesquisa.
- Filtros.
- Ações por linha.
- Status visual colorido.

### 8.3 Formulários

Usar:

- **Modal:** quando houver poucos campos.
- **Wizard / Steps:** quando o cadastro possuir múltiplas etapas.
- **Página completa:** quando houver grande quantidade de informações ou regras críticas.

### 8.4 Visualizações

Sempre que possível utilizar:

- Cards de resumo.
- Tabs.
- Badges de status.
- Indicadores visuais.
- Histórico de eventos.

### 8.5 Exclusão

O sistema não deve excluir fisicamente registros operacionais. Todas as principais entidades devem utilizar Soft Delete.

---

## 9. Requisitos Não Funcionais

### 9.1 Segurança

- Apenas usuários cadastrados podem acessar o sistema.
- Não deve haver auto cadastro.
- Senhas devem ser armazenadas com hash seguro.
- Ao cadastrar usuário, o sistema deve gerar senha aleatória ou token de primeiro acesso.
- O acesso inicial deve ser enviado por email.
- No primeiro acesso, o usuário deve ser obrigado a trocar a senha.
- O sistema deve possuir controle de perfis e permissões.
- Rotas e ações sensíveis devem ser protegidas por Policies.
- Operações financeiras devem registrar usuário responsável.

### 9.2 Auditoria

- Registrar criação, atualização e exclusão lógica das principais entidades.
- Registrar eventos importantes em contratos.
- Registrar movimentações de caução.
- Registrar data, usuário e contexto de pagamentos e repasses.

### 9.3 Performance

- Todas as listagens devem ser paginadas.
- Usar eager loading para evitar N+1.
- Evitar carregamento excessivo de imagens em listagens.
- Filtrar dados no backend sempre que possível.

### 9.4 Manutenibilidade

- Utilizar Service Layer para regras de negócio.
- Utilizar Form Requests para validação.
- Utilizar Policies para autorização.
- Utilizar Enums para status e tipos fixos.
- Utilizar componentes Vue reutilizáveis.
- Utilizar tipagem TypeScript no frontend.

### 9.5 Responsividade

- O sistema deve ser utilizável em desktop, notebook, tablet e celular.
- Tabelas devem possuir adaptação visual para telas pequenas.
- Ações críticas devem continuar acessíveis em dispositivos móveis.

---

## 10. Regras Gerais de Dados

- Entidades principais devem possuir UUID.
- Tabelas devem usar nomes em português, snake_case e sem acentos.
- Campos de status devem usar valores padronizados via Enum.
- Campos monetários devem usar decimal com precisão adequada.
- Campos de CPF/CNPJ devem aceitar máscara no frontend e valor normalizado no backend.
- Emails devem ser únicos quando aplicável.
- Registros excluídos devem usar Soft Delete.

---

# 11. Módulo de Autenticação

## 11.1 Objetivo

Permitir acesso seguro ao sistema apenas por usuários cadastrados pela imobiliária.

## 11.2 Funcionalidades

- Login.
- Logout.
- Recuperação de senha.
- Alteração de senha.
- Primeiro acesso com troca obrigatória de senha.
- Bloqueio lógico de usuário.

## 11.3 Regras de negócio

- Não haverá auto cadastro.
- Ao criar um usuário, o sistema deve gerar acesso inicial aleatório.
- O acesso inicial deve ser enviado por email.
- O usuário deve trocar a senha no primeiro login.
- Usuários inativos não podem acessar o sistema.
- Permissões devem ser avaliadas por perfil.

## 11.4 Critérios de aceite

- Dado um usuário ativo, quando informar email e senha válidos, então deve acessar o sistema.
- Dado um usuário inativo, quando tentar login, então o acesso deve ser negado.
- Dado um novo usuário, quando acessar pela primeira vez, então deve ser direcionado para troca de senha.
- Dado um usuário sem permissão, quando tentar acessar módulo restrito, então deve receber bloqueio de acesso.

---

# 12. Módulo de Dashboard

## 12.1 Objetivo

Fornecer visão rápida da operação da imobiliária.

## 12.2 Indicadores

- Imóveis disponíveis.
- Imóveis alugados.
- Contratos ativos.
- Contratos vencendo.
- Contratos vencidos.
- Receitas do mês.
- Despesas do mês.
- Inadimplência.
- Repasses pendentes.
- Cauções aguardando recebimento.

## 12.3 Gráficos

- Receitas x despesas.
- Recebimentos por mês.
- Inadimplência por período.
- Contratos por status.

## 12.4 Filtros

- Período.
- Proprietário.
- Status de contrato.
- Status de imóvel.

## 12.5 Critérios de aceite

- O dashboard deve exibir indicadores atualizados conforme dados cadastrados.
- Contratos vencendo devem aparecer como alerta.
- Parcelas vencidas devem impactar o indicador de inadimplência.
- Repasses pendentes devem ser destacados para o financeiro.

---

# 13. Módulo de Imóveis

## 13.1 Objetivo

Permitir o cadastro e gerenciamento dos imóveis administrados pela imobiliária.

## 13.2 Funcionalidades

- Criar imóvel.
- Editar imóvel.
- Visualizar imóvel.
- Excluir logicamente imóvel.
- Restaurar imóvel, conforme permissão.
- Upload de fotos.
- Upload de documentos.
- Filtrar imóveis por status, proprietário, tipo, bairro e valor.

## 13.3 Dados mínimos

- Código interno.
- Tipo.
- Finalidade.
- Proprietário.
- Endereço.
- Bairro.
- Cidade.
- Estado.
- CEP.
- Valor de aluguel.
- Status.
- Observações.

## 13.4 Status

- disponivel.
- reservado.
- alugado.
- inativo.

## 13.5 Regras de negócio

- Um imóvel pertence a um proprietário.
- Um imóvel pode possuir vários contratos ao longo do tempo.
- Apenas imóveis com status disponível podem ser selecionados para novo contrato.
- Um imóvel alugado não pode ser vinculado a outro contrato ativo.
- Imóvel com contrato ativo deve estar com status alugado.
- Imoveis não devem possuir campos de encargos, esses pertencem ao contrato.

## 13.6 Tela de listagem

### Filtros

- Busca geral.
- Código.
- Tipo.
- Proprietário.
- Bairro.
- Cidade.
- Status.
- Valor mínimo e máximo.

### Colunas

- Código.
- Foto principal.
- Tipo.
- Endereço.
- Proprietário.
- Valor aluguel.
- Status.
- Ações.

## 13.7 Critérios de aceite

- Dado um imóvel cadastrado, quando for listado, então deve aparecer com status visual.
- Dado um imóvel alugado, quando o usuário criar contrato, então ele não deve aparecer para seleção.
- Dado um imóvel excluído logicamente, quando listar imóveis ativos, então ele não deve aparecer.
- Dado um imóvel com fotos, quando visualizar detalhes, então as imagens devem ser exibidas.

---
# Modulos de clientes
Fazem parte do modulo de clientes o modulo de proprietarios e inquilinos, eles sao definidos apenas pelo tipo

# 14. Módulo de Proprietários

## 14.1 Objetivo

Gerenciar os proprietários dos imóveis administrados.

## 14.2 Funcionalidades

- Criar proprietário.
- Editar proprietário.
- Visualizar proprietário.
- Excluir logicamente proprietário.
- Consultar imóveis vinculados.
- Consultar contratos vinculados indiretamente.
- Consultar repasses.

## 14.3 Dados mínimos

- Nome.
- CPF/CNPJ.
- Telefone.
- WhatsApp.
- Email.
- Endereço.
- Dados bancários.
- Observações.

## 14.4 Regras de negócio

- Um proprietário pode possuir vários imóveis.
- CPF/CNPJ deve ser único, salvo regra administrativa futura.
- Dados bancários devem ser usados para controle de repasses.
- Proprietário com imóveis vinculados não deve ser excluído fisicamente.

## 14.5 Critérios de aceite

- Dado um proprietário cadastrado, quando acessar detalhes, então deve listar os imóveis vinculados.
- Dado um proprietário com repasses pendentes, quando acessar detalhes, então os repasses devem estar visíveis.
- Dado um CPF/CNPJ já cadastrado, quando tentar cadastrar novamente, então o sistema deve alertar duplicidade.
- Dado um CNPJ, o sistema  deve ter um serviço que consulte os dados desse documento em alguma API de consulta publica de CNPJ

---

# 15. Módulo de Inquilinos

## 15.1 Objetivo

Gerenciar os inquilinos vinculados aos contratos de locação.

## 15.2 Funcionalidades

- Criar inquilino.
- Editar inquilino.
- Visualizar inquilino.
- Excluir logicamente inquilino.
- Consultar histórico de contratos.
- Selecionar ou cadastrar inquilino durante o wizard de contrato.

## 15.3 Dados mínimos

- Nome.
- CPF.
- RG.
- Telefone.
- WhatsApp.
- Email.
- Profissão.
- Renda.
- Endereço.
- Observações.

## 15.4 Regras de negócio

- Um inquilino pode possuir vários contratos ao longo do tempo.
- CPF deve ser único, salvo regra administrativa futura.
- O histórico de contratos deve ser preservado.

## 15.5 Critérios de aceite

- Dado um inquilino existente, quando criar contrato, então deve ser possível selecioná-lo.
- Dado um inquilino sem cadastro, quando criar contrato, então deve ser possível cadastrá-lo no fluxo.
- Dado um inquilino com contrato anterior, quando visualizar detalhes, então o histórico deve aparecer.

---

# 16. Módulo de Corretores

## 16.1 Objetivo

Gerenciar corretores vinculados aos contratos.

## 16.2 Funcionalidades

- Criar corretor.
- Editar corretor.
- Visualizar corretor.
- Excluir logicamente corretor.
- Vincular corretor a contrato.

## 16.3 Dados mínimos

- Nome.
- CPF.
- CRECI.
- Telefone.
- Email.
- Percentual de comissão padrão.
- Status.

## 16.4 Regras de negócio

- Corretor pode ser opcional em contratos de locação.
- Percentual de comissão padrão deve servir como sugestão, não como regra obrigatória no MVP.
- CRECI deve ser armazenado quando informado.

## 16.5 Critérios de aceite

- Dado um corretor ativo, quando criar contrato, então ele deve estar disponível para seleção.
- Dado um corretor inativo, quando criar contrato, então ele não deve aparecer como opção padrão.

---

# 17. Módulo de Contratos de Locação

## 17.1 Objetivo

Permitir que a imobiliária cadastre, acompanhe e gerencie contratos de locação de forma integrada ao financeiro, às parcelas, à caução e aos repasses.

## 17.2 Entidades relacionadas

O contrato conecta:

- Imóvel.
- Proprietário.
- Inquilino.
- Corretor.
- Encargos.
- Caução.
- Parcelas de aluguel.
- Recebimentos.
- Multas.
- Repasses ao proprietário.
- Movimentações financeiras.

## 17.3 Status do contrato

- rascunho.
- ativo.
- vencido.
- encerrado.
- cancelado.

## 17.4 Regras por status

### Rascunho

- Não gera parcelas.
- Não altera o status do imóvel.
- Permite edição completa.

### Ativo

- Imóvel muda para alugado.
- Gera parcelas de aluguel, se configurado.
- Permite registro de pagamentos.
- Permite geração de repasses.

### Vencido

- Contrato passou da data final e ainda não foi renovado ou encerrado.
- Deve aparecer como alerta no dashboard.
- Permite renovação ou encerramento.

### Encerrado

- Mantém histórico financeiro.
- Pode cancelar parcelas futuras.
- Imóvel pode voltar para disponível ou inativo.

### Cancelado

- Deve exigir motivo.
- Não deve apagar histórico.
- Pode cancelar parcelas futuras.

## 17.5 Wizard de cadastro

O cadastro deve ser feito em página completa com wizard.

### Etapas

1. Imóvel.
2. Inquilino.
3. Dados do contrato.
4. Multas e regras.
5. Encargos.
6. Financeiro e Caução.
7. Revisão.

## 17.6 Etapa 1 — Imóvel

### Objetivo

Selecionar o imóvel que será alugado.

### Regras

- Listar apenas imóveis disponíveis.
- Não permitir seleção de imóvel alugado, reservado ou inativo.
- Exibir dados resumidos do imóvel.
- Exibir proprietário vinculado ao imóvel.

### Campos exibidos

- Código do imóvel.
- Tipo do imóvel.
- Endereço.
- Bairro.
- Cidade.
- Proprietário.
- Valor sugerido de aluguel.
- Status.
- Fotos do imóvel.

### Filtros

- Código.
- Endereço.
- Proprietário.
- Tipo.
- Bairro.
- Valor máximo.

## 17.7 Etapa 2 — Inquilino

### Objetivo

Selecionar o inquilino do contrato.

### Campos exibidos

- Nome.
- CPF.
- RG.
- Telefone.
- WhatsApp.
- Email.
- Profissão.
- Renda.

## 17.8 Etapa 3 — Dados do contrato

### Campos

- Data de início.
- Data de fim.
- Prazo em meses.
- Dia de vencimento.
- Valor do aluguel.
- Corretor responsável.
- Observações.

### Regras

- Data final deve ser maior que data inicial.
- Dia de vencimento deve estar entre 1 e 31.
- Valor do aluguel deve ser maior que zero.
- Corretor pode ser opcional.
- Proprietário do contrato deve ser o proprietário atual do imóvel.

## 17.9 Etapa 4 — Multas e regras

### Multa por atraso

Campos:

- Aplicar multa por atraso.
- Percentual da multa por atraso.
- Aplicar juros por atraso.
- Percentual de juros mensal.
- Dias de tolerância.
- Observações.

Cálculo:

```text
Multa por atraso = valor do aluguel x percentual da multa
Juros proporcional = valor do aluguel x percentual mensal / 30 x dias em atraso
Valor total = aluguel + multa + juros + encargos - descontos
```

### Multa por rescisão antecipada

Campos:

- Aplicar multa por rescisão antecipada.
- Tipo da multa de rescisão.
- Quantidade de aluguéis da multa.
- Valor fixo da multa.
- Calcular proporcional ao tempo restante.
- Observações.

Tipos:

- quantidade_alugueis.
- valor_fixo.

Cálculo proporcional:

```text
Multa cheia = valor do aluguel x quantidade de aluguéis
Multa proporcional = multa cheia x meses restantes / meses totais do contrato
```

## 17.10 Etapa 5 — Encargos contratuais

### Objetivo

Definir a responsabilidade por cada encargo vinculado ao contrato.

### Encargos padrão

- IPTU.
- Condomínio.
- Água.
- Energia.
- Seguro.
- Outro.

### Responsáveis possíveis

- locador.
- locatario.
- incluso_no_aluguel.

### Campos

- Tipo do encargo.
- Responsável.
- Cobrar junto ao aluguel.
- Valor estimado.
- Observações.

### Regras

- Encargos devem ficar vinculados ao contrato.
- Encargos não devem compor automaticamente a receita da imobiliária.
- Encargos cobrados junto ao aluguel devem aparecer na parcela.
- Encargos de terceiros devem ser separados da taxa da imobiliária.

## 17.11 Etapa 6 — Financeiro e Caução

### Financeiro mensal

Campos:

- Valor do aluguel.
- Percentual da taxa de administração.
- Valor da taxa de administração.
- Valor previsto de repasse ao proprietário.
- Gerar parcelas automaticamente.
- Primeiro vencimento.
- Quantidade de parcelas.

Regra de cálculo:

```text
Valor da taxa de administração = valor do aluguel x percentual da taxa / 100
Valor de repasse ao proprietário = valor do aluguel - valor da taxa de administração
```

### Caução / garantia

Regra principal:

- Caução não é aluguel.
- Caução não é receita operacional da imobiliária.
- Caução não gera taxa de administração automaticamente.
- Caução não gera repasse mensal automaticamente.
- Caução deve permanecer vinculada ao contrato.

Tipos de garantia:

- caucao_dinheiro.
- fiador.
- seguro_fianca.
- titulo_capitalizacao.
- sem_garantia.
- outro.

Campos:

- Possui caução.
- Tipo de garantia.
- Valor da caução.
- Quantidade de aluguéis.
- Data de recebimento.
- Forma de recebimento.
- Responsável pela guarda.
- Status da caução.
- Observações.

Responsável pela guarda:

- imobiliaria.
- proprietario.
- terceiro.

Status da caução:

- nao_aplicavel.
- aguardando_recebimento.
- recebida.
- devolvida.
- abatida.
- retida_parcialmente.
- retida_integralmente.
- cancelada.

## 17.12 Etapa 7 - Anexos 

Nesta etapa o usuario pode anexar arquivos, tais como o pdf de um contrato ou os aditivos

- Pode anexar varios arquivos cada arquivo com no maximo 10MB.
- O usuario pode dar um nome para cada arquivo, identificando cada um como queira.
- Se deixar em branco o nome do arquivo o sistema coloca um nome aleatorio em forma de uuid;

## 17.13 Etapa 8 — Revisão

Blocos da revisão:

- Imóvel.
- Proprietário.
- Inquilino.
- Corretor.
- Dados do contrato.
- Multas e regras.
- Encargos.
- Financeiro mensal.
- Caução / Garantia.

Ações:

- Salvar como rascunho.
- Ativar contrato.
- Voltar.
- Cancelar.

## 17.14 Ativação do contrato

Ao ativar o contrato, o sistema deve:

1. Validar se o imóvel ainda está disponível.
2. Alterar status do contrato para ativo.
3. Alterar status do imóvel para alugado.
4. Gerar parcelas de aluguel, se configurado.
5. Registrar histórico da ativação.
6. Criar registro de caução, se houver.
7. Criar lançamento financeiro da caução, se já recebida.

## 17.15 Critérios de aceite do módulo de contratos

- Dado um imóvel disponível, quando criar contrato, então ele deve poder ser selecionado.
- Dado um imóvel alugado, quando criar contrato, então ele não deve aparecer na seleção.
- Dado um contrato salvo como rascunho, então o imóvel não deve mudar de status.
- Dado um contrato ativado, então o imóvel deve mudar para alugado.
- Dado um contrato ativo com geração automática, então as parcelas devem ser criadas.
- Dado um contrato com caução, então o registro de caução deve ficar vinculado ao contrato.
- Dado um contrato cancelado, então o sistema deve exigir motivo.
- Dado um contrato vencido, então ele deve aparecer como alerta no dashboard.

---

# 18. Módulo de Parcelas de Aluguel

## 18.1 Objetivo

Controlar os valores mensais previstos e pagos de cada contrato ativo.

## 18.2 Regras

- Parcelas devem ser geradas conforme data de início, data de fim e dia de vencimento.
- Contrato em rascunho não gera parcelas.
- Parcela deve possuir mês e ano de referência.
- Parcela deve armazenar aluguel, encargos, multa, juros, desconto e total.
- Parcelas futuras podem ser canceladas em caso de rescisão.

## 18.3 Status da parcela

- pendente.
- pago.
- vencido.
- cancelado.
- pago_parcial.

## 18.4 Campos principais

- Contrato de locação.
- Mês referência.
- Ano referência.
- Data de vencimento.
- Valor do aluguel.
- Valor dos encargos.
- Valor da multa por atraso.
- Valor dos juros por atraso.
- Valor de desconto.
- Valor total.
- Valor pago.
- Data do pagamento.
- Forma de pagamento.
- Status.
- Observações.

## 18.5 Critérios de aceite

- Dado um contrato ativo, quando gerar parcelas, então cada mês deve possuir uma parcela.
- Dado uma parcela vencida e não paga, então seu status deve indicar vencimento.
- Dado um pagamento com atraso, então o sistema deve calcular multa e juros quando configurados.
- Dado uma rescisão, então parcelas futuras podem ser canceladas conforme escolha do usuário.

---

# 19. Módulo de Pagamentos

## 19.1 Objetivo

Registrar pagamentos de aluguel e integrar automaticamente com financeiro e repasses.

## 19.2 Fluxo

```text
Usuário seleciona uma parcela
↓
Sistema calcula atraso, multa e juros
↓
Usuário informa data e forma de pagamento
↓
Sistema calcula total
↓
Usuário confirma pagamento
↓
Sistema registra entrada financeira
↓
Sistema gera repasse pendente ao proprietário
↓
Sistema atualiza status da parcela
```

## 19.3 Campos

- Parcela.
- Data do pagamento.
- Forma de pagamento.
- Valor do aluguel.
- Valor dos encargos.
- Valor da multa.
- Valor dos juros.
- Valor do desconto.
- Valor total.
- Valor pago.
- Observação.

## 19.4 Formas de pagamento

- pix.
- dinheiro.
- cartao_credito.
- cartao_debito.
- transferencia.
- boleto.
- outro.

## 19.5 Regras

- Ao registrar pagamento, criar entrada financeira.
- Ao registrar pagamento, calcular taxa da imobiliária.
- Ao registrar pagamento, gerar repasse pendente ao proprietário.
- Encargos, caução, multas e valores de terceiros não devem ser misturados automaticamente ao repasse sem regra explícita.
- Pagamento parcial deve alterar status para pago_parcial.

## 19.6 Critérios de aceite

- Dado uma parcela pendente, quando registrar pagamento integral, então o status deve mudar para pago.
- Dado um pagamento inferior ao total, então o status deve mudar para pago_parcial.
- Dado pagamento de aluguel, então deve ser criada movimentação financeira de entrada.
- Dado pagamento de aluguel, então deve ser gerado repasse pendente ao proprietário.

---

# 20. Módulo de Repasses aos Proprietários

## 20.1 Objetivo

Controlar os valores líquidos a serem repassados aos proprietários após recebimento de aluguel e desconto da taxa da imobiliária.

## 20.2 Cálculo

```text
Valor bruto = valor recebido de aluguel
Taxa de administração = valor bruto x percentual da taxa / 100
Valor líquido = valor bruto - taxa de administração
```

## 20.3 Status do repasse

- pendente.
- pago.
- cancelado.

## 20.4 Campos

- Contrato de locação.
- Imóvel.
- Proprietário.
- Parcela de aluguel.
- Valor bruto.
- Valor da taxa de administração.
- Valor líquido.
- Status.
- Data de pagamento.
- Forma de pagamento.
- Observações.

## 20.5 Regras

- Repasse deve ser gerado ao registrar pagamento de aluguel.
- Repasse deve iniciar como pendente.
- Pagamento do repasse deve registrar saída financeira.
- Repasse não deve incluir caução automaticamente.
- Repasse não deve incluir encargos de terceiros sem regra explícita.

## 20.6 Critérios de aceite

- Dado um pagamento registrado, então deve existir repasse pendente vinculado.
- Dado um repasse pago, então deve existir saída financeira correspondente.
- Dado um repasse cancelado, então deve exigir justificativa.

---

# 21. Módulo de Caução e Garantias

## 21.1 Objetivo

Controlar garantias e movimentações de caução vinculadas ao contrato de locação.

## 21.2 Regras principais

- Caução não é aluguel.
- Caução não é receita operacional da imobiliária.
- Caução não gera taxa de administração automaticamente.
- Caução não gera repasse mensal automaticamente.
- Caução deve permanecer vinculada ao contrato.
- Toda movimentação de caução deve ter histórico.

## 21.3 Recebimento da caução

Fluxo:

```text
Contrato ativo ou em ativação
↓
Usuário registra recebimento da caução
↓
Sistema cria lançamento financeiro do tipo entrada
↓
Categoria: caução
↓
Sistema atualiza status da caução para recebida
```

## 21.4 Devolução da caução

Fluxo:

```text
Contrato encerrado ou em rescisão
↓
Usuário informa valor a devolver
↓
Sistema registra movimentação da caução
↓
Sistema cria lançamento financeiro do tipo saída
↓
Sistema atualiza status da caução
```

## 21.5 Retenção da caução

- Usuário informa valor retido.
- Usuário informa motivo da retenção.
- Sistema registra movimentação.
- Sistema atualiza saldo da caução.

## 21.6 Abatimento da caução

- Usuário informa valor a abater.
- Usuário seleciona débito a ser abatido.
- Sistema registra movimentação.
- Sistema reduz saldo da caução.

## 21.7 Tipos de movimentação

- recebimento.
- devolucao.
- abatimento.
- retencao_parcial.
- retencao_integral.
- ajuste.

## 21.8 Critérios de aceite

- Dado um contrato com caução aguardando recebimento, quando registrar recebimento, então o status deve mudar para recebida.
- Dado uma caução recebida, quando registrar devolução total, então o status deve mudar para devolvida.
- Dado uma retenção parcial, então o saldo da caução deve ser recalculado.
- Dado qualquer movimentação de caução, então deve haver histórico com data, valor, tipo e usuário.

---

# 22. Módulo de Rescisão de Contrato

## 22.1 Objetivo

Permitir encerramento antecipado de contrato com cálculo de multa, análise de débitos, uso de caução e atualização do imóvel.

## 22.2 Campos

- Data da rescisão.
- Solicitado por.
- Motivo.
- Destino do imóvel.
- Ação sobre parcelas futuras.
- Valor da multa.
- Valor do desconto.
- Valor final da multa.
- Valor da caução recebida.
- Débitos em aberto.
- Valor a reter da caução.
- Valor a abater com caução.
- Valor a devolver da caução.
- Observações.

## 22.3 Solicitado por

- locatario.
- locador.
- imobiliaria.
- acordo.

## 22.4 Destino do imóvel

- disponivel.
- inativo.

## 22.5 Ação sobre parcelas futuras

- cancelar_parcelas_futuras.
- manter_parcelas_futuras.

## 22.6 Regras

- Sistema deve calcular multa de rescisão, se configurada.
- Sistema deve verificar parcelas vencidas em aberto.
- Sistema deve permitir usar caução para abater débitos.
- Sistema deve permitir retenção parcial ou integral da caução.
- Sistema deve permitir devolução do saldo da caução.
- Contrato deve mudar para encerrado.
- Imóvel deve mudar para disponível ou inativo.
- Parcelas futuras podem ser canceladas.
- Histórico financeiro deve ser preservado.

## 22.7 Critérios de aceite

- Dado um contrato ativo, quando rescindir, então deve exigir motivo.
- Dado uma multa configurada, quando rescindir, então o valor deve ser calculado.
- Dado caução recebida, quando rescindir, então deve permitir reter, abater ou devolver.
- Dado rescisão confirmada, então o contrato deve mudar para encerrado.
- Dado rescisão confirmada, então o imóvel deve mudar para disponível ou inativo conforme escolha.

---

# 23. Módulo de Renovação de Contrato

## 23.1 Objetivo

Criar nova vigência para contrato existente, preservando histórico do contrato original.

## 23.2 Campos

- Nova data de início.
- Nova data de fim.
- Novo valor do aluguel.
- Novo percentual da taxa de administração.
- Manter encargos anteriores.
- Manter regras de multa.
- Manter caução anterior.
- Gerar novas parcelas.
- Observações.

## 23.3 Regras

- Renovação deve preservar histórico do contrato anterior.
- Sistema pode criar novo contrato vinculado ao contrato original.
- Sistema deve permitir reajuste de valor.
- Sistema deve permitir manter ou alterar encargos.
- Sistema deve permitir manter ou alterar regras de multa.
- Caução pode ser mantida, devolvida ou complementada.

## 23.4 Critérios de aceite

- Dado um contrato vencido, quando renovar, então deve criar nova vigência.
- Dado renovação com novo valor, então as novas parcelas devem considerar o novo aluguel.
- Dado renovação mantendo encargos, então os encargos devem ser copiados para o novo contrato.
- Dado contrato renovado, então o histórico do contrato original deve permanecer acessível.

---

# 24. Módulo Financeiro

## 24.1 Objetivo

Controlar entradas, saídas, receitas, despesas, caução e repasses.

## 24.2 Entradas

- Recebimento de aluguel.
- Receitas diversas.
- Recebimento de caução.
- Multas recebidas.
- Juros recebidos.

## 24.3 Saídas

- Despesas operacionais.
- Despesas administrativas.
- Repasses.
- Devolução de caução.
- Ajustes financeiros.

## 24.4 Categorias iniciais

### Entradas

- aluguel.
- caucao.
- multa.
- juros.
- receita_diversa.

### Saídas

- repasse_proprietario.
- despesa_operacional.
- despesa_administrativa.
- devolucao_caucao.
- ajuste.

## 24.5 Regras

- Recebimento de aluguel deve gerar entrada.
- Pagamento de repasse deve gerar saída.
- Recebimento de caução deve gerar entrada separada por categoria caução.
- Devolução de caução deve gerar saída separada por categoria caução.
- Encargos de terceiros devem ser separados da receita da imobiliária.
- Receita da imobiliária é a taxa de administração, salvo receita diversa lançada manualmente.

## 24.6 Critérios de aceite

- Dado pagamento de aluguel, então deve haver entrada financeira correspondente.
- Dado repasse pago, então deve haver saída financeira correspondente.
- Dado recebimento de caução, então a entrada deve usar categoria caução.
- Dado relatório financeiro, então deve separar receitas da imobiliária de valores de terceiros.

---

# 25. Módulo de Usuários, Perfis e Permissões

## 25.1 Objetivo

Controlar acesso ao sistema por perfil e permissões granulares.

## 25.2 Usuários

Dados:

- Nome.
- Email.
- Senha.
- Perfil.
- Status.
- Primeiro acesso pendente.

## 25.3 Perfis padrão

- Administrador.
- Financeiro.
- Corretor.
- Atendente.

## 25.4 Permissões

Permissões por módulo:

- Visualizar.
- Criar.
- Editar.
- Excluir.
- Restaurar.
- Exportar.
- Registrar pagamento.
- Marcar repasse como pago.
- Movimentar caução.
- Rescindir contrato.
- Renovar contrato.

## 25.5 Matriz sugerida

| Módulo | Administrador | Financeiro | Atendente | Corretor |
|---|---:|---:|---:|---:|
| Dashboard | Total | Parcial | Parcial | Parcial |
| Imóveis | Total | Visualizar | Criar/Editar | Visualizar |
| Proprietários | Total | Visualizar | Criar/Editar | Visualizar limitado |
| Inquilinos | Total | Visualizar | Criar/Editar | Visualizar limitado |
| Corretores | Total | Visualizar | Visualizar | Visualizar próprio |
| Contratos | Total | Visualizar | Criar/Editar | Visualizar vinculados |
| Pagamentos | Total | Total | Visualizar | Sem acesso |
| Repasses | Total | Total | Sem acesso | Sem acesso |
| Caução | Total | Total | Visualizar | Sem acesso |
| Financeiro | Total | Total | Sem acesso | Sem acesso |
| Usuários | Total | Sem acesso | Sem acesso | Sem acesso |
| Relatórios | Total | Total | Parcial | Parcial |

## 25.6 Critérios de aceite

- Dado um usuário financeiro, quando acessar pagamentos, então deve conseguir registrar recebimentos.
- Dado um corretor, quando acessar financeiro, então o acesso deve ser negado.
- Dado um administrador, quando acessar usuários, então deve conseguir criar e editar usuários.
- Dado um usuário criado, então ele deve receber acesso inicial por email.

---

# 26. Módulo de Notificações

## 26.1 Objetivo

Enviar notificações por email para eventos importantes da operação.

## 26.2 Tipos iniciais

- Vencimento próximo.
- Cobrança vencida.
- Confirmação de pagamento.
- Primeiro acesso de usuário.
- Recuperação de senha.

## 26.3 Regras

- Notificações devem ser enviadas por email no MVP.
- A estrutura deve permitir futura integração com WhatsApp.
- Scheduler deve identificar vencimentos próximos e cobranças vencidas.
- Queue deve processar envios assíncronos.

## 26.4 Critérios de aceite

- Dado uma parcela próxima ao vencimento, então o sistema deve poder enviar email ao inquilino.
- Dado uma parcela vencida, então o sistema deve poder enviar email de cobrança.
- Dado pagamento confirmado, então o sistema deve poder enviar confirmação.
- Dado novo usuário, então deve receber email de primeiro acesso.

---

# 27. Módulo de Relatórios

## 27.1 Objetivo

Permitir análise básica da operação e do financeiro.

## 27.2 Relatórios do MVP

### Imóveis

- Disponíveis.
- Alugados.
- Por proprietário.
- Por status.

### Contratos

- Ativos.
- Encerrados.
- Vencidos.
- Vencendo.

### Financeiro

- Receitas.
- Despesas.
- Fluxo de caixa.
- Inadimplência.

### Repasses

- Pendentes.
- Pagos.
- Por proprietário.

### Caução

- Aguardando recebimento.
- Recebidas.
- Devolvidas.
- Retidas.

## 27.3 Filtros gerais

- Período.
- Proprietário.
- Imóvel.
- Contrato.
- Status.

## 27.4 Critérios de aceite

- Dado um período selecionado, quando gerar relatório financeiro, então deve exibir receitas e despesas do período.
- Dado filtro de proprietário, quando gerar relatório de repasses, então deve listar apenas repasses daquele proprietário.
- Dado relatório de imóveis alugados, então deve listar imóveis com status alugado.

---

# 28. Telas do Sistema

## 28.1 Autenticação

- Login.
- Recuperação de senha.
- Troca de senha no primeiro acesso.

## 28.2 Dashboard

- Cards de indicadores.
- Gráficos.
- Alertas de contratos vencendo.
- Alertas de parcelas vencidas.
- Alertas de repasses pendentes.

## 28.3 Imóveis

- Listagem.
- Cadastro em wizard ou página completa.
- Edição.
- Detalhes.
- Upload de fotos e documentos.

## 28.4 Proprietários

- Listagem.
- Cadastro.
- Edição.
- Detalhes com imóveis e repasses.

## 28.5 Inquilinos

- Listagem.
- Cadastro.
- Edição.
- Detalhes com histórico de contratos.

## 28.6 Corretores

- Listagem.
- Cadastro.
- Edição.
- Detalhes.

## 28.7 Contratos

### Listagem

Filtros:

- Busca geral.
- Status.
- Proprietário.
- Inquilino.
- Imóvel.
- Corretor.
- Período de início.
- Período de fim.
- Dia de vencimento.
- Contratos vencidos.
- Contratos vencendo.

Colunas:

- Código.
- Imóvel.
- Inquilino.
- Proprietário.
- Valor do aluguel.
- Vencimento.
- Data de início.
- Data de fim.
- Status.
- Ações.

Ações:

- Visualizar.
- Editar.
- Registrar pagamento.
- Renovar.
- Encerrar.
- Cancelar.
- Excluir.

### Cadastro

- Wizard com 7 etapas.

### Detalhes

Usar tabs:

- Resumo.
- Parcelas.
- Encargos.
- Caução.
- Repasses.
- Documentos.
- Histórico.

## 28.8 Financeiro

- Listagem de movimentações.
- Cadastro de receita diversa.
- Cadastro de despesa.
- Filtros por tipo, categoria e período.

## 28.9 Repasses

- Listagem de repasses.
- Filtros por proprietário, status e período.
- Marcar como pago.
- Visualizar vínculo com parcela e contrato.

## 28.10 Usuários e permissões

- Listagem de usuários.
- Cadastro de usuário.
- Edição de usuário.
- Gestão de perfis.
- Gestão de permissões.

---

# 29. Modelo de Dados Sugerido

## 29.1 Tabelas principais

- usuarios.
- perfis.
- permissoes.
- perfil_permissoes.
- proprietarios.
- inquilinos.
- corretores.
- imoveis.
- fotos_imovel.
- documentos_imovel.
- contratos_locacao.
- encargos_contrato.
- parcelas_aluguel.
- repasses_proprietarios.
- caucoes_contrato.
- movimentacoes_caucao.
- rescisoes_contrato.
- renovacoes_contrato.
- movimentacoes_financeiras.
- documentos_contrato.
- historicos_contrato.
- notificacoes.

## 29.2 usuarios

```text
id
uuid
nome
email
senha
perfil_id
status
primeiro_acesso_pendente
ultimo_login_em
created_at
updated_at
deleted_at
```

## 29.3 perfis

```text
id
uuid
nome
descricao
status
created_at
updated_at
deleted_at
```

## 29.4 permissoes

```text
id
uuid
modulo
acao
nome
descricao
created_at
updated_at
```

## 29.5 proprietarios

```text
id
uuid
nome
cpf_cnpj
telefone
whatsapp
email
cep
endereco
numero
complemento
bairro
cidade
estado
banco
agencia
conta
tipo_conta
chave_pix
observacoes
created_at
updated_at
deleted_at
```

## 29.6 inquilinos

```text
id
uuid
nome
cpf
rg
telefone
whatsapp
email
profissao
renda
cep
endereco
numero
complemento
bairro
cidade
estado
observacoes
created_at
updated_at
deleted_at
```

## 29.7 corretores

```text
id
uuid
nome
cpf
creci
telefone
email
percentual_comissao_padrao
status
observacoes
created_at
updated_at
deleted_at
```

## 29.8 imoveis

```text
id
uuid
codigo_interno
tipo
finalidade
proprietario_id
cep
endereco
numero
complemento
bairro
cidade
estado
valor_aluguel
valor_venda
status
observacoes
created_at
updated_at
deleted_at
```

## 29.9 contratos_locacao

```text
id
uuid
codigo
imovel_id
proprietario_id
inquilino_id
corretor_id
contrato_anterior_id
data_inicio
data_fim
dia_vencimento
valor_aluguel
percentual_taxa_administracao
valor_taxa_administracao
valor_repasse_proprietario
aplicar_multa_atraso
percentual_multa_atraso
aplicar_juros_atraso
percentual_juros_mensal_atraso
dias_tolerancia_atraso
aplicar_multa_rescisao
tipo_multa_rescisao
quantidade_alugueis_multa_rescisao
valor_fixo_multa_rescisao
multa_rescisao_proporcional
status
observacoes
criado_por
created_at
updated_at
deleted_at
```

## 29.10 encargos_contrato

```text
id
uuid
contrato_locacao_id
tipo_encargo
responsavel
valor_estimado
cobrar_junto_aluguel
observacoes
created_at
updated_at
deleted_at
```

## 29.11 parcelas_aluguel

```text
id
uuid
contrato_locacao_id
mes_referencia
ano_referencia
data_vencimento
valor_aluguel
valor_encargos
valor_multa_atraso
valor_juros_atraso
valor_desconto
valor_total
valor_pago
data_pagamento
forma_pagamento
status
observacoes
created_at
updated_at
deleted_at
```

## 29.12 repasses_proprietarios

```text
id
uuid
contrato_locacao_id
imovel_id
proprietario_id
parcela_aluguel_id
valor_bruto
valor_taxa_administracao
valor_liquido
status
data_pagamento
forma_pagamento
observacoes
created_at
updated_at
deleted_at
```

## 29.13 caucoes_contrato

```text
id
uuid
contrato_locacao_id
tipo_garantia
valor_caucao
quantidade_alugueis
data_recebimento
forma_recebimento
responsavel_guarda
status
observacoes
created_at
updated_at
deleted_at
```

## 29.14 movimentacoes_caucao

```text
id
uuid
caucao_contrato_id
tipo_movimentacao
valor
data_movimentacao
forma_movimentacao
descricao
criado_por
created_at
updated_at
```

## 29.15 rescisoes_contrato

```text
id
uuid
contrato_locacao_id
data_rescisao
motivo
solicitado_por
meses_restantes
valor_multa_rescisao
valor_desconto
valor_final_multa
valor_caucao_recebida
valor_caucao_retida
valor_caucao_abatida
valor_caucao_devolvida
destino_imovel
acao_parcelas_futuras
observacoes
criado_por
created_at
updated_at
```

## 29.16 renovacoes_contrato

```text
id
uuid
contrato_original_id
novo_contrato_id
data_renovacao
valor_aluguel_anterior
valor_aluguel_novo
data_inicio_anterior
data_fim_anterior
nova_data_inicio
nova_data_fim
manter_encargos
manter_regras_multa
manter_caucao
gerar_novas_parcelas
observacoes
criado_por
created_at
updated_at
```

## 29.17 movimentacoes_financeiras

```text
id
uuid
tipo
categoria
descricao
valor
data_movimentacao
forma_pagamento
contrato_locacao_id
parcela_aluguel_id
repasse_proprietario_id
caucao_contrato_id
proprietario_id
inquilino_id
status
criado_por
created_at
updated_at
deleted_at
```

## 29.18 historicos_contrato

```text
id
uuid
contrato_locacao_id
tipo_evento
descricao
dados_anteriores
dados_novos
criado_por
created_at
```

---

# 30. Rotas Laravel Sugeridas

```php
Route::resource('imoveis', ImovelController::class);
Route::resource('proprietarios', ProprietarioController::class);
Route::resource('inquilinos', InquilinoController::class);
Route::resource('corretores', CorretorController::class);
Route::resource('contratos-locacao', ContratoLocacaoController::class);
Route::resource('usuarios', UsuarioController::class);
Route::resource('perfis', PerfilController::class);

Route::post('contratos-locacao/{contrato}/ativar', [ContratoLocacaoController::class, 'ativar'])
    ->name('contratos-locacao.ativar');

Route::post('contratos-locacao/{contrato}/parcelas/{parcela}/pagamento', [PagamentoAluguelController::class, 'store'])
    ->name('contratos-locacao.parcelas.pagamento');

Route::post('contratos-locacao/{contrato}/rescindir', [RescisaoContratoController::class, 'store'])
    ->name('contratos-locacao.rescindir');

Route::post('contratos-locacao/{contrato}/renovar', [RenovacaoContratoController::class, 'store'])
    ->name('contratos-locacao.renovar');

Route::post('contratos-locacao/{contrato}/caucao/movimentacoes', [MovimentacaoCaucaoController::class, 'store'])
    ->name('contratos-locacao.caucao.movimentacoes');

Route::post('repasses-proprietarios/{repasse}/marcar-como-pago', [RepasseProprietarioController::class, 'marcarComoPago'])
    ->name('repasses-proprietarios.marcar-como-pago');
```

---

# 31. Estrutura Vue/Inertia Sugerida

## 31.1 Páginas

```text
resources/js/Pages/Auth/Login.vue
resources/js/Pages/Auth/ForgotPassword.vue
resources/js/Pages/Auth/FirstAccessChangePassword.vue

resources/js/Pages/Dashboard.vue

resources/js/Pages/Imoveis/Index.vue
resources/js/Pages/Imoveis/Create.vue
resources/js/Pages/Imoveis/Edit.vue
resources/js/Pages/Imoveis/Show.vue

resources/js/Pages/Proprietarios/Index.vue
resources/js/Pages/Proprietarios/Create.vue
resources/js/Pages/Proprietarios/Edit.vue
resources/js/Pages/Proprietarios/Show.vue

resources/js/Pages/Inquilinos/Index.vue
resources/js/Pages/Inquilinos/Create.vue
resources/js/Pages/Inquilinos/Edit.vue
resources/js/Pages/Inquilinos/Show.vue

resources/js/Pages/Corretores/Index.vue
resources/js/Pages/Corretores/Create.vue
resources/js/Pages/Corretores/Edit.vue
resources/js/Pages/Corretores/Show.vue

resources/js/Pages/ContratosLocacao/Index.vue
resources/js/Pages/ContratosLocacao/Create.vue
resources/js/Pages/ContratosLocacao/Edit.vue
resources/js/Pages/ContratosLocacao/Show.vue

resources/js/Pages/Financeiro/Index.vue
resources/js/Pages/Repasses/Index.vue
resources/js/Pages/Relatorios/Index.vue
resources/js/Pages/Usuarios/Index.vue
resources/js/Pages/Perfis/Index.vue
```

## 31.2 Componentes

```text
resources/js/Components/Common/DataTable.vue
resources/js/Components/Common/StatusBadge.vue
resources/js/Components/Common/ConfirmButton.vue
resources/js/Components/Common/FormMoneyInput.vue
resources/js/Components/Common/FormCpfCnpjInput.vue
resources/js/Components/Common/FormDateInput.vue
resources/js/Components/Common/PageHeader.vue
resources/js/Components/Common/EmptyState.vue

resources/js/Components/ContratosLocacao/ContratoWizard.vue
resources/js/Components/ContratosLocacao/Steps/StepImovel.vue
resources/js/Components/ContratosLocacao/Steps/StepInquilino.vue
resources/js/Components/ContratosLocacao/Steps/StepDadosContrato.vue
resources/js/Components/ContratosLocacao/Steps/StepMultas.vue
resources/js/Components/ContratosLocacao/Steps/StepEncargos.vue
resources/js/Components/ContratosLocacao/Steps/StepFinanceiroCaucao.vue
resources/js/Components/ContratosLocacao/Steps/StepRevisao.vue
resources/js/Components/ContratosLocacao/ContratoResumoCards.vue
resources/js/Components/ContratosLocacao/TabsContrato.vue
resources/js/Components/ContratosLocacao/TabelaParcelas.vue
resources/js/Components/ContratosLocacao/TabelaRepasses.vue
resources/js/Components/ContratosLocacao/AbaCaucao.vue
resources/js/Components/ContratosLocacao/ModalRegistrarPagamento.vue
resources/js/Components/ContratosLocacao/ModalRescisaoContrato.vue
resources/js/Components/ContratosLocacao/ModalRenovarContrato.vue
resources/js/Components/ContratosLocacao/ModalMovimentacaoCaucao.vue
```

---

# 32. Services Sugeridos

```text
UsuarioService
PrimeiroAcessoService
ImovelService
ProprietarioService
InquilinoService
CorretorService
ContratoLocacaoService
GerarParcelasContratoService
PagamentoAluguelService
RepasseProprietarioService
CalcularMultaAtrasoService
CalcularMultaRescisaoService
CaucaoContratoService
RescisaoContratoService
RenovacaoContratoService
MovimentacaoFinanceiraService
NotificacaoVencimentoService
DashboardService
RelatorioService
```

---

# 33. Regras de Negócio Consolidadas

1. Apenas imóveis disponíveis podem ser vinculados a contratos.
2. Contratos em rascunho não alteram status do imóvel.
3. Contratos ativos alteram o imóvel para alugado.
4. Um imóvel não pode possuir dois contratos ativos simultâneos.
5. Contrato ativo pode gerar parcelas automaticamente.
6. Multa por atraso deve considerar dias de tolerância.
7. Juros por atraso devem ser proporcionais aos dias de atraso.
8. Multa por rescisão pode ser proporcional ao tempo restante.
9. Encargos devem ficar vinculados ao contrato.
10. Encargos não são automaticamente receita da imobiliária.
11. Caução não é aluguel.
12. Caução não é receita operacional da imobiliária.
13. Caução não gera taxa de administração automaticamente.
14. Caução não gera repasse mensal automaticamente.
15. Caução pode ser recebida, devolvida, abatida ou retida.
16. Toda movimentação de caução deve ter histórico.
17. Ao registrar pagamento de aluguel, o sistema deve criar entrada financeira.
18. Ao registrar pagamento de aluguel, o sistema deve calcular taxa da imobiliária.
19. Ao registrar pagamento de aluguel, o sistema deve gerar repasse pendente ao proprietário.
20. Ao marcar repasse como pago, o sistema deve criar saída financeira.
21. Rescisão deve preservar histórico financeiro.
22. Renovação deve preservar histórico do contrato anterior.
23. Exclusões devem usar Soft Delete.
24. Usuários não podem se autocadastrar.
25. Novo usuário deve receber acesso inicial por email.
26. Usuário deve trocar senha no primeiro acesso.
27. Ações críticas devem respeitar permissões.
28. Dados financeiros devem ser rastreáveis por usuário e data.

---

# 34. Métricas de Sucesso do MVP

## 34.1 Operacionais

- Quantidade de imóveis cadastrados.
- Quantidade de contratos ativos.
- Quantidade de parcelas geradas automaticamente.
- Percentual de pagamentos registrados pelo sistema.
- Tempo médio para cadastrar contrato.

## 34.2 Financeiras

- Valor total recebido no mês.
- Valor total de repasses pendentes.
- Valor total de repasses pagos.
- Inadimplência por período.
- Receita da imobiliária por taxa de administração.

## 34.3 Produto

- Usuários ativos por semana.
- Módulos mais acessados.
- Erros em formulários críticos.
- Tempo médio para registrar pagamento.
- Volume de notificações enviadas.

---

# 35. Critérios Gerais de Aceite do MVP

O MVP será considerado funcional quando:

- Usuário administrador conseguir acessar o sistema.
- Administrador conseguir criar usuários e perfis.
- Usuário novo receber acesso inicial por email e trocar senha no primeiro acesso.
- Imóveis, proprietários, inquilinos e corretores puderem ser cadastrados.
- Contrato puder ser criado apenas com imóvel disponível.
- Contrato puder ser salvo como rascunho sem alterar status do imóvel.
- Contrato puder ser ativado e alterar imóvel para alugado.
- Parcelas puderem ser geradas automaticamente.
- Pagamento de aluguel puder ser registrado.
- Multa e juros por atraso puderem ser calculados.
- Repasse pendente puder ser gerado após pagamento.
- Repasse puder ser marcado como pago.
- Caução puder ser registrada e movimentada.
- Contrato puder ser rescindido.
- Contrato puder ser renovado.
- Dashboard exibir indicadores principais.
- Relatórios básicos puderem ser consultados.
- Permissões impedirem ações não autorizadas.
- Registros principais usarem Soft Delete.

---

# 36. Roadmap de Implementação

## 36.1 Fase 1 — Fundação

1. Setup Laravel, Vue, Inertia, TypeScript, Tailwind e DaisyUI.
2. Autenticação.
3. Usuários, perfis e permissões.
4. Layout base autenticado.
5. Componentes base de UI.

## 36.2 Fase 2 — Cadastros essenciais

1. Proprietários.
2. Inquilinos.
3. Corretores.
4. Imóveis.
5. Uploads de fotos e documentos.

## 36.3 Fase 3 — Contratos

1. Migrations das tabelas de contrato.
2. Models e relacionamentos.
3. Enums/status.
4. Listagem de contratos.
5. Wizard de cadastro.
6. Seleção apenas de imóveis disponíveis.
7. Dados básicos do contrato.
8. Multas e regras.
9. Encargos.
10. Financeiro e caução.
11. Ativação do contrato.
12. Geração de parcelas.

## 36.4 Fase 4 — Financeiro operacional

1. Registro de pagamento.
2. Cálculo de multa e juros.
3. Movimentação financeira de entrada.
4. Geração de repasses.
5. Marcar repasse como pago.
6. Movimentação financeira de saída.

## 36.5 Fase 5 — Caução, rescisão e renovação

1. Aba de caução.
2. Movimentações de caução.
3. Rescisão de contrato.
4. Renovação de contrato.
5. Histórico do contrato.

## 36.6 Fase 6 — Dashboard, notificações e relatórios

1. Dashboard com indicadores.
2. Notificações por email.
3. Scheduler de vencimentos.
4. Relatórios básicos.
5. Ajustes finais de UX.

---

# 37. Riscos e Mitigações

## 37.1 Risco: cálculo financeiro incorreto

**Mitigação:** centralizar cálculos em Services, criar testes unitários e registrar histórico dos valores calculados.

## 37.2 Risco: imóvel ser alugado em dois contratos ativos

**Mitigação:** validar status na seleção e novamente na ativação do contrato, usando transação no backend.

## 37.3 Risco: mistura entre receita da imobiliária, valores do proprietário e valores de terceiros

**Mitigação:** separar categorias financeiras e não incluir encargos/caução automaticamente em receita ou repasse sem regra explícita.

## 37.4 Risco: perda de histórico em rescisões ou renovações

**Mitigação:** nunca apagar contratos; usar status, tabelas de histórico e vínculo entre contrato original e novo contrato.

## 37.5 Risco: permissões inconsistentes

**Mitigação:** centralizar autorização em Policies e criar matriz de permissões clara.

---

# 38. Questões em Aberto

1. A imobiliária trabalhará com múltiplas filiais futuramente?
2. O código interno do imóvel será manual, automático ou ambos?
3. O código do contrato será sequencial automático?
4. Haverá validação real de CPF/CNPJ ou apenas formato no MVP?
5. O sistema deve permitir mais de um inquilino por contrato no futuro?
6. A caução em dinheiro ficará em conta da imobiliária ou apenas registrada como controle?
7. Multas e juros recebidos serão receita da imobiliária, do proprietário ou configuráveis?
8. Encargos cobrados junto ao aluguel serão repassados a quem?
9. Haverá comissão de corretor sobre locação no MVP ou apenas cadastro do percentual?
10. Os relatórios deverão ser exportados em PDF/Excel na primeira versão?

---

# 39. Definição de Pronto

Uma funcionalidade será considerada pronta quando:

- Possuir backend implementado com validações.
- Possuir autorização por Policy quando aplicável.
- Possuir frontend responsivo.
- Possuir mensagens de sucesso e erro.
- Possuir tratamento de estados vazios.
- Possuir paginação em listagens.
- Possuir filtros definidos no PRD.
- Possuir testes mínimos para regras críticas.
- Preservar histórico quando aplicável.
- Usar Soft Delete em entidades principais.
- Seguir nomes de tabelas em português, snake_case e sem acentos.

---

# 40. Conclusão

O MVP do Sistema de Gestão Imobiliária deve entregar uma base sólida para administração de imóveis de aluguel, com foco em contratos de locação, controle financeiro, repasses e caução. A prioridade é garantir que a operação diária da imobiliária seja centralizada, segura, rastreável e simples de usar.

A arquitetura proposta permite começar com um MVP funcional e evoluir posteriormente para integrações com assinatura digital, PIX, boletos, WhatsApp, portais externos, vistorias digitais e automações financeiras mais avançadas.

# Sistema de Gestão Imobiliária - Documento Base do Produto

## Visão Geral

O sistema tem como objetivo permitir que imobiliárias realizem a gestão completa de imóveis, proprietários, inquilinos, contratos de locação, recebimentos, repasses e controle financeiro.

O foco inicial é um MVP funcional que permita à imobiliária controlar toda a operação de administração de imóveis de aluguel através de uma plataforma web moderna, intuitiva e responsiva.

O sistema deverá priorizar simplicidade operacional, produtividade da equipe e facilidade de aprendizado.

O sistema será em português brasileiro, logo nao crie nada em ingles

---

# Objetivos do MVP

Permitir que uma imobiliária consiga:
* Realizar Login para utilizar o sistema
* Cadastrar imóveis.
* Cadastrar proprietários.
* Cadastrar inquilinos.
* Cadastrar corretores.
* Gerenciar contratos de aluguel.
* Controlar recebimentos.
* Controlar repasses aos proprietários.
* Controlar receitas e despesas.
* Controlar usuários e permissões.
* Notificar inquilinos sobre vencimentos.
* Emitir relatórios gerenciais básicos.

---

# Stack Tecnológica

## Backend

* Laravel 13
* PHP 8.4+
* PostgreSQL

## Frontend

* Vue 3 (componetização)
* Inertia.js
* TypeScript
* Tailwind CSS 4
* DaisyUI

## Componentes Auxiliares

* SweetAlert2 para confirmações
* Laravel Queues
* Laravel Scheduler
* Laravel Notifications
* Lucide Icons

---

# Diretrizes Arquiteturais

## Backend

O sistema deverá seguir:

* Service Layer
* Form Requests
* Policies
* Soft Deletes
* UUID nas entidades principais
* Eager Loading para evitar N+1
* Paginação em todas as listagens

## Frontend

* Componentização
* Composables reutilizáveis
* Tipagem TypeScript
* Componentes desacoplados
* Formulários reutilizáveis

---

# Diretrizes de UX/UI

## Listagens

Todas as listagens deverão possuir:

* Tabela
* Paginação
* Ordenação
* Pesquisa
* Filtros

## Formulários

Utilizar:

### Modal

Quando houver poucos campos.

### Wizard (Steps)

Quando o cadastro possuir múltiplas etapas.

### Página Completa

Quando houver grande quantidade de informações.

## Visualização

Sempre que possível utilizar:

* Tabs
* Cards
* Indicadores visuais
* Status coloridos

## Exclusão

O sistema não deve excluir registros fisicamente.

Todas as entidades devem utilizar:

* Soft Delete

---

# Módulos do Sistema

## 1. Autenticação

Funcionalidades:

* Login
* Logout
* Recuperação de senha
* Alteração de senha

Regras:

* Apenas usuários cadastrados podem acessar o sistema.
* Não haverá auto cadastro.

---

## 2. Dashboard

Indicadores:

* Imóveis disponíveis
* Imóveis alugados
* Contratos ativos
* Contratos vencendo
* Receitas do mês
* Despesas do mês
* Inadimplência
* Repasses pendentes

Gráficos:

* Receitas x Despesas
* Recebimentos por mês
* Inadimplência

---

## 3. Gestão de Imóveis

CRUD completo.

Informações mínimas:

* Código interno
* Tipo
* Finalidade
* Proprietário
* Endereço
* Valor de aluguel
* Valor de venda
* Status

Status:

* Disponível
* Reservado
* Alugado
* Inativo

Uploads:

* Fotos
* Documentos

Relacionamentos:

* Um imóvel pertence a um proprietário.
* Um imóvel pode possuir vários contratos ao longo do tempo.

---

## 4. Gestão de Proprietários

CRUD completo.

Dados mínimos:

* Nome
* CPF/CNPJ
* Telefone
* WhatsApp
* Email
* Endereço
* Dados bancários

Relacionamentos:

* Um proprietário pode possuir vários imóveis.

---

## 5. Gestão de Inquilinos

CRUD completo.

Dados mínimos:

* Nome
* CPF
* RG
* Telefone
* WhatsApp
* Email
* Profissão
* Renda

Relacionamentos:

* Um inquilino pode possuir vários contratos ao longo do tempo.

---

## 6. Gestão de Corretores

CRUD completo.

Dados mínimos:

* Nome
* CPF
* CRECI
* Telefone
* Email

Informações adicionais:

* Percentual de comissão padrão.

---

## 7. Gestão de Contratos de Locação

CRUD completo.

Relacionamentos:

* Imóvel
* Proprietário
* Inquilino
* Corretor

Dados mínimos:

* Data início
* Data fim
* Dia vencimento
* Valor aluguel
* Percentual da imobiliária

---

## Encargos Contratuais

O contrato deverá permitir definir a responsabilidade dos encargos.

Exemplos:

* IPTU
* Condomínio
* Água
* Energia
* Seguro

Responsáveis:

* Locador
* Locatário
* Incluso no aluguel

Estas informações devem permanecer vinculadas ao contrato.

---

# Regras Financeiras

## Taxa da Imobiliária

A imobiliária recebe apenas o percentual definido no contrato.

Exemplo:

Aluguel: R$ 1.500

Taxa Administração: 10%

Receita da Imobiliária:

R$ 150

Repasse ao Proprietário:

R$ 1.350

---

## Encargos

Os encargos não devem compor automaticamente a receita da imobiliária.

O sistema deve separar:

* Receita da imobiliária
* Valores pertencentes ao proprietário
* Valores de terceiros

---

## Recebimento

Ao registrar o pagamento de um aluguel:

O sistema deverá:

* Registrar entrada financeira.
* Calcular taxa da imobiliária.
* Gerar valor de repasse.
* Atualizar indicadores financeiros.

---

## 8. Gestão Financeira

Controle de:

### Entradas

* Recebimento de aluguel
* Receitas diversas

### Saídas

* Despesas operacionais
* Despesas administrativas
* Repasses

---

## 9. Repasses

O sistema deverá controlar:

* Proprietário
* Referência
* Valor recebido
* Taxa da imobiliária
* Valor líquido

Status:

* Pendente
* Pago

---

## 10. Usuários

CRUD completo.

Dados:

* Nome
* Email
* Senha

Relacionamento:

* Perfil

---

## 11. Perfis

CRUD completo.

Exemplos:

* Administrador
* Financeiro
* Corretor
* Atendente

---

## 12. Permissões

Controle granular por módulo.

Exemplos:

* Visualizar
* Criar
* Editar
* Excluir
* Restaurar

---

## 13. Notificações

Inicialmente apenas por email.

Tipos:

* Vencimento próximo
* Cobrança vencida
* Confirmação de pagamento

O sistema deverá possuir estrutura preparada para futura integração com WhatsApp.

---

# Relatórios

MVP deverá possuir:

## Imóveis

* Disponíveis
* Alugados

## Contratos

* Ativos
* Encerrados

## Financeiro

* Receitas
* Despesas
* Fluxo de caixa

## Repasses

* Pendentes
* Pagos

---

# Funcionalidades Fora do Escopo do MVP

Estas funcionalidades não fazem parte da primeira versão:

* Portal do proprietário
* Portal do inquilino
* Assinatura digital
* Vistorias digitais
* Integração Asaas
* PIX automático
* WhatsApp
* CRM imobiliário
* Venda de imóveis
* Aplicativo mobile
* Multiempresa
* Multiidioma

---

# Critérios de Qualidade

* Código limpo
* Tipagem forte
* Componentização
* Responsividade
* Testável
* Escalável
* Fácil manutenção
* Preparado para futuras integrações



## Por que

O ImobGestor precisa de uma área centralizada para gerenciar clientes da imobiliária. Sem ela, não é possível vincular proprietários a imóveis nem inquilinos a contratos, bloqueando todos os módulos futuros do sistema. A entidade de Cliente é a fundação sobre a qual imóveis, contratos, financeiro e dashboard serão construídos.

## O que muda

- **Novo módulo de Gestão de Clientes** com listagem, cadastro, edição e visualização detalhada.
- **Entidade central Cliente** que substitui cadastros separados de proprietários e inquilinos, eliminando duplicidade de dados.
- **Sistema de papéis** (proprietário, inquilino ou ambos) associados ao mesmo cliente.
- **Dados adicionais por papel**: informações bancárias e de repasse para proprietários; profissão, renda e dados de análise para inquilinos.
- **Controle de status** (ativo/inativo) com proteção contra exclusão física de clientes com vínculos.
- **Validações de unicidade** de CPF e CNPJ no sistema.
- **Base para integrações** futuras com imóveis (proprietário) e contratos (inquilino).

## Capacidades

### Novas Capacidades

- `gestao-de-clientes`: Cadastro, listagem, edição, visualização e controle de status de clientes da imobiliária, com suporte a múltiplos papéis (proprietário e/ou inquilino), dados complementares por papel, endereço e contatos.

### Capacidades Modificadas

<!-- Nenhuma capacidade existente tem requisitos alterados por esta mudança. -->

## Impacto

- **Banco de dados**: Novas tabelas `clientes`, `cliente_papeis`, `cliente_dados_proprietario`, `cliente_dados_inquilino`.
- **Backend Laravel**: Migrations, Models, Controllers, Form Requests, Services, Policies e rotas sob o prefixo `/clientes`.
- **Frontend Vue/Inertia**: Novas páginas Index, Create, Edit e Show dentro do layout administrativo existente.
- **Integrações futuras**: Módulo de imóveis deverá filtrar clientes ativos com papel de proprietário; módulo de contratos deverá filtrar clientes ativos com papel de inquilino.
- **Dependências**: Nenhuma dependência externa nova; usa stack existente (Laravel, Inertia, Vue 3, DaisyUI, Tailwind CSS 4, PostgreSQL).

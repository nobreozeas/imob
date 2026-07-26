## Why

A tela de detalhes do contrato (`Admin/Contratos/Show.vue`) hoje mostra tudo em uma única rolagem longa (dados gerais, parcelas, encargos, caução, repasses, documentos, histórico), o que já foge do padrão de abas previsto no PRD (seção 28.7: "Detalhes: Usar tabs: Resumo, Parcelas, Encargos, Caução, Repasses, Documentos, Histórico") e dificulta a navegação à medida que o contrato acumula histórico. Além disso, não existe hoje nenhuma forma de gerar um documento do contrato a partir dos dados cadastrados (o usuário precisa redigitar o contrato em outro lugar para imprimir/assinar), e embora o backend já tenha rota para anexar documentos ao contrato depois de criado (`contratos.documentos.adicionar`, inclusive com o tipo `contrato_assinado` já previsto), não existe nenhuma tela para de fato anexar ou remover esses documentos — a funcionalidade existe só na API.

## What Changes

- Reorganizar `Admin/Contratos/Show.vue` em abas: Resumo, Parcelas, Encargos, Caução, Repasses, Documentos e Histórico, cada uma exibindo o que já é mostrado hoje (sem novos dados), com a aba selecionável por parâmetro de URL (`?tab=`) para permitir links diretos vindos de outras telas (ex.: repasses, inadimplência).
- Criar uma visualização impressa do contrato, gerada a partir dos dados já cadastrados (partes, imóvel, vigência, valores, reajuste, encargos, multas, caução), acessível por um botão "Imprimir contrato" — abre em nova aba uma página HTML limpa (sem menu/topo do sistema) com folha de estilo própria para impressão; o usuário imprime ou salva como PDF pelo diálogo de impressão do navegador.
- Adicionar, na aba Documentos, upload de novos documentos (reaproveitando a rota `contratos.documentos.adicionar` já existente) — com destaque para anexar o contrato assinado (tipo `contrato_assinado`) — e remoção de documentos já anexados (reaproveitando `contratos.documentos.remover`), ambos hoje sem UI.
- A edição do contrato continua disponível apenas quando o contrato está em rascunho (regra já existente do PRD, sem mudança) — o botão "Editar" permanece no cabeçalho, fora das abas, e continua levando ao fluxo de edição já existente.

## Capabilities

### New Capabilities
- `contrato-visualizacao-tabs`: reorganização da tela de detalhes do contrato em abas (Resumo, Parcelas, Encargos, Caução, Repasses, Documentos, Histórico), com suporte a deep-link por parâmetro de URL.
- `contrato-impressao`: geração de uma visualização/documento imprimível do contrato a partir dos dados cadastrados.
- `contrato-documentos-anexos`: upload e remoção de documentos do contrato pela interface (a capacidade de backend já existe; falta a tela).

### Modified Capabilities
(nenhuma — são funcionalidades novas ou telas para capacidades de backend já existentes, sem specs formais arquivadas para o módulo de contratos)

## Impact

- **Frontend**: `resources/js/Pages/Admin/Contratos/Show.vue` reestruturado em abas; novo componente de upload/remoção de documentos; novo botão/link "Imprimir contrato".
- **Backend**: nova rota + view Blade de impressão do contrato (`GET contratos/{contrato}/imprimir`), reaproveitando o model `ContratoLocacao` e suas relações já carregadas; nenhuma mudança de schema, nenhuma nova permissão (impressão reaproveita `contratos.view`; upload/remoção de documentos reaproveita a policy `documentos`/permissão `contratos.documentos` já existentes).
- Sem novas dependências — a impressão usa uma view Blade comum com CSS de impressão (`@media print`) e `window.print()` no navegador, sem biblioteca de geração de PDF no servidor.
- Não muda quando a edição do contrato é permitida (continua restrita a `rascunho`, conforme confirmado com o usuário).

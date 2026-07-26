## Context

`Admin/Contratos/Show.vue` (285 linhas) hoje renderiza em sequência: cabeçalho com ações por status, card de dados gerais, `TabelaParcelas`, `TabelaRepasses`, `CardEncargos`, `CardCaucao`, `CardMultas`, lista de documentos (somente leitura, sem upload/remoção na UI) e `CardHistorico`, mais uma coluna lateral com resumo financeiro/vigência/meta. Todos esses componentes já existem e recebem os dados via props a partir do `contrato` carregado com eager loading no `ContratoLocacaoController::show()`. A rota e o método `adicionarDocumento`/`removerDocumento` já existem no controller (autorizados pela policy `documentos`, permissão `contratos.documentos`) mas não são usados por nenhuma tela hoje.

Não existe nenhuma geração de documento/impressão do contrato no sistema, nem biblioteca de PDF instalada (`composer.json` não tem `dompdf`/`snappy`).

## Goals / Non-Goals

**Goals:**
- Reorganizar a tela de detalhes do contrato em abas (Resumo, Parcelas, Encargos, Caução, Repasses, Documentos, Histórico), sem alterar os dados exibidos em cada seção — é uma reorganização de layout, reaproveitando os componentes já existentes.
- Permitir navegar direto para uma aba específica via `?tab=parcelas` (por exemplo), para que outras telas (repasses, inadimplência) possam linkar direto para a aba relevante do contrato.
- Gerar uma visualização imprimível do contrato a partir dos dados já cadastrados, cobrindo: partes (locador/locatário/corretor), imóvel e endereço, vigência, valor do aluguel e reajuste, forma de pagamento e encargos, multas por atraso e rescisão, caução/garantia, e campo de assinaturas — permitindo imprimir ou salvar como PDF pelo navegador.
- Dar uma UI para anexar (com destaque para o contrato assinado) e remover documentos do contrato, reaproveitando as rotas já existentes no backend.

**Non-Goals:**
- Não muda quando a edição do contrato é permitida — continua restrita a contratos em `rascunho` (confirmado com o usuário). O botão "Editar" permanece como está, fora das abas.
- Não gera PDF no servidor (sem `dompdf`/`snappy`/etc.) — a "impressão" é uma página HTML com CSS de impressão, usando o recurso nativo do navegador (`window.print()`, que também permite "Salvar como PDF").
- Não implementa assinatura eletrônica (fora do MVP, PRD item explícito de não-objetivo) — o documento impresso tem um campo de assinatura física/espaço em branco, e o "contrato assinado" entra no sistema como upload manual de arquivo (já é o fluxo hoje).
- Não altera as regras de cálculo (multa, juros, taxa de administração, repasse) — a view de impressão só formata valores já calculados/armazenados.
- Não adiciona nova permissão — impressão reaproveita `contratos.view` (é só uma forma de visualizar dados já visíveis), upload/remoção de documentos reaproveita a policy/permissão `contratos.documentos` já existentes.

## Decisions

### 1. Abas implementadas com o componente `tabs` do DaisyUI, sem novo componente genérico
`Show.vue` ganha um estado local `abaAtiva` (lido de `?tab=` na URL na montagem, com fallback `'resumo'`) e usa `tabs`/`tab` do DaisyUI para alternar qual bloco de conteúdo é exibido. Cada aba mantém os componentes que já existem (`TabelaParcelas`, `CardEncargos`, etc.) — não são reescritos, só movidos para dentro do bloco da aba correspondente. Ao trocar de aba, atualiza a URL via `router.visit(..., { preserveState: true, preserveScroll: true, replace: true })` (sem nova requisição ao backend — é só atualização da querystring local) para permitir compartilhar o link da aba atual.

### 2. Impressão como view Blade dedicada, não Inertia
Uma página de impressão precisa de um layout totalmente isolado do shell autenticado (sem sidebar/topbar, com uma folha de estilo própria voltada a papel A4). Fazer isso dentro do SPA Inertia exigiria esconder todo o layout via CSS `@media print` nas páginas Vue — funciona, mas deixa a estrutura HTML do app inteira presente no DOM só para ser escondida. Uma view Blade simples (`resources/views/contratos/imprimir.blade.php`), servida por uma rota própria que carrega o `ContratoLocacao` com as relações necessárias e passa para a view, é mais direta e mais fácil de manter só para essa finalidade. Rota: `GET contratos/{contrato}/imprimir`, nome `contratos.imprimir`, dentro do grupo `auth` já existente, autorizada via `Gate::authorize('view', $contrato)`.

### 3. Conteúdo do contrato: cláusulas geradas a partir dos dados já existentes, sem novos campos
A view de impressão interpola os dados já existentes no model (não pede nenhum campo novo ao usuário). Estrutura das cláusulas:
- **Partes**: locador (proprietário), locatário (inquilino), corretor (se houver), com nome/CPF-CNPJ/endereço já cadastrados em `Cliente`.
- **Objeto**: endereço completo do imóvel.
- **Prazo**: `data_inicio`, `data_fim` ou `duracao_meses`.
- **Valor e reajuste**: `valor_aluguel`, `indice_reajuste`, `periodicidade_reajuste`.
- **Forma de pagamento**: `dia_vencimento`.
- **Encargos**: lista de `ContratoEncargo` com responsável por cada um.
- **Multas**: percentual de multa por atraso, juros e tolerância (`ContratoMultas`), e multa por rescisão antecipada quando configurada.
- **Caução/Garantia**: tipo e valor (`ContratoCaucao`), quando `possui_caucao`.
- **Assinaturas**: espaço para locador, locatário, corretor (quando houver) e testemunhas.
Campos ausentes/não configurados (ex.: sem caução, sem corretor) simplesmente omitem a cláusula correspondente, em vez de exibir "—" no meio de um texto corrido.

### 4. Upload de documentos reaproveita validação e rota existentes
O novo formulário na aba Documentos usa exatamente `StoreContratoRequest`... não, reaproveita a validação já embutida em `ContratoLocacaoController::adicionarDocumento()` (`mimes:pdf,jpg,jpeg,png,docx`, até 20MB, `tipo` em `contrato_assinado,laudo_vistoria,comprovante_caucao,outros`) — nenhuma mudança no backend, só a tela que estava faltando. O seletor de tipo vem com `contrato_assinado` pré-selecionado (é o caso de uso mais citado pelo usuário), mas os demais tipos continuam disponíveis.

## Risks / Trade-offs

- [Texto legal gerado automaticamente pode não refletir exigências jurídicas específicas de cada imobiliária/região] → o documento serve como minuta/rascunho a partir dos dados do sistema, não como contrato juridicamente validado; cabe à imobiliária revisar antes de usar — isso já é esperado (o PRD não pede validação jurídica no MVP).
- [Trocar de aba via querystring sem nova requisição ao servidor pode ficar dessincronizado se os dados de uma aba dependerem de uma prop não carregada de início] → não é o caso aqui: todos os dados de todas as abas já vêm juntos no `show()` atual (mesma carga que existe hoje, só reorganizada visualmente), então não há necessidade de lazy-loading por aba nesta mudança.
- [Usuário remover um documento por engano] → a remoção exige confirmação (modal/`Swal.fire`), consistente com o padrão já usado em outras ações destrutivas do sistema.

## Migration Plan

Mudança de frontend + uma rota/view nova no backend, sem schema ou dado envolvido.
1. Reorganizar `Show.vue` em abas (sem tocar nos subcomponentes).
2. Adicionar leitura/escrita de `?tab=` na URL.
3. Criar a UI de upload/remoção de documentos na aba Documentos.
4. Criar a rota + view Blade de impressão e o botão "Imprimir contrato" no cabeçalho.

## Open Questions

- O texto legal completo (cláusulas com redação jurídica formal) deve ser revisado por um advogado/pela imobiliária antes de uso real — o sistema fornece a estrutura e os dados, não uma consultoria jurídica.

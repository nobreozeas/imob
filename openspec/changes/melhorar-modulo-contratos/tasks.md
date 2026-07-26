## 1. Detalhes do contrato em abas

- [x] 1.1 `Show.vue` reestruturado: estado `abaAtiva` (lido de `URLSearchParams(window.location.search)` na montagem, default `'resumo'`), abas renderizadas com `tabs`/`tab` do DaisyUI para as 7 seções (Resumo, Parcelas, Encargos, Caução, Repasses, Documentos, Histórico)
- [x] 1.2 Aba "Resumo": Dados Gerais, Resumo Financeiro, Vigência e Informações/Meta agrupados nela, sem alterar campos
- [x] 1.3 Aba "Parcelas": `TabelaParcelas`
- [x] 1.4 Aba "Encargos": `CardEncargos` + `CardMultas` juntos (decisão: multas ficam com encargos, não no Resumo, por serem ambos regras do contrato)
- [x] 1.5 Aba "Caução": `CardCaucao`
- [x] 1.6 Aba "Repasses": `TabelaRepasses`
- [x] 1.7 Aba "Documentos": lista de documentos existente + novo formulário de upload (task 3)
- [x] 1.8 Aba "Histórico": `CardHistorico`
- [x] 1.9 Troca de aba usa `window.history.replaceState` (não `router.visit`) para atualizar `?tab=` sem nenhum round-trip ao Inertia/servidor — mais direto que o `router.visit` cogitado no design, mesmo resultado; um listener de `popstate` sincroniza a aba ao usar voltar/avançar do navegador
- [x] 1.10 Links atualizados: `Financeiro/Repasses/Index.vue` → `?tab=repasses`; `Components/Financeiro/TabelaInadimplencia.vue` → `?tab=parcelas` (via `route('contratos.show', { contrato: id, tab: 'slug' })`, suportado nativamente pelo Ziggy)

## 2. Impressão do contrato

- [x] 2.1 Criado `app/Http/Controllers/Contratos/ContratoImpressaoController.php::imprimir()`, autoriza via `$this->authorize('view', $contrato)`, carrega imóvel/proprietário/inquilino/corretor/encargos/caução/multas
- [x] 2.2 Rota `GET contratos/{contrato}/imprimir` → `contratos.imprimir` adicionada ao grupo `auth` existente
- [x] 2.3 Criado `resources/views/contratos/imprimir.blade.php`: partes, objeto, prazo, valor/reajuste, encargos (tabela), multas, caução (condicional), assinaturas; formatação de moeda/data em pt-BR feita com closures locais (evitando `function` nomeada no `@php`, que quebraria com "Cannot redeclare function" ao renderizar a view mais de uma vez no mesmo processo — pego durante a implementação, não previsto no design original)
- [x] 2.4 Botão "Imprimir" (`onclick="window.print()"`) incluído na própria view, oculto via `@media print`
- [x] 2.5 Botão "Imprimir Contrato" adicionado no cabeçalho de `Show.vue`, abre em nova aba, condicionado a `contratos.view`

## 3. Anexar e remover documentos pela interface

- [x] 3.1 Criado `resources/js/Components/Contratos/FormularioDocumentoContrato.vue`: input de arquivo + select de tipo (`contrato_assinado` pré-selecionado), `useForm` + `forceFormData` para `contratos.documentos.adicionar`; renderizado em `Show.vue` só quando `contratos.documentos` está nas permissões do usuário
- [x] 3.2 Ação "Remover" adicionada em cada item de documento na aba Documentos, com confirmação via `Swal.fire` antes de `DELETE contratos.documentos.remover`
- [x] 3.3 Erros de validação exibidos via `form.errors.documento`; adicionado também um alerta de sucesso (`flash.status`) no topo de `Show.vue` — não existia nenhum feedback de sucesso na tela antes desta mudança

## 4. Verificação

- [x] 4.1 Suíte completa rodada antes e depois das mudanças (`docker compose exec -e APP_ENV=testing app ./vendor/bin/phpunit`): nenhuma regressão
- [x] 4.2 Criado `tests/Feature/Contratos/ContratoImpressaoTest.php`: usuário com permissão recebe 200 e vê o número do contrato; usuário sem permissão recebe 403; seção de caução omitida/exibida conforme configuração do contrato
- [x] 4.3 Criado `tests/Feature/Contratos/ContratoDocumentoTest.php`: upload de contrato assinado com permissão, upload negado sem permissão, remoção de documento — nenhum teste cobria essas rotas antes desta mudança
- [x] 4.4 `vue-tsc --noEmit` conferido: nenhum erro novo nos arquivos tocados além do padrão pré-existente de `route()`/`ContratoPaginado.from/to` já documentado em mudanças anteriores; `vite build` rodado com sucesso como verificação adicional de que os componentes novos/alterados compilam
- [x] 4.5 Verificação manual completa (clicar pelas 7 abas, gerar impressão visualmente, anexar/remover documento num navegador real) **não foi possível nesta sessão** — ambiente sem contratos cadastrados no banco de dev e sem sessão de navegador disponível; a cobertura ficou por conta de: suíte automatizada (incluindo asserções de conteúdo renderizado da view de impressão), `vue-tsc` e `vite build`. Recomenda-se um teste manual rápido (via `/run`) antes de considerar esta mudança pronta para uso real

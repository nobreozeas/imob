## Context

`imovel_dados_comerciais` guarda hoje 4 campos de responsabilidade (`responsavel_iptu`, `responsavel_agua`, `responsavel_energia`, `responsavel_condominio`, valores `proprietario`/`inquilino`) preenchidos no wizard de cadastro de imóvel (Step 4 — Dados Comerciais). O módulo de Contratos já possui `contrato_encargos` (`ContratoEncargo`), com `tipo_encargo` + `responsavel` por contrato, e `ContratoLocacaoService` já sincroniza encargos a partir do wizard de contrato (`sincronizarEncargos`). Um levantamento no código confirmou que nenhuma tela ou serviço do módulo de Contratos lê os campos `responsavel_*` do imóvel — ou seja, removê-los não quebra nenhuma integração existente.

## Goals / Non-Goals

**Goals:**
- Eliminar os 4 campos de responsabilidade de encargo do cadastro/edição/exibição de imóvel.
- Manter `contrato_encargos` como única fonte de verdade para "quem paga o quê" em uma locação.

**Non-Goals:**
- Não alterar a estrutura de `contrato_encargos` (ex.: adicionar `cobrar_junto_aluguel`/`valor_estimado`, mencionados no PRD 17.10) — fica para uma mudança futura do módulo de Contratos, fora deste escopo.
- Não remover `valor_condominio`, `valor_iptu`, `condominio_incluso` ou `valor_caucao_sugerido` de `imovel_dados_comerciais` — são valores de referência informativos, não decisão de responsabilidade, e não foram objeto do pedido.

## Decisions

1. **Remover as colunas via nova migration (drop), não apenas parar de usar.**
   Alternativa considerada: manter as colunas no banco e só remover do formulário/validação. Rejeitada porque manter colunas mortas no schema é exatamente o tipo de duplicidade/confusão que esta mudança busca eliminar, e o levantamento confirmou que nada mais depende delas.

2. **Remover o tipo `ResponsavelCusto` do frontend.**
   Ele é usado exclusivamente pelos 4 campos removidos (`resources/js/types/imovel.ts`). Mantê-lo órfão não tem propósito.

3. **Step 4 do wizard de imóvel (`WizardStep4DadosComerciais.vue`) perde os 4 selects de responsável, mas continua existindo** para os campos financeiros que permanecem (`valor_aluguel`, `valor_venda`, `valor_condominio`, `valor_iptu`, `condominio_incluso`, `valor_caucao_sugerido`, `observacoes_comerciais`).

## Risks / Trade-offs

- [Dado histórico nas 4 colunas é perdido ao rodar a migration de remoção] → Aceitável: nenhum outro lugar do sistema lê esse dado hoje, e a fonte de verdade correta (contrato) não depende dele.
- [Usuários acostumados a preencher esses campos no cadastro de imóvel podem estranhar a mudança] → Mitigação: a responsabilidade por encargo continua sendo perguntada, só que no momento certo — na criação do contrato (Step de Encargos), que é onde ela realmente se aplica.

## Migration Plan

1. Nova migration `drop_responsaveis_from_imovel_dados_comerciais_table` removendo as 4 colunas.
2. Deploy: rodar a migration; não há dado a migrar/preservar (ver Risks).
3. Rollback: migration `down()` recria as colunas (nullable, sem dados), caso seja necessário reverter antes de um novo deploy.

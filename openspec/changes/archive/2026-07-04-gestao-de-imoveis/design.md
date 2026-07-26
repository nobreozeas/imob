## Context

O módulo de Imóveis (`app/Models/Imovel.php`, `ImovelController`, `ImovelService`, `ImovelPolicy`) já está implementado e cobre cadastro, edição, listagem com filtros/ordenação/paginação e alteração de status (`disponivel`, `reservado`, `alugado`, `em_manutencao`, `inativo`). Fotos e documentos hoje só são gerenciados dentro do payload de create/update (`fotos_novas`, `fotos_remover`, `documentos_novos`, `documentos_remover`), sem endpoints próprios. Não existe exclusão lógica (`->except(['destroy'])` nas rotas), nem restauração, nem qualquer tabela/registro de histórico de eventos do imóvel — diferente do contrato, que já tem `contrato_historicos` + `ContratoHistoricoService` como padrão estabelecido no projeto.

Esta mudança fecha essas lacunas seguindo os padrões já usados no módulo de Contratos (Service dedicado para histórico, tabela `<entidade>_historicos` com `tipo_evento`/`dados_anteriores`/`dados_novos`).

## Goals / Non-Goals

**Goals:**
- Adicionar exclusão lógica e restauração de imóveis, respeitando a regra de que imóvel com contrato ativo não pode ser excluído.
- Adicionar indicadores rápidos e filtro de excluídos na listagem.
- Extrair o gerenciamento de fotos/documentos para endpoints incrementais, mantendo compatibilidade com o fluxo atual do formulário (create/update continuam aceitando os mesmos campos).
- Registrar histórico de eventos do imóvel e exibi-lo em uma aba na tela de detalhes.

**Non-Goals:**
- Não reespecificar ou alterar o CRUD, wizard, enums de status/tipo/finalidade ou regras já implementadas (permanecem como estão).
- Não implementar a aba "Contratos" no Show com listagem detalhada de contratos vinculados (fica para a mudança de Contratos de Locação, que já existe como capability separada).
- Não migrar `fotos_novas`/`documentos_novos` do formulário de create/edit para os novos endpoints — os dois caminhos coexistem.

## Decisions

1. **Histórico como Service explícito, não Model Observer.**
   Seguir o padrão de `ContratoHistoricoService`: criar `ImovelHistoricoService::registrar(Imovel $imovel, string $tipoEvento, string $descricao, array $dadosAnteriores = null, array $dadosNovos = null)`, chamado explicitamente em `ImovelService` (criar/atualizar/alterarStatus/excluir/restaurar) e nos novos controllers de fotos/documentos.
   Alternativa considerada: `Model::observe()` global — rejeitada porque o projeto já estabeleceu o padrão de Service explícito para contratos, e observers tornam os eventos implícitos e mais difíceis de rastrear/testar.

2. **Tabela `imovel_historicos`, mesma forma de `contrato_historicos`.**
   Colunas: `id (uuid)`, `imovel_id`, `tipo_evento` (enum), `descricao` (text), `dados_anteriores`/`dados_novos` (json nullable), `usuario_id`, `created_at`. Sem `updated_at`/soft delete (histórico é imutável), consistente com `contrato_historicos`.
   `tipo_evento` enum: `criacao`, `atualizacao`, `alteracao_status`, `foto_adicionada`, `foto_removida`, `documento_adicionado`, `documento_removido`, `exclusao`, `restauracao`.

3. **Exclusão lógica via soft delete padrão do Eloquent, com bloqueio de contrato ativo.**
   `ImovelService::excluir(Imovel $imovel)`: valida `$imovel->temContratoAtivo()` (método já existe) e lança `ValidationException` se houver contrato ativo — mesmo padrão usado em `alterarStatus()`. Reaproveita `SoftDeletes` já presente no model.
   Restauração via `ImovelService::restaurar(Imovel $imovel)`, sem validação adicional (restaurar é sempre permitido).

4. **Rotas: reabilitar `destroy` no resource e adicionar `restore`, escopadas por permissão dedicada.**
   Remover `->except(['destroy'])` de `Route::resource('imoveis', ...)` e adicionar:
   ```
   Route::patch('imoveis/{imovel}/restaurar', [ImovelController::class, 'restore'])->name('imoveis.restore')->withTrashed();
   ```
   Novas permissões: `imoveis.destroy`, `imoveis.restore` (Policy: `delete()`, `restore()`).

5. **Fotos/documentos: novos controllers dedicados, reaproveitando a lógica de storage do `ImovelService`.**
   Extrair `sincronizarFotos`/`sincronizarDocumentos` para um `ImovelMidiaService` compartilhado entre `ImovelService` (fluxo do formulário) e os novos `FotoImovelController`/`DocumentoImovelController` (ações incrementais). Isso evita duplicar a lógica de `Storage::disk('public')`.
   Rotas:
   ```
   POST   imoveis/{imovel}/fotos                  FotoImovelController@store
   DELETE imoveis/{imovel}/fotos/{foto}            FotoImovelController@destroy
   PATCH  imoveis/{imovel}/fotos/{foto}/principal  FotoImovelController@definirPrincipal
   POST   imoveis/{imovel}/documentos              DocumentoImovelController@store
   DELETE imoveis/{imovel}/documentos/{documento}  DocumentoImovelController@destroy
   ```
   Autorização via `imoveis.gerenciar-fotos` / `imoveis.gerenciar-documentos`, checadas na Policy (`gerenciarFotos`, `gerenciarDocumentos`).

6. **Indicadores da listagem calculados com uma única query agregada.**
   `ImovelController@index` adiciona um `groupBy('status')` + `count()` sobre o mesmo escopo de proprietário/busca visível para o usuário (sem paginação), evitando 5 queries separadas. Retornado como prop `indicadores` para o Index.vue.

## Risks / Trade-offs

- [Duplicar regras de negócio entre `ImovelService` e os novos controllers de mídia] → Mitigado extraindo `ImovelMidiaService` compartilhado (decisão 5).
- [Excluir imóvel com fotos/documentos deixa arquivos órfãos no storage] → Fora de escopo: soft delete não remove arquivos físicos, consistente com o restante do sistema (nenhuma outra entidade limpa storage no soft delete); pode ser tratado em rotina de limpeza futura.
- [Enum `tipo_evento` do histórico ficar defasado conforme novas ações forem adicionadas] → Aceitável para o MVP; adicionar valor ao enum é uma migration simples quando necessário.

## Open Questions

- Deve haver um limite de retenção/paginação específico para o histórico de um imóvel com muitos eventos? (assumimos paginação simples de 20 por página, igual às demais listagens, até indicação em contrário)

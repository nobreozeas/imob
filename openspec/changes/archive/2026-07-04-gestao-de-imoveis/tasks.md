## 1. Banco de dados

- [x] 1.1 Criar migration `create_imovel_historicos_table` (uuid, imovel_id, tipo_evento enum, descricao, dados_anteriores/dados_novos json nullable, usuario_id, created_at), com `foreignUuid('imovel_id')->constrained('imoveis')->cascadeOnDelete()`.
- [x] 1.2 Rodar a migration em ambiente local (`docker compose exec app php artisan migrate`).

## 2. Models e permissões

- [x] 2.1 Criar model `ImovelHistorico` (fillable, casts json, relação `imovel()`, `usuario()`), seguindo o padrão de `ContratoHistorico`.
- [x] 2.2 Adicionar relação `historicos()` (`hasMany(ImovelHistorico::class)->latest()`) em `Imovel`.
- [x] 2.3 Adicionar as permissões `imoveis.destroy`, `imoveis.restore`, `imoveis.gerenciar-fotos`, `imoveis.gerenciar-documentos` em `database/seeders/ImovelPermissionsSeeder.php` (e nos perfis aplicáveis em `PerfisEPermissoesSeeder.php`).
- [x] 2.4 Rodar os seeders atualizados.

## 3. Services

- [x] 3.1 Criar `ImovelHistoricoService::registrar(Imovel $imovel, string $tipoEvento, string $descricao, ?array $dadosAnteriores = null, ?array $dadosNovos = null, ?User $usuario = null)`.
- [x] 3.2 Extrair `sincronizarFotos`/`sincronizarDocumentos` de `ImovelService` para um novo `ImovelMidiaService`, reutilizável pelos novos controllers e pelo fluxo de create/update existente.
- [x] 3.3 Adicionar `ImovelService::excluir(Imovel $imovel)`: valida `temContratoAtivo()`, lança `ValidationException` se houver contrato ativo, executa soft delete e registra histórico (`exclusao`).
- [x] 3.4 Adicionar `ImovelService::restaurar(Imovel $imovel)`: restaura o soft delete e registra histórico (`restauracao`).
- [x] 3.5 Chamar `ImovelHistoricoService::registrar` em `criar()`, `atualizar()` e `alterarStatus()` de `ImovelService` (eventos `criacao`, `atualizacao`, `alteracao_status`).
- [x] 3.6 Chamar `ImovelHistoricoService::registrar` em `ImovelMidiaService` para os eventos `foto_adicionada`, `foto_removida`, `documento_adicionado`, `documento_removido`.

## 4. Policy

- [x] 4.1 Adicionar `delete()`, `restore()`, `gerenciarFotos()`, `gerenciarDocumentos()` em `ImovelPolicy`, mapeando para as novas permissões.

## 5. Controllers e Form Requests

- [x] 5.1 Adicionar `ImovelController::destroy()` (autoriza `delete`, chama `ImovelService::excluir`, redireciona com mensagem de sucesso/erro) e `ImovelController::restore()` (autoriza `restore`, usa `Imovel::withTrashed()->findOrFail()`).
- [x] 5.2 Atualizar `ImovelController::index()` para aceitar filtro `excluidos` (usa `onlyTrashed()` quando ativo) e para retornar prop `indicadores` com contagem total e por status (via `groupBy('status')->count()` sobre o mesmo escopo de filtros, sem paginação).
- [x] 5.3 Criar `StoreFotoImovelRequest` e `FotoImovelController` com `store()`, `destroy()`, `definirPrincipal()`, usando `ImovelMidiaService`.
- [x] 5.4 Criar `StoreDocumentoImovelRequest` e `DocumentoImovelController` com `store()`, `destroy()`, usando `ImovelMidiaService`.

## 6. Rotas

- [x] 6.1 Remover `->except(['destroy'])` de `Route::resource('imoveis', ImovelController::class)`.
- [x] 6.2 Adicionar rota `PATCH imoveis/{imovel}/restaurar` (com `withTrashed()`) apontando para `ImovelController::restore`.
- [x] 6.3 Adicionar rotas `POST imoveis/{imovel}/fotos`, `DELETE imoveis/{imovel}/fotos/{foto}`, `PATCH imoveis/{imovel}/fotos/{foto}/principal`.
- [x] 6.4 Adicionar rotas `POST imoveis/{imovel}/documentos`, `DELETE imoveis/{imovel}/documentos/{documento}`.

## 7. Frontend — listagem e ciclo de vida

- [x] 7.1 Adicionar cards de indicadores (total, disponível, alugado, reservado, em manutenção, inativo) no topo de `Index.vue`.
- [x] 7.2 Adicionar filtro "imóveis excluídos" em `Index.vue`, visível apenas para quem tem permissão de restaurar.
- [x] 7.3 Adicionar ação "Excluir" (com confirmação via SweetAlert2) e "Restaurar" por linha na tabela, condicionadas às permissões.
- [x] 7.4 Atualizar `resources/js/types/imovel.ts` com os novos campos (`indicadores`, filtro `excluidos`, `deleted_at`).

## 8. Frontend — mídias e histórico

- [x] 8.1 Reestruturar `Show.vue` para usar tabs (Resumo, Fotos, Documentos, Histórico), reaproveitando os componentes existentes (`CardCaracteristicas`, `CardDadosComerciais`) na aba Resumo.
- [x] 8.2 Criar componente de gerenciamento incremental de fotos (upload, remover, definir principal) chamando os novos endpoints, substituindo o upload de foto avulso na aba Fotos do Show.
- [x] 8.3 Criar componente de gerenciamento incremental de documentos (upload com seleção de tipo, remover) chamando os novos endpoints, na aba Documentos do Show.
- [x] 8.4 Criar componente de linha do tempo de histórico (data, tipo de evento, descrição, usuário) consumindo a relação `historicos` paginada, na aba Histórico do Show.
- [x] 8.5 Atualizar `ImovelController::show()` para carregar `historicos` paginado (ou endpoint próprio, se a paginação do histórico exigir carregamento assíncrono).

## 9. Testes

- [x] 9.1 Teste: não deve excluir imóvel com contrato ativo.
- [x] 9.2 Teste: deve excluir (soft delete) imóvel sem contrato ativo e registrar histórico.
- [x] 9.3 Teste: deve restaurar imóvel excluído e registrar histórico.
- [x] 9.4 Teste: usuário sem permissão não consegue excluir/restaurar.
- [x] 9.5 Teste: upload/remoção de foto via endpoint dedicado atualiza galeria e registra histórico.
- [x] 9.6 Teste: definir foto principal desmarca a anterior; primeira foto vira principal automaticamente.
- [x] 9.7 Teste: upload/remoção de documento via endpoint dedicado registra histórico.
- [x] 9.8 Teste: indicadores da listagem refletem contagens corretas e ignoram imóveis excluídos.
- [x] 9.9 Teste: histórico é criado ao cadastrar, editar e alterar status do imóvel.

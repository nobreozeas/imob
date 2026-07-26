## Why

O módulo de Imóveis já possui CRUD funcional (cadastro, edição, listagem com filtros, alteração de status, upload de fotos/documentos embutido no formulário), mas ainda não atende integralmente ao PRD e à especificação de Gestão de Imóveis: não há exclusão lógica nem restauração, não há histórico auditável dos eventos do imóvel, o gerenciamento de fotos/documentos só ocorre dentro do formulário completo (sem ações incrementais dedicadas) e a listagem não exibe os indicadores rápidos exigidos (total, disponíveis, alugados, reservados, inativos). Sem isso, a "Definição de Pronto" do PRD (soft delete, rastreabilidade, histórico de eventos) não é atendida para esta funcionalidade.

## What Changes

- Adicionar exclusão lógica (soft delete) de imóveis, bloqueando a exclusão quando houver contrato ativo vinculado.
- Adicionar restauração de imóveis excluídos, restrita a usuários com permissão específica.
- Adicionar filtro "imóveis excluídos" na listagem e ação de restaurar por linha.
- Adicionar indicadores rápidos no topo da listagem (total, disponíveis, alugados, reservados, inativos).
- Adicionar endpoints dedicados para gerenciar fotos (upload, remoção, definir principal) e documentos (upload, remoção) fora do fluxo de edição completa, com ações próprias na tela de detalhes.
- Adicionar histórico de eventos do imóvel (criação, edição, alteração de status, foto/documento adicionados, vínculo a contrato, exclusão, restauração), exibido em nova aba "Histórico" na tela de detalhes.
- Adicionar novas permissões: `imoveis.destroy`, `imoveis.restore`, `imoveis.gerenciar-fotos`, `imoveis.gerenciar-documentos`.

## Capabilities

### New Capabilities
- `imovel-ciclo-de-vida`: exclusão lógica e restauração de imóveis, permissões associadas, indicadores rápidos e filtro de excluídos na listagem.
- `imovel-midias`: endpoints e ações dedicadas para gerenciar fotos (upload, remoção, foto principal) e documentos (upload, remoção) do imóvel de forma incremental.
- `imovel-historico`: registro e exibição do histórico de eventos relevantes do imóvel.

### Modified Capabilities
- Nenhuma. Não há spec previamente mesclada em `openspec/specs/` para Gestão de Imóveis; o CRUD, wizard e alteração de status já implementados permanecem como estão e não são reespecificados nesta mudança.

## Impact

- Backend: `ImovelController`, `ImovelService`, `ImovelPolicy`, novas classes `FotoImovelController`/`DocumentoImovelController`, novo model/migration `ImovelHistorico`/`imovel_historicos`, novo `ImovelHistoricoService`, novas Form Requests para fotos/documentos, seeder de permissões (`ImovelPermissionsSeeder`), rotas em `routes/web.php`.
- Frontend: `resources/js/Pages/Admin/Imoveis/Index.vue` (indicadores, filtro de excluídos, ações restaurar), `Show.vue` (tabs, ações de mídia, aba histórico), novos componentes em `resources/js/Components/Imoveis/` (cards de indicadores, gerenciador de fotos/documentos, linha do tempo de histórico), `resources/js/types/imovel.ts`.
- Banco de dados: nova tabela `imovel_historicos`; nenhuma alteração destrutiva nas tabelas existentes de `imoveis`, `imovel_fotos`, `imovel_documentos`.

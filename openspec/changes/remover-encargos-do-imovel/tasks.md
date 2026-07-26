## 1. Banco de dados

- [x] 1.1 Criar migration removendo `responsavel_iptu`, `responsavel_agua`, `responsavel_energia`, `responsavel_condominio` de `imovel_dados_comerciais` (com `down()` recriando as colunas nullable, sem dado).
- [x] 1.2 Rodar a migration em ambiente local (`docker compose exec app php artisan migrate`).

## 2. Backend

- [x] 2.1 Remover os 4 campos de `$fillable` e `casts()` (se aplicável) em `app/Models/ImovelDadosComerciais.php`.
- [x] 2.2 Remover as regras `dados_comerciais.responsavel_iptu`, `dados_comerciais.responsavel_agua`, `dados_comerciais.responsavel_energia`, `dados_comerciais.responsavel_condominio` de `StoreImovelRequest` e `UpdateImovelRequest`.
- [x] 2.3 Remover os 4 campos de `dadosComerciaisData()` em `app/Services/Imoveis/ImovelService.php`.

## 3. Frontend

- [x] 3.1 Remover o tipo `ResponsavelCusto` e os 4 campos de `ImovelDadosComerciais`/`FormularioImovelData` em `resources/js/types/imovel.ts`.
- [x] 3.2 Remover os 4 campos do estado inicial do formulário em `resources/js/Pages/Admin/Imoveis/Create.vue`.
- [x] 3.3 Remover os 4 campos do preenchimento do formulário em `resources/js/Pages/Admin/Imoveis/Edit.vue`.
- [x] 3.4 Remover a entrada desses campos no mapa campo→step em `resources/js/Components/Imoveis/WizardImovel.vue`.
- [x] 3.5 Remover os 4 `<select>` de responsável em `resources/js/Components/Imoveis/WizardStep4DadosComerciais.vue`.
- [x] 3.6 Remover a exibição dos 4 campos (`labelResponsavel(...)`) em `resources/js/Components/Imoveis/CardDadosComerciais.vue`.

## 4. Verificação

- [x] 4.1 Rodar `php artisan test` (com env sqlite explícito, ver nota da change `gestao-de-imoveis`) e confirmar que nada quebrou.
- [x] 4.2 Testar manualmente o fluxo de cadastro/edição de imóvel: Step 4 não deve mais perguntar responsável por IPTU/água/energia/condomínio.
- [x] 4.3 Confirmar visualmente que a tela de detalhes do imóvel (`CardDadosComerciais`) não exibe mais esses campos.

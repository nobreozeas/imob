## Why

O cadastro de imóvel hoje pergunta quem paga IPTU, água, energia e condomínio (`responsavel_iptu`, `responsavel_agua`, `responsavel_energia`, `responsavel_condominio` em `imovel_dados_comerciais`), como se fosse uma característica fixa do imóvel. Isso contraria a regra do PRD (seção 13.5): "Imóveis não devem possuir campos de encargos, esses pertencem ao contrato". Esses valores podem mudar a cada contrato (o mesmo imóvel pode ter locações com regras de encargo diferentes ao longo do tempo), e o sistema já possui a estrutura correta para isso — `contrato_encargos` / `ContratoEncargo` — mas ela hoje não é usada em nenhum lugar do fluxo de contratos. Ter os dois lugares (imóvel e contrato) tratando "responsável pelo encargo" cria duplicidade e confusão sobre qual é a fonte da verdade.

## What Changes

- **BREAKING**: Remover as colunas `responsavel_iptu`, `responsavel_agua`, `responsavel_energia`, `responsavel_condominio` da tabela `imovel_dados_comerciais`.
- Remover esses 4 campos do model `ImovelDadosComerciais`, de `StoreImovelRequest`/`UpdateImovelRequest` e da montagem de dados em `ImovelService`.
- Remover os 4 campos e o step correspondente no wizard de cadastro/edição de imóvel (`WizardStep4DadosComerciais.vue`), e a exibição em `CardDadosComerciais.vue`.
- Remover o tipo `ResponsavelCusto` de `resources/js/types/imovel.ts` (fica órfão após a remoção dos 4 campos) e os campos correspondentes em `ImovelDadosComerciais`/`FormularioImovelData`.
- Manter em `imovel_dados_comerciais` os campos que são apenas valores de referência (não decisão de responsabilidade): `valor_condominio`, `valor_iptu`, `condominio_incluso`, `valor_caucao_sugerido`. Não fazem parte desta mudança.

## Capabilities

### New Capabilities
- Nenhuma.

### Modified Capabilities
- `imovel-dados-comerciais`: os dados comerciais do imóvel deixam de incluir responsabilidade por encargos (IPTU, água, energia, condomínio); essa responsabilidade passa a ser modelada exclusivamente no contrato de locação, através de `contrato_encargos`.

## Impact

- Banco de dados: migration removendo as 4 colunas de `imovel_dados_comerciais` (dado existente nessas colunas é descartado; nenhum outro módulo lê essas colunas hoje, conforme levantamento).
- Backend: `app/Models/ImovelDadosComerciais.php`, `app/Http/Requests/Imoveis/StoreImovelRequest.php`, `app/Http/Requests/Imoveis/UpdateImovelRequest.php`, `app/Services/Imoveis/ImovelService.php`.
- Frontend: `resources/js/types/imovel.ts`, `resources/js/Pages/Admin/Imoveis/Create.vue`, `resources/js/Pages/Admin/Imoveis/Edit.vue`, `resources/js/Components/Imoveis/WizardImovel.vue`, `resources/js/Components/Imoveis/WizardStep4DadosComerciais.vue`, `resources/js/Components/Imoveis/CardDadosComerciais.vue`.
- Não afeta o módulo de Contratos: levantamento confirmou que nenhum código do wizard/service de contrato lê ou sugere valores a partir desses 4 campos do imóvel.

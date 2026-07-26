## REMOVED Requirements

### Requirement: Responsabilidade por encargos no cadastro de imóvel
O sistema permitia registrar, nos dados comerciais do imóvel, quem é responsável (proprietário ou inquilino) pelo pagamento de IPTU, água, energia e condomínio.

**Reason**: Duplica responsabilidade que já pertence ao contrato de locação (`contrato_encargos`) e pode variar entre contratos do mesmo imóvel ao longo do tempo, contrariando a regra do PRD de que imóveis não devem possuir campos de encargos.

**Migration**: A responsabilidade por encargo passa a ser definida exclusivamente na etapa de Encargos do wizard de contrato de locação (`contrato_encargos`). Nenhuma ação de dados é necessária para quem já usa contratos, pois esse fluxo já existe e não lia os campos removidos do imóvel.

## ADDED Requirements

### Requirement: Dados comerciais do imóvel não incluem responsabilidade por encargo
Os dados comerciais do imóvel SHALL conter apenas valores de referência (aluguel, venda, condomínio, IPTU, se o condomínio está incluso no aluguel, caução sugerida) e NÃO SHALL conter campos de responsabilidade (quem paga) por IPTU, água, energia ou condomínio.

#### Scenario: Cadastrar imóvel sem perguntar responsabilidade por encargo
- **WHEN** um usuário cadastra ou edita os dados comerciais de um imóvel
- **THEN** o formulário não apresenta campos para definir responsável por IPTU, água, energia ou condomínio

#### Scenario: Responsabilidade por encargo é definida no contrato
- **WHEN** um usuário cria um contrato de locação para um imóvel
- **THEN** a responsabilidade por cada encargo (IPTU, água, energia, condomínio, etc.) é definida na etapa de Encargos do contrato, vinculada ao contrato e não ao imóvel

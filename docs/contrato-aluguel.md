# Módulo de Contratos de Locação — Fluxo Atualizado

## Sistema de Gestão Imobiliária

Este documento descreve o fluxo atualizado do módulo de **Contratos de Locação** para o MVP do Sistema de Gestão Imobiliária.

O contrato será o ponto central da operação de aluguel, conectando:

- Imóvel
- Proprietário
- Inquilino
- Corretor
- Encargos
- Caução
- Parcelas de aluguel
- Recebimentos
- Multas
- Repasses ao proprietário
- Movimentações financeiras

---

# 1. Objetivo do módulo

Permitir que a imobiliária cadastre, acompanhe e gerencie contratos de locação de forma simples, segura e integrada ao financeiro.

O módulo deve permitir:

- Criar contratos de locação.
- Selecionar apenas imóveis disponíveis.
- Vincular imóvel, proprietário, inquilino e corretor.
- Definir dados financeiros do aluguel.
- Definir multa por atraso.
- Definir juros por atraso.
- Definir multa por quebra de contrato.
- Definir encargos contratuais.
- Registrar caução/garantia.
- Gerar parcelas de aluguel.
- Registrar pagamentos.
- Calcular taxa da imobiliária.
- Gerar repasses ao proprietário.
- Controlar rescisões e renovações.
- Manter histórico completo do contrato.

---

# 2. Regra principal sobre imóveis

Apenas imóveis com status **Disponível** poderão ser usados na criação de um novo contrato.

## Status permitidos para seleção

```text
Disponível
```

## Status bloqueados para contrato

```text
Reservado
Alugado
Inativo
```

## Regras

```text
1. O sistema deve listar apenas imóveis com status disponível na criação do contrato.
2. Um imóvel alugado não pode ser vinculado a outro contrato ativo.
3. Ao ativar o contrato, o imóvel deve mudar para status alugado.
4. Contrato em rascunho não altera o status do imóvel.
5. Ao encerrar o contrato, o imóvel pode voltar para disponível ou inativo.
6. Ao cancelar contrato não iniciado, o imóvel pode voltar para disponível.
```

---

# 3. Fluxo geral do contrato

```text
Selecionar imóvel disponível
   ↓
Selecionar ou cadastrar inquilino
   ↓
Informar dados principais do contrato
   ↓
Configurar multas e regras de pagamento
   ↓
Configurar encargos contratuais
   ↓
Configurar financeiro e caução
   ↓
Revisar dados
   ↓
Salvar como rascunho ou ativar contrato
   ↓
Gerar parcelas de aluguel
   ↓
Registrar pagamentos
   ↓
Gerar repasses ao proprietário
   ↓
Controlar renovação ou rescisão
```

---

# 4. Status do contrato

```text
rascunho
ativo
vencido
encerrado
cancelado
```

## Rascunho

Contrato ainda em elaboração.

Regras:

```text
- Não gera parcelas.
- Não altera o status do imóvel.
- Permite edição completa.
```

## Ativo

Contrato válido e em execução.

Regras:

```text
- Imóvel muda para alugado.
- Gera parcelas de aluguel.
- Permite registro de pagamentos.
- Permite geração de repasses.
```

## Vencido

Contrato passou da data final e ainda não foi renovado ou encerrado.

Regras:

```text
- Deve aparecer como alerta no dashboard.
- Permite renovação ou encerramento.
```

## Encerrado

Contrato finalizado normalmente.

Regras:

```text
- Mantém histórico financeiro.
- Pode cancelar parcelas futuras.
- Imóvel pode voltar para disponível ou inativo.
```

## Cancelado

Contrato cancelado por erro, desistência ou situação administrativa.

Regras:

```text
- Deve exigir motivo.
- Não deve apagar histórico.
- Pode cancelar parcelas futuras.
```

---

# 5. Wizard de cadastro do contrato

O cadastro do contrato deve ser feito em página completa, utilizando wizard/steps.

## Etapas

```text
1. Imóvel
2. Inquilino
3. Dados do contrato
4. Multas e regras
5. Encargos
6. Financeiro e Caução
7. Revisão
```

---

# 6. Etapa 1 — Imóvel

## Objetivo

Selecionar o imóvel que será alugado.

## Regras

```text
- Listar apenas imóveis disponíveis.
- Não permitir seleção de imóvel alugado, reservado ou inativo.
- Exibir dados resumidos do imóvel.
- Exibir proprietário vinculado ao imóvel.
```

## Campos exibidos

```text
Código do imóvel
Tipo do imóvel
Endereço
Bairro
Cidade
Proprietário
Valor sugerido de aluguel
Status
Fotos do imóvel
```

## Filtros

```text
Código
Endereço
Proprietário
Tipo
Bairro
Valor máximo
```

---

# 7. Etapa 2 — Inquilino

## Objetivo

Selecionar ou cadastrar o inquilino do contrato.

## Campos exibidos

```text
Nome
CPF
RG
Telefone
WhatsApp
Email
Profissão
Renda
```

## Ações

```text
Selecionar inquilino existente
Cadastrar novo inquilino
Editar dados básicos do inquilino
Visualizar histórico de contratos
```

---

# 8. Etapa 3 — Dados do contrato

## Objetivo

Informar os dados principais da locação.

## Campos

```text
Data de início
Data de fim
Prazo em meses
Dia de vencimento
Valor do aluguel
Corretor responsável
Observações
```

## Regras

```text
1. Data final deve ser maior que a data inicial.
2. Dia de vencimento deve estar entre 1 e 31.
3. Valor do aluguel deve ser maior que zero.
4. Corretor pode ser opcional.
5. O proprietário do contrato deve ser o proprietário atual do imóvel.
```

---

# 9. Etapa 4 — Multas e regras de pagamento

## Objetivo

Configurar as penalidades aplicáveis ao contrato.

Esta etapa contempla:

- Multa por atraso no pagamento.
- Juros por atraso.
- Dias de tolerância.
- Multa por quebra de contrato.
- Cálculo proporcional da multa de rescisão.

---

## 9.1 Multa por atraso

A multa por atraso é aplicada quando o inquilino paga o aluguel após a data de vencimento.

### Campos

```text
Aplicar multa por atraso
Percentual da multa por atraso
Aplicar juros por atraso
Percentual de juros mensal
Dias de tolerância
Observações
```

### Exemplo

```text
Valor do aluguel: R$ 1.500,00
Multa por atraso: 2%
Juros mensal: 1%
Dias de tolerância: 0
```

### Cálculo

```text
Multa por atraso = valor do aluguel x percentual da multa
Juros proporcional = valor do aluguel x percentual mensal / 30 x dias em atraso
Valor total = aluguel + multa + juros + encargos - descontos
```

### Exemplo de atraso

```text
Aluguel: R$ 1.500,00
Dias em atraso: 10
Multa 2%: R$ 30,00
Juros 1% ao mês proporcional: R$ 5,00
Total: R$ 1.535,00
```

---

## 9.2 Multa por quebra de contrato

A multa por quebra de contrato é aplicada quando o contrato é encerrado antes do prazo previsto.

### Campos

```text
Aplicar multa por rescisão antecipada
Tipo da multa de rescisão
Quantidade de aluguéis da multa
Calcular proporcional ao tempo restante
Observações
```

### Tipo da multa

```text
quantidade_alugueis
valor_fixo
```

Para o MVP, recomenda-se iniciar com:

```text
quantidade_alugueis
```

### Exemplo

```text
Valor do aluguel: R$ 1.500,00
Multa contratual: 3 aluguéis
Duração total do contrato: 12 meses
Meses restantes: 6
```

### Cálculo

```text
Multa cheia = valor do aluguel x quantidade de aluguéis
Multa proporcional = multa cheia x meses restantes / meses totais do contrato
```

### Resultado

```text
Multa cheia = R$ 4.500,00
Multa proporcional = R$ 2.250,00
```

---

# 10. Etapa 5 — Encargos contratuais

## Objetivo

Definir a responsabilidade por cada encargo vinculado ao contrato.

## Encargos padrão

```text
IPTU
Condomínio
Água
Energia
Seguro
Outro
```

## Responsáveis possíveis

```text
locador
locatario
incluso_no_aluguel
```

## Campos

```text
Tipo do encargo
Responsável
Cobrar junto ao aluguel
Valor estimado
Observações
```

## Regras

```text
1. Encargos devem ficar vinculados ao contrato.
2. Encargos não devem compor automaticamente a receita da imobiliária.
3. Encargos cobrados junto ao aluguel devem aparecer na parcela.
4. Encargos de terceiros devem ser separados da taxa da imobiliária.
```

---

# 11. Etapa 6 — Financeiro e Caução

## Objetivo

Configurar os dados financeiros mensais do contrato e a garantia/caução.

Esta etapa deve ser dividida em dois blocos:

```text
Financeiro mensal
Caução / Garantia
```

---

## 11.1 Financeiro mensal

### Campos

```text
Valor do aluguel
Percentual da taxa de administração
Valor da taxa de administração
Valor previsto de repasse ao proprietário
Gerar parcelas automaticamente
Primeiro vencimento
Quantidade de parcelas
```

### Regra de cálculo

```text
Valor da taxa de administração = valor do aluguel x percentual da taxa / 100
Valor de repasse ao proprietário = valor do aluguel - valor da taxa de administração
```

### Exemplo

```text
Aluguel: R$ 1.500,00
Taxa de administração: 10%
Receita da imobiliária: R$ 150,00
Repasse ao proprietário: R$ 1.350,00
```

---

## 11.2 Caução / Garantia

A caução faz parte das condições financeiras do contrato, mas não deve ser tratada como aluguel mensal.

## Regra principal

```text
A caução não é aluguel.
A caução não é receita operacional da imobiliária.
A caução não gera automaticamente taxa de administração.
A caução não gera repasse mensal ao proprietário.
A caução deve permanecer vinculada ao contrato.
```

## Tipos de garantia

```text
caucao_dinheiro
fiador
seguro_fianca
titulo_capitalizacao
sem_garantia
outro
```

## Campos

```text
Possui caução
Tipo de garantia
Valor da caução
Quantidade de aluguéis
Data de recebimento
Forma de recebimento
Responsável pela guarda
Status da caução
Observações
```

## Responsável pela guarda

```text
imobiliaria
proprietario
terceiro
```

## Status da caução

```text
nao_aplicavel
aguardando_recebimento
recebida
devolvida
abatida
retida_parcialmente
retida_integralmente
cancelada
```

## Exemplo

```text
Tipo de garantia: Caução em dinheiro
Valor da caução: R$ 3.000,00
Quantidade de aluguéis: 2
Data de recebimento: 01/07/2026
Forma de recebimento: PIX
Responsável pela guarda: Imobiliária
Status: Aguardando recebimento
```

---

# 12. Etapa 7 — Revisão

## Objetivo

Exibir todos os dados do contrato antes de salvar ou ativar.

## Blocos da revisão

```text
Imóvel
Proprietário
Inquilino
Corretor
Dados do contrato
Multas e regras
Encargos
Financeiro mensal
Caução / Garantia
```

## Ações

```text
Salvar como rascunho
Ativar contrato
Voltar
Cancelar
```

---

# 13. Ativação do contrato

Ao ativar o contrato, o sistema deve executar as seguintes ações:

```text
1. Validar se o imóvel ainda está disponível.
2. Alterar status do contrato para ativo.
3. Alterar status do imóvel para alugado.
4. Gerar parcelas de aluguel, se configurado.
5. Registrar histórico da ativação.
6. Criar registro de caução, se houver.
7. Criar lançamento financeiro da caução, se já recebida.
```

---

# 14. Geração de parcelas

## Regras

```text
1. Parcelas devem ser geradas conforme data de início, data de fim e dia de vencimento.
2. Contrato em rascunho não gera parcelas.
3. Parcela deve possuir mês e ano de referência.
4. Parcela deve armazenar aluguel, encargos, multa, juros, desconto e total.
5. Parcelas futuras podem ser canceladas em caso de rescisão.
```

## Status da parcela

```text
pendente
pago
vencido
cancelado
pago_parcial
```

---

# 15. Registro de pagamento

## Fluxo

```text
Usuário seleciona uma parcela
   ↓
Sistema calcula atraso, multa e juros
   ↓
Usuário informa data e forma de pagamento
   ↓
Sistema calcula total
   ↓
Usuário confirma pagamento
   ↓
Sistema registra entrada financeira
   ↓
Sistema gera repasse pendente ao proprietário
   ↓
Sistema atualiza status da parcela
```

## Campos

```text
Parcela
Data do pagamento
Forma de pagamento
Valor do aluguel
Valor dos encargos
Valor da multa
Valor dos juros
Valor do desconto
Valor total
Valor pago
Observação
```

## Formas de pagamento

```text
pix
dinheiro
cartao_credito
cartao_debito
transferencia
boleto
outro
```

---

# 16. Repasses ao proprietário

## Regra

Ao registrar o pagamento de uma parcela de aluguel, o sistema deve gerar um repasse pendente ao proprietário.

## Cálculo

```text
Valor bruto = valor recebido de aluguel
Taxa de administração = valor bruto x percentual da taxa / 100
Valor líquido = valor bruto - taxa de administração
```

## Status do repasse

```text
pendente
pago
cancelado
```

## Observação

Encargos, caução, multas e valores de terceiros não devem ser misturados automaticamente com o repasse mensal sem regra explícita.

---

# 17. Caução no financeiro

## Recebimento da caução

```text
Contrato ativo ou em ativação
   ↓
Usuário registra recebimento da caução
   ↓
Sistema cria lançamento financeiro do tipo entrada
   ↓
Categoria: caução
   ↓
Sistema atualiza status da caução para recebida
```

## Devolução da caução

```text
Contrato encerrado ou em rescisão
   ↓
Usuário informa valor a devolver
   ↓
Sistema registra movimentação da caução
   ↓
Sistema cria lançamento financeiro do tipo saída
   ↓
Sistema atualiza status da caução
```

## Retenção da caução

```text
Usuário informa valor retido
   ↓
Informa motivo da retenção
   ↓
Sistema registra movimentação
   ↓
Sistema atualiza saldo da caução
```

## Abatimento da caução

```text
Usuário informa valor a abater
   ↓
Seleciona débito a ser abatido
   ↓
Sistema registra movimentação
   ↓
Sistema reduz saldo da caução
```

---

# 18. Rescisão de contrato

## Objetivo

Encerrar o contrato antes da data final prevista.

## Campos

```text
Data da rescisão
Solicitado por
Motivo
Destino do imóvel
Ação sobre parcelas futuras
Valor da multa
Valor do desconto
Valor final da multa
Valor da caução recebida
Débitos em aberto
Valor a reter da caução
Valor a abater com caução
Valor a devolver da caução
Observações
```

## Solicitado por

```text
locatario
locador
imobiliaria
acordo
```

## Destino do imóvel

```text
disponivel
inativo
```

## Ação sobre parcelas futuras

```text
cancelar_parcelas_futuras
manter_parcelas_futuras
```

## Regras

```text
1. O sistema deve calcular multa de rescisão, se configurada.
2. O sistema deve verificar parcelas vencidas em aberto.
3. O sistema deve permitir usar caução para abater débitos.
4. O sistema deve permitir retenção parcial ou integral da caução.
5. O sistema deve permitir devolução do saldo da caução.
6. O contrato deve mudar para encerrado.
7. O imóvel deve mudar para disponível ou inativo.
8. Parcelas futuras podem ser canceladas.
9. Histórico financeiro deve ser preservado.
```

---

# 19. Renovação de contrato

## Objetivo

Criar nova vigência para um contrato existente.

## Campos

```text
Nova data de início
Nova data de fim
Novo valor do aluguel
Novo percentual da taxa de administração
Manter encargos anteriores
Manter regras de multa
Manter caução anterior
Gerar novas parcelas
Observações
```

## Regras

```text
1. Renovação deve preservar histórico do contrato anterior.
2. O sistema pode criar um novo contrato vinculado ao contrato original.
3. O sistema deve permitir reajuste de valor.
4. O sistema deve permitir manter ou alterar encargos.
5. O sistema deve permitir manter ou alterar regras de multa.
6. A caução pode ser mantida, devolvida ou complementada.
```

---

# 20. Tela de listagem de contratos

## Filtros

```text
Busca geral
Status
Proprietário
Inquilino
Imóvel
Corretor
Período de início
Período de fim
Dia de vencimento
Contratos vencidos
Contratos vencendo
```

## Colunas

```text
Código
Imóvel
Inquilino
Proprietário
Valor do aluguel
Vencimento
Data de início
Data de fim
Status
Ações
```

## Ações

```text
Visualizar
Editar
Registrar pagamento
Renovar
Encerrar
Cancelar
Excluir
```

---

# 21. Tela de detalhes do contrato

A tela de detalhes deve usar tabs.

## Tabs

```text
Resumo
Parcelas
Encargos
Caução
Repasses
Documentos
Histórico
```

---

## 21.1 Tab Resumo

Exibir:

```text
Dados do contrato
Imóvel
Proprietário
Inquilino
Corretor
Período
Dia de vencimento
Valor do aluguel
Taxa da imobiliária
Repasse previsto
Multa por atraso
Multa por rescisão
Status da caução
```

---

## 21.2 Tab Parcelas

Colunas:

```text
Referência
Vencimento
Valor do aluguel
Encargos
Multa
Juros
Desconto
Total
Status
Ações
```

---

## 21.3 Tab Encargos

Colunas:

```text
Encargo
Responsável
Cobrar junto ao aluguel
Valor estimado
Observações
```

---

## 21.4 Tab Caução

Exibir resumo:

```text
Tipo de garantia
Valor da caução
Quantidade de aluguéis
Data de recebimento
Forma de recebimento
Responsável pela guarda
Status atual
Saldo disponível
```

Movimentações:

```text
Data
Tipo
Valor
Forma
Descrição
Usuário
```

Ações:

```text
Registrar recebimento
Registrar devolução
Registrar abatimento
Registrar retenção
Registrar ajuste
```

---

## 21.5 Tab Repasses

Colunas:

```text
Referência
Valor recebido
Taxa da imobiliária
Valor líquido
Status
Data de pagamento
Ações
```

---

## 21.6 Tab Documentos

Exibir documentos vinculados ao contrato.

Exemplos:

```text
Contrato assinado
Documento do inquilino
Comprovante de caução
Comprovante de pagamento
Outros anexos
```

---

## 21.7 Tab Histórico

Registrar eventos relevantes.

Exemplos:

```text
Contrato criado
Contrato ativado
Parcelas geradas
Pagamento registrado
Repasse gerado
Caução recebida
Caução devolvida
Contrato renovado
Contrato rescindido
```

---

# 22. Tabelas em português

Todas as tabelas devem usar nomes em português, em snake_case e sem acentos.

## Tabelas principais

```text
contratos_locacao
encargos_contrato
parcelas_aluguel
repasses_proprietarios
caucoes_contrato
movimentacoes_caucao
rescisoes_contrato
renovacoes_contrato
```

---

# 23. Tabela contratos_locacao

```text
id
uuid
codigo
imovel_id
proprietario_id
inquilino_id
corretor_id
contrato_anterior_id
data_inicio
data_fim
dia_vencimento
valor_aluguel
percentual_taxa_administracao
valor_taxa_administracao
valor_repasse_proprietario
aplicar_multa_atraso
percentual_multa_atraso
aplicar_juros_atraso
percentual_juros_mensal_atraso
dias_tolerancia_atraso
aplicar_multa_rescisao
tipo_multa_rescisao
quantidade_alugueis_multa_rescisao
valor_fixo_multa_rescisao
multa_rescisao_proporcional
status
observacoes
criado_por
created_at
updated_at
deleted_at
```

---

# 24. Tabela encargos_contrato

```text
id
uuid
contrato_locacao_id
tipo_encargo
responsavel
valor_estimado
cobrar_junto_aluguel
observacoes
created_at
updated_at
deleted_at
```

---

# 25. Tabela parcelas_aluguel

```text
id
uuid
contrato_locacao_id
mes_referencia
ano_referencia
data_vencimento
valor_aluguel
valor_encargos
valor_multa_atraso
valor_juros_atraso
valor_desconto
valor_total
valor_pago
data_pagamento
forma_pagamento
status
observacoes
created_at
updated_at
deleted_at
```

---

# 26. Tabela repasses_proprietarios

```text
id
uuid
contrato_locacao_id
imovel_id
proprietario_id
parcela_aluguel_id
valor_bruto
valor_taxa_administracao
valor_liquido
status
data_pagamento
forma_pagamento
observacoes
created_at
updated_at
deleted_at
```

---

# 27. Tabela caucoes_contrato

```text
id
uuid
contrato_locacao_id
tipo_garantia
valor_caucao
quantidade_alugueis
data_recebimento
forma_recebimento
responsavel_guarda
status
observacoes
created_at
updated_at
deleted_at
```

---

# 28. Tabela movimentacoes_caucao

```text
id
uuid
caucao_contrato_id
tipo_movimentacao
valor
data_movimentacao
forma_movimentacao
descricao
criado_por
created_at
updated_at
```

## Tipos de movimentação

```text
recebimento
devolucao
abatimento
retencao_parcial
retencao_integral
ajuste
```

---

# 29. Tabela rescisoes_contrato

```text
id
uuid
contrato_locacao_id
data_rescisao
motivo
solicitado_por
meses_restantes
valor_multa_rescisao
valor_desconto
valor_final_multa
valor_caucao_recebida
valor_caucao_retida
valor_caucao_abatida
valor_caucao_devolvida
destino_imovel
acao_parcelas_futuras
observacoes
criado_por
created_at
updated_at
```

---

# 30. Tabela renovacoes_contrato

```text
id
uuid
contrato_original_id
novo_contrato_id
data_renovacao
valor_aluguel_anterior
valor_aluguel_novo
data_inicio_anterior
data_fim_anterior
nova_data_inicio
nova_data_fim
manter_encargos
manter_regras_multa
manter_caucao
gerar_novas_parcelas
observacoes
criado_por
created_at
updated_at
```

---

# 31. Rotas sugeridas Laravel

```php
Route::resource('contratos-locacao', ContratoLocacaoController::class);

Route::post('contratos-locacao/{contrato}/ativar', [ContratoLocacaoController::class, 'ativar'])
    ->name('contratos-locacao.ativar');

Route::post('contratos-locacao/{contrato}/parcelas/{parcela}/pagamento', [PagamentoAluguelController::class, 'store'])
    ->name('contratos-locacao.parcelas.pagamento');

Route::post('contratos-locacao/{contrato}/rescindir', [RescisaoContratoController::class, 'store'])
    ->name('contratos-locacao.rescindir');

Route::post('contratos-locacao/{contrato}/renovar', [RenovacaoContratoController::class, 'store'])
    ->name('contratos-locacao.renovar');

Route::post('contratos-locacao/{contrato}/caucao/movimentacoes', [MovimentacaoCaucaoController::class, 'store'])
    ->name('contratos-locacao.caucao.movimentacoes');

Route::post('repasses-proprietarios/{repasse}/marcar-como-pago', [RepasseProprietarioController::class, 'marcarComoPago'])
    ->name('repasses-proprietarios.marcar-como-pago');
```

---

# 32. Páginas Vue/Inertia sugeridas

```text
resources/js/Pages/ContratosLocacao/Index.vue
resources/js/Pages/ContratosLocacao/Create.vue
resources/js/Pages/ContratosLocacao/Edit.vue
resources/js/Pages/ContratosLocacao/Show.vue
```

---

# 33. Componentes Vue sugeridos

```text
resources/js/Components/ContratosLocacao/ContratoWizard.vue
resources/js/Components/ContratosLocacao/Steps/StepImovel.vue
resources/js/Components/ContratosLocacao/Steps/StepInquilino.vue
resources/js/Components/ContratosLocacao/Steps/StepDadosContrato.vue
resources/js/Components/ContratosLocacao/Steps/StepMultas.vue
resources/js/Components/ContratosLocacao/Steps/StepEncargos.vue
resources/js/Components/ContratosLocacao/Steps/StepFinanceiroCaucao.vue
resources/js/Components/ContratosLocacao/Steps/StepRevisao.vue
resources/js/Components/ContratosLocacao/ContratoResumoCards.vue
resources/js/Components/ContratosLocacao/TabsContrato.vue
resources/js/Components/ContratosLocacao/TabelaParcelas.vue
resources/js/Components/ContratosLocacao/TabelaRepasses.vue
resources/js/Components/ContratosLocacao/AbaCaucao.vue
resources/js/Components/ContratosLocacao/ModalRegistrarPagamento.vue
resources/js/Components/ContratosLocacao/ModalRescisaoContrato.vue
resources/js/Components/ContratosLocacao/ModalRenovarContrato.vue
resources/js/Components/ContratosLocacao/ModalMovimentacaoCaucao.vue
```

---

# 34. Services sugeridos

```text
ContratoLocacaoService
GerarParcelasContratoService
PagamentoAluguelService
RepasseProprietarioService
CalcularMultaAtrasoService
CalcularMultaRescisaoService
CaucaoContratoService
RescisaoContratoService
RenovacaoContratoService
```

---

# 35. Regras de negócio consolidadas

```text
1. Apenas imóveis disponíveis podem ser vinculados a contratos.
2. Contratos em rascunho não alteram status do imóvel.
3. Contratos ativos alteram o imóvel para alugado.
4. Um imóvel não pode possuir dois contratos ativos simultâneos.
5. Contrato ativo pode gerar parcelas automaticamente.
6. Multa por atraso deve considerar dias de tolerância.
7. Juros por atraso devem ser proporcionais aos dias de atraso.
8. Multa por rescisão pode ser proporcional ao tempo restante.
9. Encargos devem ficar vinculados ao contrato.
10. Encargos não são automaticamente receita da imobiliária.
11. Caução não é aluguel.
12. Caução não é receita operacional da imobiliária.
13. Caução não gera taxa de administração automaticamente.
14. Caução não gera repasse mensal automaticamente.
15. Caução pode ser recebida, devolvida, abatida ou retida.
16. Toda movimentação de caução deve ter histórico.
17. Ao registrar pagamento de aluguel, o sistema deve criar entrada financeira.
18. Ao registrar pagamento de aluguel, o sistema deve calcular taxa da imobiliária.
19. Ao registrar pagamento de aluguel, o sistema deve gerar repasse pendente ao proprietário.
20. Rescisão deve preservar histórico financeiro.
21. Renovação deve preservar histórico do contrato anterior.
22. Exclusões devem usar Soft Delete.
```

---

# 36. Ordem recomendada de implementação

```text
1. Criar migrations das tabelas do contrato.
2. Criar models e relacionamentos.
3. Criar enums/status do contrato, parcelas, repasses e caução.
4. Criar tela de listagem de contratos.
5. Criar wizard de cadastro.
6. Implementar seleção apenas de imóveis disponíveis.
7. Implementar dados básicos do contrato.
8. Implementar multas e regras.
9. Implementar encargos.
10. Implementar financeiro e caução.
11. Implementar ativação do contrato.
12. Implementar geração de parcelas.
13. Implementar tela de detalhes.
14. Implementar registro de pagamento.
15. Implementar geração de repasses.
16. Implementar aba de caução.
17. Implementar movimentações de caução.
18. Implementar rescisão.
19. Implementar renovação.
20. Implementar notificações por email.
```

---

# 37. Observações para MVP

Para manter o MVP simples, recomenda-se implementar inicialmente:

```text
Contrato com imóvel, proprietário, inquilino e corretor.
Multa por atraso com percentual fixo.
Juros mensal proporcional.
Multa por rescisão baseada em quantidade de aluguéis.
Cálculo proporcional da multa de rescisão.
Encargos vinculados ao contrato.
Caução em dinheiro.
Movimentações básicas de caução.
Parcelas mensais.
Pagamento de aluguel.
Repasses ao proprietário.
Rescisão e renovação simples.
```

Deixar para fases futuras:

```text
Correção monetária automática.
Reajuste automático por índice.
Assinatura digital.
Boleto/PIX automático.
Portal do proprietário.
Portal do inquilino.
Integração com WhatsApp.
Vistoria digital.
```

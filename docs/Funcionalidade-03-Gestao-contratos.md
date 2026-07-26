# Funcionalidade — Contratos de Locação

## Sistema de Gestão Imobiliária

Este documento descreve a funcionalidade de **Contratos de Locação** do Sistema de Gestão Imobiliária.

O módulo de contratos será o ponto central da operação de aluguel, conectando:

- Imóvel;
- Proprietário;
- Inquilino;
- Corretor;
- Encargos;
- Caução/Garantia;
- Parcelas de aluguel;
- Recebimentos;
- Multas;
- Repasses ao proprietário;
- Movimentações financeiras;
- Histórico do contrato.

---

# 1. Objetivo da funcionalidade

Permitir que a imobiliária cadastre, acompanhe e gerencie contratos de aluguel de forma simples, segura e integrada ao financeiro.

A funcionalidade deve permitir:

- Criar contratos de locação;
- Selecionar apenas imóveis disponíveis;
- Vincular imóvel, proprietário, inquilino e corretor;
- Definir dados financeiros do aluguel;
- Definir multa por atraso;
- Definir juros por atraso;
- Definir multa por quebra de contrato;
- Definir encargos contratuais;
- Registrar caução/garantia;
- Gerar parcelas de aluguel;
- Registrar pagamentos;
- Calcular taxa da imobiliária;
- Gerar repasses ao proprietário;
- Controlar rescisões;
- Controlar renovações;
- Manter histórico completo do contrato.

---

# 2. Escopo do MVP

Para o MVP, o módulo de contratos deve contemplar:

## 2.1 Cadastro do contrato

- Criar contrato como rascunho;
- Selecionar imóvel disponível;
- Vincular proprietário automaticamente pelo imóvel;
- Selecionar inquilino;
- Selecionar corretor responsável, opcional;
- Definir data inicial e data final;
- Definir dia de vencimento;
- Definir valor do aluguel;
- Definir percentual da taxa de administração;
- Configurar multa por atraso;
- Configurar juros por atraso;
- Configurar multa por rescisão antecipada;
- Configurar encargos;
- Configurar caução/garantia;
- Salvar como rascunho;
- Ativar contrato.

## 2.2 Após ativação

- Alterar status do imóvel para alugado;
- Gerar parcelas mensais de aluguel;
- Permitir registro de pagamento;
- Calcular multa e juros por atraso;
- Gerar entrada financeira;
- Gerar repasse pendente ao proprietário;
- Permitir rescisão;
- Permitir renovação;
- Manter histórico das ações.

---

# 3. Status do contrato

```text
rascunho
ativo
vencido
encerrado
cancelado
```

## 3.1 Rascunho

Contrato ainda em elaboração.

Regras:

- Não gera parcelas;
- Não altera status do imóvel;
- Permite edição completa;
- Pode ser ativado posteriormente.

## 3.2 Ativo

Contrato vigente.

Regras:

- Imóvel muda para alugado;
- Gera parcelas de aluguel;
- Permite registro de pagamentos;
- Permite geração de repasses;
- Permite rescisão e renovação.

## 3.3 Vencido

Contrato cuja data final já passou e ainda não foi encerrado ou renovado.

Regras:

- Deve aparecer como alerta no dashboard;
- Permite renovação;
- Permite encerramento;
- Mantém cobranças e histórico financeiro.

## 3.4 Encerrado

Contrato finalizado normalmente ou por rescisão.

Regras:

- Mantém histórico financeiro;
- Pode cancelar parcelas futuras;
- Imóvel pode voltar para disponível ou inativo;
- Não permite novos pagamentos comuns, salvo ajustes administrativos.

## 3.5 Cancelado

Contrato cancelado por erro, desistência ou situação administrativa.

Regras:

- Deve exigir motivo;
- Não deve apagar histórico;
- Pode cancelar parcelas futuras;
- Não deve excluir fisicamente o registro.

---

# 4. Regra principal sobre imóveis

Apenas imóveis com status **Disponível** podem ser usados na criação de um novo contrato.

## 4.1 Status permitidos para seleção

```text
Disponível
```

## 4.2 Status bloqueados para contrato

```text
Reservado
Alugado
Inativo
```

## 4.3 Regras

1. O sistema deve listar apenas imóveis disponíveis na criação do contrato.
2. Um imóvel alugado não pode ser vinculado a outro contrato ativo.
3. Ao ativar o contrato, o imóvel deve mudar para status alugado.
4. Contrato em rascunho não altera o status do imóvel.
5. Ao encerrar o contrato, o imóvel pode voltar para disponível ou inativo.
6. Ao cancelar contrato não iniciado, o imóvel pode voltar para disponível.

---

# 5. Fluxo geral do contrato

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

# 6. Wizard de cadastro do contrato

O cadastro do contrato deve ser feito em página completa, utilizando wizard/steps.

## 6.1 Etapas

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

# 7. Etapa 1 — Imóvel

## Objetivo

Selecionar o imóvel que será alugado.

## Regras

- Listar apenas imóveis disponíveis;
- Não permitir seleção de imóvel alugado, reservado ou inativo;
- Exibir dados resumidos do imóvel;
- Exibir proprietário vinculado ao imóvel.

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

# 8. Etapa 2 — Inquilino

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

# 9. Etapa 3 — Dados do contrato

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

1. Data final deve ser maior que a data inicial.
2. Dia de vencimento deve estar entre 1 e 31.
3. Valor do aluguel deve ser maior que zero.
4. Corretor pode ser opcional.
5. O proprietário do contrato deve ser o proprietário atual do imóvel.
6. O prazo em meses pode ser calculado automaticamente a partir das datas.

---

# 10. Etapa 4 — Multas e regras de pagamento

## Objetivo

Configurar as penalidades aplicáveis ao contrato.

Esta etapa contempla:

- Multa por atraso no pagamento;
- Juros por atraso;
- Dias de tolerância;
- Multa por quebra de contrato;
- Cálculo proporcional da multa de rescisão.

---

## 10.1 Multa por atraso

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

### Cálculo

```text
Multa por atraso = valor do aluguel x percentual da multa
Juros proporcional = valor do aluguel x percentual mensal / 30 x dias em atraso
Valor total = aluguel + multa + juros + encargos - descontos
```

### Exemplo

```text
Aluguel: R$ 1.500,00
Dias em atraso: 10
Multa por atraso: 2%
Juros mensal: 1%

Multa: R$ 30,00
Juros proporcional: R$ 5,00
Total: R$ 1.535,00
```

---

## 10.2 Multa por quebra de contrato

A multa por quebra de contrato é aplicada quando o contrato é encerrado antes do prazo previsto.

### Campos

```text
Aplicar multa por rescisão antecipada
Tipo da multa de rescisão
Quantidade de aluguéis da multa
Valor fixo da multa
Calcular proporcional ao tempo restante
Observações
```

### Tipos de multa

```text
quantidade_alugueis
valor_fixo
```

Para o MVP, recomenda-se iniciar com:

```text
quantidade_alugueis
```

### Cálculo proporcional

```text
Multa cheia = valor do aluguel x quantidade de aluguéis
Multa proporcional = multa cheia x meses restantes / meses totais do contrato
```

### Exemplo

```text
Valor do aluguel: R$ 1.500,00
Multa contratual: 3 aluguéis
Duração total do contrato: 12 meses
Meses restantes: 6

Multa cheia = R$ 4.500,00
Multa proporcional = R$ 2.250,00
```

---

# 11. Etapa 5 — Encargos contratuais

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

1. Encargos devem ficar vinculados ao contrato.
2. Encargos não devem compor automaticamente a receita da imobiliária.
3. Encargos cobrados junto ao aluguel devem aparecer na parcela.
4. Encargos de terceiros devem ser separados da taxa da imobiliária.
5. O sistema deve permitir mais de um encargo por contrato.

---

# 12. Etapa 6 — Financeiro e Caução

## Objetivo

Configurar os dados financeiros mensais do contrato e a garantia/caução.

Esta etapa deve ser dividida em dois blocos:

```text
Financeiro mensal
Caução / Garantia
```

---

## 12.1 Financeiro mensal

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

## 12.2 Caução / Garantia

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

Para o MVP, recomenda-se iniciar com:

```text
caucao_dinheiro
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

---

# 13. Etapa 7 — Revisão

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

# 14. Ativação do contrato

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

## Regra importante

A ativação deve ocorrer dentro de uma transação de banco de dados.

Exemplo:

```php
DB::transaction(function () {
    // validar imóvel
    // ativar contrato
    // atualizar imóvel
    // gerar parcelas
    // registrar histórico
});
```

---

# 15. Geração de parcelas

## Regras

1. Parcelas devem ser geradas conforme data de início, data de fim e dia de vencimento.
2. Contrato em rascunho não gera parcelas.
3. Parcela deve possuir mês e ano de referência.
4. Parcela deve armazenar aluguel, encargos, multa, juros, desconto e total.
5. Parcelas futuras podem ser canceladas em caso de rescisão.
6. Parcelas não devem ser duplicadas para o mesmo contrato e mesma referência.

## Status da parcela

```text
pendente
pago
vencido
cancelado
pago_parcial
```

---

# 16. Registro de pagamento

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

# 17. Repasses ao proprietário

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

# 18. Caução no financeiro

## 18.1 Recebimento da caução

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

## 18.2 Devolução da caução

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

## 18.3 Retenção da caução

```text
Usuário informa valor retido
   ↓
Informa motivo da retenção
   ↓
Sistema registra movimentação
   ↓
Sistema atualiza saldo da caução
```

## 18.4 Abatimento da caução

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

# 19. Rescisão de contrato

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

1. O sistema deve calcular multa de rescisão, se configurada.
2. O sistema deve verificar parcelas vencidas em aberto.
3. O sistema deve permitir usar caução para abater débitos.
4. O sistema deve permitir retenção parcial ou integral da caução.
5. O sistema deve permitir devolução do saldo da caução.
6. O contrato deve mudar para encerrado.
7. O imóvel deve mudar para disponível ou inativo.
8. Parcelas futuras podem ser canceladas.
9. Histórico financeiro deve ser preservado.

---

# 20. Renovação de contrato

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

1. Renovação deve preservar histórico do contrato anterior.
2. O sistema pode criar um novo contrato vinculado ao contrato original.
3. O sistema deve permitir reajuste de valor.
4. O sistema deve permitir manter ou alterar encargos.
5. O sistema deve permitir manter ou alterar regras de multa.
6. A caução pode ser mantida, devolvida ou complementada.
7. O contrato anterior deve ser encerrado ou marcado como renovado, conforme regra definida no sistema.

---

# 21. Tela de listagem de contratos

## Caminho sugerido

```text
resources/js/Pages/ContratosLocacao/Index.vue
```

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

# 22. Tela de cadastro de contrato

## Caminho sugerido

```text
resources/js/Pages/ContratosLocacao/Create.vue
```

## Componente principal

```text
resources/js/Components/ContratosLocacao/ContratoWizard.vue
```

## Comportamento esperado

- Página completa;
- Wizard em etapas;
- Validação por etapa;
- Botões de avançar e voltar;
- Possibilidade de salvar como rascunho;
- Possibilidade de ativar contrato na revisão.

---

# 23. Tela de detalhes do contrato

## Caminho sugerido

```text
resources/js/Pages/ContratosLocacao/Show.vue
```

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

## 23.1 Tab Resumo

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
Status do contrato
```

---

## 23.2 Tab Parcelas

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

Ações:

```text
Registrar pagamento
Visualizar pagamento
Cancelar parcela
```

---

## 23.3 Tab Encargos

Colunas:

```text
Encargo
Responsável
Cobrar junto ao aluguel
Valor estimado
Observações
```

---

## 23.4 Tab Caução

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

## 23.5 Tab Repasses

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

Ações:

```text
Marcar como pago
Visualizar detalhes
Cancelar repasse
```

---

## 23.6 Tab Documentos

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

## 23.7 Tab Histórico

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
Contrato cancelado
```

---

# 24. Tabelas do banco de dados

Todas as tabelas devem usar nomes em português, em `snake_case` e sem acentos.

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
historicos_contrato
documentos_contrato
```

---

# 25. Tabela contratos_locacao

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

# 26. Tabela encargos_contrato

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

# 27. Tabela parcelas_aluguel

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

# 28. Tabela repasses_proprietarios

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

# 29. Tabela caucoes_contrato

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

# 30. Tabela movimentacoes_caucao

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

---

# 31. Tabela rescisoes_contrato

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

# 32. Tabela renovacoes_contrato

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

# 33. Tabela historicos_contrato

```text
id
uuid
contrato_locacao_id
tipo_evento
descricao
dados_anteriores
dados_novos
criado_por
created_at
updated_at
```

---

# 34. Tabela documentos_contrato

```text
id
uuid
contrato_locacao_id
nome
tipo_documento
arquivo
mime_type
tamanho
observacoes
criado_por
created_at
updated_at
deleted_at
```

---

# 35. Models Laravel sugeridas

```text
ContratoLocacao
EncargoContrato
ParcelaAluguel
RepasseProprietario
CaucaoContrato
MovimentacaoCaucao
RescisaoContrato
RenovacaoContrato
HistoricoContrato
DocumentoContrato
```

---

# 36. Relacionamentos principais

## ContratoLocacao

```text
belongsTo Imovel
belongsTo Proprietario
belongsTo Inquilino
belongsTo Corretor
belongsTo ContratoLocacao contratoAnterior
hasMany EncargoContrato
hasMany ParcelaAluguel
hasMany RepasseProprietario
hasOne CaucaoContrato
hasOne RescisaoContrato
hasMany HistoricoContrato
hasMany DocumentoContrato
```

## ParcelaAluguel

```text
belongsTo ContratoLocacao
hasOne RepasseProprietario
```

## CaucaoContrato

```text
belongsTo ContratoLocacao
hasMany MovimentacaoCaucao
```

## RepasseProprietario

```text
belongsTo ContratoLocacao
belongsTo Imovel
belongsTo Proprietario
belongsTo ParcelaAluguel
```

---

# 37. Controllers Laravel sugeridos

```text
ContratoLocacaoController
PagamentoAluguelController
RepasseProprietarioController
MovimentacaoCaucaoController
RescisaoContratoController
RenovacaoContratoController
DocumentoContratoController
```

---

# 38. Services Laravel sugeridos

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
HistoricoContratoService
```

---

# 39. Form Requests sugeridos

```text
StoreContratoLocacaoRequest
UpdateContratoLocacaoRequest
AtivarContratoLocacaoRequest
RegistrarPagamentoAluguelRequest
StoreMovimentacaoCaucaoRequest
StoreRescisaoContratoRequest
StoreRenovacaoContratoRequest
StoreDocumentoContratoRequest
```

---

# 40. Policies sugeridas

```text
ContratoLocacaoPolicy
ParcelaAluguelPolicy
RepasseProprietarioPolicy
CaucaoContratoPolicy
```

Permissões sugeridas:

```text
contratos.visualizar
contratos.criar
contratos.editar
contratos.excluir
contratos.ativar
contratos.cancelar
contratos.rescindir
contratos.renovar
contratos.registrar_pagamento
contratos.gerenciar_caucao
contratos.gerenciar_documentos
contratos.visualizar_historico
repasses.visualizar
repasses.marcar_como_pago
```

---

# 41. Rotas Laravel sugeridas

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

Route::post('contratos-locacao/{contrato}/documentos', [DocumentoContratoController::class, 'store'])
    ->name('contratos-locacao.documentos.store');

Route::delete('contratos-locacao/{contrato}/documentos/{documento}', [DocumentoContratoController::class, 'destroy'])
    ->name('contratos-locacao.documentos.destroy');
```

---

# 42. Páginas Vue/Inertia sugeridas

```text
resources/js/Pages/ContratosLocacao/Index.vue
resources/js/Pages/ContratosLocacao/Create.vue
resources/js/Pages/ContratosLocacao/Edit.vue
resources/js/Pages/ContratosLocacao/Show.vue
```

---

# 43. Componentes Vue sugeridos

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
resources/js/Components/ContratosLocacao/ModalDocumentoContrato.vue
```

---

# 44. Tipos TypeScript sugeridos

```ts
export type StatusContratoLocacao =
  | 'rascunho'
  | 'ativo'
  | 'vencido'
  | 'encerrado'
  | 'cancelado';

export type StatusParcelaAluguel =
  | 'pendente'
  | 'pago'
  | 'vencido'
  | 'cancelado'
  | 'pago_parcial';

export type StatusRepasseProprietario =
  | 'pendente'
  | 'pago'
  | 'cancelado';

export type StatusCaucaoContrato =
  | 'nao_aplicavel'
  | 'aguardando_recebimento'
  | 'recebida'
  | 'devolvida'
  | 'abatida'
  | 'retida_parcialmente'
  | 'retida_integralmente'
  | 'cancelada';

export interface ContratoLocacao {
  id: number;
  uuid: string;
  codigo: string;
  imovel_id: number;
  proprietario_id: number;
  inquilino_id: number;
  corretor_id?: number | null;
  data_inicio: string;
  data_fim: string;
  dia_vencimento: number;
  valor_aluguel: number;
  percentual_taxa_administracao: number;
  valor_taxa_administracao: number;
  valor_repasse_proprietario: number;
  status: StatusContratoLocacao;
  observacoes?: string | null;
}
```

---

# 45. Regras de negócio consolidadas

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
23. Operações críticas devem usar transação de banco.
24. Listagens devem ter filtros e paginação.
25. O sistema deve registrar histórico das principais ações.

---

# 46. Ordem recomendada de implementação

```text
1. Criar enums/status do módulo.
2. Criar migrations das tabelas de contratos.
3. Criar models e relacionamentos.
4. Criar policies.
5. Criar form requests.
6. Criar services principais.
7. Criar controller de contratos.
8. Criar tela de listagem de contratos.
9. Criar wizard de cadastro.
10. Implementar seleção apenas de imóveis disponíveis.
11. Implementar criação do contrato como rascunho.
12. Implementar ativação do contrato.
13. Implementar alteração do imóvel para alugado.
14. Implementar geração de parcelas.
15. Criar tela de detalhes com tabs.
16. Implementar registro de pagamento.
17. Implementar cálculo de multa e juros.
18. Implementar geração de repasse.
19. Implementar aba de caução.
20. Implementar movimentações de caução.
21. Implementar documentos do contrato.
22. Implementar histórico do contrato.
23. Implementar rescisão.
24. Implementar renovação.
25. Implementar notificações por email.
```

---

# 47. Divisão em entregas

## Entrega 1 — Núcleo do contrato

- Listagem de contratos;
- Cadastro via wizard;
- Salvar contrato como rascunho;
- Ativar contrato;
- Mudar imóvel para alugado;
- Gerar parcelas;
- Tela de detalhes com resumo e parcelas.

## Entrega 2 — Pagamentos e repasses

- Registrar pagamento de parcela;
- Calcular multa e juros;
- Gerar entrada financeira;
- Gerar repasse pendente;
- Marcar repasse como pago.

## Entrega 3 — Caução

- Registrar caução;
- Receber caução;
- Movimentar caução;
- Devolver, reter ou abater caução.

## Entrega 4 — Rescisão e renovação

- Rescindir contrato;
- Cancelar parcelas futuras;
- Definir destino do imóvel;
- Renovar contrato criando nova vigência.

## Entrega 5 — Documentos e histórico

- Anexar documentos;
- Visualizar documentos;
- Excluir documentos com soft delete;
- Registrar histórico automático das principais ações.

---

# 48. Observações para o MVP

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

---

# 49. Resumo final

A funcionalidade de **Contratos de Locação** deve ser implementada como um módulo central do sistema.

O primeiro foco deve ser:

```text
Contrato + Imóvel disponível + Inquilino + Financeiro + Parcelas
```

Depois entram:

```text
Pagamentos + Repasses + Caução + Rescisão + Renovação + Documentos + Histórico
```

Essa abordagem reduz a complexidade inicial e permite evoluir o módulo de forma segura, organizada e aderente ao MVP do Sistema de Gestão Imobiliária.

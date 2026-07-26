# Funcionalidade — Gestão Financeira

## Sistema de Gestão Imobiliária

Este documento descreve a funcionalidade de **Gestão Financeira** para o MVP do Sistema de Gestão Imobiliária.

A Gestão Financeira será responsável por centralizar os lançamentos financeiros da imobiliária, controlar receitas, despesas, recebimentos de aluguel, cauções, repasses aos proprietários, inadimplência e fluxo de caixa.

> Observação importante: no sistema, **proprietários** e **inquilinos** fazem parte da funcionalidade de **Gestão de Clientes**. Portanto, neste documento, sempre que aparecer proprietário ou inquilino, deve-se entender como um cliente com tipo `proprietario`, `inquilino` ou `ambos`.

---

# 1. Objetivo do módulo

Permitir que a imobiliária tenha controle financeiro operacional sobre os valores recebidos, valores a receber, valores pagos, valores a pagar, repasses aos proprietários e despesas internas.

O módulo deve permitir:

- Controlar entradas financeiras.
- Controlar saídas financeiras.
- Registrar recebimentos de aluguel.
- Registrar receitas diversas.
- Registrar despesas operacionais e administrativas.
- Controlar parcelas de aluguel em aberto, pagas, vencidas ou canceladas.
- Gerar repasses aos proprietários após recebimento do aluguel.
- Marcar repasses como pagos.
- Controlar cauções recebidas, devolvidas, abatidas ou retidas.
- Separar valores da imobiliária, valores do proprietário e valores de terceiros.
- Exibir indicadores financeiros.
- Emitir relatórios básicos de receitas, despesas, fluxo de caixa e repasses.

---

# 2. Conceito principal

A Gestão Financeira não deve ser apenas um cadastro manual de entradas e saídas.

Ela deve funcionar como o centro financeiro do sistema, recebendo movimentações originadas de outros módulos.

## Origem das movimentações financeiras

```text
Contrato de locação
Parcelas de aluguel
Pagamento de aluguel
Repasses ao proprietário
Caução / garantia
Despesas administrativas
Despesas operacionais
Receitas diversas
Ajustes manuais
```

---

# 3. Escopo do MVP

Para o MVP, o financeiro deve contemplar:

```text
1. Listagem de lançamentos financeiros.
2. Cadastro manual de receita diversa.
3. Cadastro manual de despesa.
4. Registro de pagamento de parcela de aluguel.
5. Geração automática de entrada financeira ao receber aluguel.
6. Geração automática de repasse pendente ao proprietário.
7. Registro de pagamento de repasse.
8. Registro de recebimento de caução.
9. Registro de devolução, abatimento ou retenção de caução.
10. Indicadores de receitas, despesas, saldo, inadimplência e repasses pendentes.
11. Relatório de fluxo de caixa básico.
```

---

# 4. Fora do escopo do MVP

As seguintes funcionalidades ficam para versões futuras:

```text
PIX automático
Boleto automático
Integração bancária
Conciliação bancária automática
Contas bancárias múltiplas
Plano de contas avançado
Centro de custo avançado
Nota fiscal
Integração contábil
DRE completo
Correção monetária automática
Reajuste automático por índice
Portal financeiro do proprietário
Portal financeiro do inquilino
```

---

# 5. Tipos de movimentação financeira

O sistema deve trabalhar inicialmente com dois tipos principais:

```text
entrada
saida
```

## Entrada

Representa dinheiro que entrou ou que será recebido pela imobiliária.

Exemplos:

```text
Recebimento de aluguel
Receita administrativa
Receita avulsa
Recebimento de caução
Multa recebida
Juros recebidos
Taxa de administração
```

## Saída

Representa dinheiro que saiu ou que será pago pela imobiliária.

Exemplos:

```text
Repasse ao proprietário
Despesa operacional
Despesa administrativa
Devolução de caução
Pagamento de fornecedor
Ajuste financeiro negativo
```

---

# 6. Categorias financeiras

As categorias ajudam a organizar o financeiro.

## Categorias de entrada sugeridas

```text
aluguel
receita_diversa
taxa_administracao
multa_atraso
juros_atraso
caucao
ajuste_positivo
```

## Categorias de saída sugeridas

```text
repasse_proprietario
despesa_operacional
despesa_administrativa
fornecedor
devolucao_caucao
manutencao_imovel
comissao_corretor
ajuste_negativo
```

---

# 7. Status do lançamento financeiro

```text
pendente
pago
cancelado
estornado
```

## Pendente

Lançamento previsto, ainda não pago ou recebido.

Exemplos:

```text
Despesa cadastrada com vencimento futuro
Receita diversa ainda não recebida
Repasse ao proprietário ainda não pago
```

## Pago

Lançamento liquidado.

Exemplos:

```text
Aluguel recebido
Despesa paga
Repasse pago
Caução recebida
```

## Cancelado

Lançamento cancelado por erro, desistência ou alteração administrativa.

Regras:

```text
- Não deve apagar o histórico.
- Deve exigir motivo do cancelamento.
- Deve registrar usuário responsável.
```

## Estornado

Lançamento pago que precisou ser revertido.

Regras:

```text
- Deve preservar o lançamento original.
- Deve criar movimentação de estorno.
- Deve exigir motivo.
```

---

# 8. Formas de pagamento

```text
pix
dinheiro
cartao_credito
cartao_debito
transferencia
boleto
cheque
outro
```

---

# 9. Regra da taxa da imobiliária

A imobiliária recebe somente o percentual definido no contrato de locação.

## Exemplo

```text
Aluguel: R$ 1.500,00
Taxa de administração: 10%
Receita da imobiliária: R$ 150,00
Repasse ao proprietário: R$ 1.350,00
```

## Fórmulas

```text
Valor da taxa de administração = valor do aluguel x percentual da taxa / 100
Valor líquido do proprietário = valor do aluguel - valor da taxa de administração
```

---

# 10. Separação dos valores

O sistema deve separar claramente:

```text
1. Receita da imobiliária.
2. Valores pertencentes ao proprietário.
3. Valores de terceiros.
4. Valores de caução.
5. Multas e juros.
6. Despesas internas da imobiliária.
```

## Regra principal

```text
Nem todo valor recebido é receita da imobiliária.
```

Exemplo:

```text
Se a imobiliária recebe R$ 1.500,00 de aluguel e possui taxa de administração de 10%, apenas R$ 150,00 são receita da imobiliária.
Os R$ 1.350,00 restantes pertencem ao proprietário e devem ser tratados como repasse.
```

---

# 11. Encargos no financeiro

Encargos como IPTU, condomínio, água, energia, seguro ou outros valores vinculados ao contrato não devem compor automaticamente a receita da imobiliária.

## Regras

```text
1. Encargos podem ser cobrados junto à parcela de aluguel.
2. Encargos devem aparecer separados no detalhamento da parcela.
3. Encargos não entram automaticamente no cálculo da taxa de administração.
4. Encargos não devem ser repassados ao proprietário sem regra explícita.
5. Encargos pagos a terceiros devem ser classificados como valores de terceiros.
```

---

# 12. Fluxo de recebimento de aluguel

```text
Usuário acessa uma parcela de aluguel
   ↓
Sistema calcula vencimento, multa, juros, descontos e total
   ↓
Usuário informa data de pagamento e forma de pagamento
   ↓
Sistema registra pagamento da parcela
   ↓
Sistema cria entrada financeira
   ↓
Sistema calcula taxa da imobiliária
   ↓
Sistema gera repasse pendente ao proprietário
   ↓
Sistema atualiza indicadores financeiros
```

---

# 13. Cálculo do recebimento de aluguel

## Campos considerados

```text
Valor do aluguel
Valor dos encargos
Valor da multa por atraso
Valor dos juros por atraso
Valor de desconto
Valor pago
Data de pagamento
Forma de pagamento
```

## Fórmula do total da parcela

```text
Valor total = aluguel + encargos + multa + juros - desconto
```

## Fórmula da taxa da imobiliária

```text
Taxa da imobiliária = valor do aluguel x percentual da taxa de administração / 100
```

## Fórmula do repasse

```text
Repasse ao proprietário = valor do aluguel - taxa da imobiliária
```

## Observação

Para o MVP, a taxa da imobiliária deve incidir apenas sobre o valor do aluguel, não sobre encargos, caução, multa ou juros, salvo futura configuração explícita.

---

# 14. Fluxo de repasse ao proprietário

```text
Aluguel é recebido
   ↓
Sistema calcula taxa da imobiliária
   ↓
Sistema cria repasse pendente
   ↓
Usuário acessa área de repasses
   ↓
Usuário confere valor líquido
   ↓
Usuário informa data e forma de pagamento
   ↓
Sistema marca repasse como pago
   ↓
Sistema cria ou atualiza saída financeira vinculada ao repasse
```

---

# 15. Status do repasse

```text
pendente
pago
cancelado
```

## Pendente

Repasse gerado, mas ainda não pago ao proprietário.

## Pago

Repasse efetivamente pago ao proprietário.

## Cancelado

Repasse cancelado por erro, estorno ou alteração administrativa.

---

# 16. Caução no financeiro

A caução deve ser controlada pelo financeiro, mas não deve ser tratada como aluguel nem como receita operacional da imobiliária.

## Regras principais

```text
1. Caução não é aluguel.
2. Caução não é receita operacional da imobiliária.
3. Caução não gera taxa de administração automaticamente.
4. Caução não gera repasse mensal automaticamente.
5. Caução deve permanecer vinculada ao contrato.
6. Toda movimentação da caução deve gerar histórico.
```

---

# 17. Fluxo de recebimento de caução

```text
Contrato possui caução configurada
   ↓
Usuário registra recebimento da caução
   ↓
Sistema cria entrada financeira
   ↓
Categoria: caução
   ↓
Sistema atualiza status da caução para recebida
   ↓
Sistema registra movimentação da caução
```

---

# 18. Fluxo de devolução de caução

```text
Contrato encerrado ou em rescisão
   ↓
Usuário informa valor a devolver
   ↓
Sistema cria saída financeira
   ↓
Categoria: devolucao_caucao
   ↓
Sistema registra movimentação da caução
   ↓
Sistema atualiza saldo e status da caução
```

---

# 19. Fluxo de retenção de caução

```text
Usuário informa valor retido
   ↓
Usuário informa motivo da retenção
   ↓
Sistema registra movimentação da caução
   ↓
Sistema atualiza saldo da caução
   ↓
Sistema atualiza status para retida_parcialmente ou retida_integralmente
```

---

# 20. Fluxo de abatimento de caução

```text
Usuário informa valor a abater
   ↓
Seleciona débito em aberto
   ↓
Sistema registra abatimento
   ↓
Sistema atualiza saldo da caução
   ↓
Sistema atualiza parcela/débito relacionado
```

---

# 21. Despesas

A funcionalidade deve permitir o cadastro manual de despesas.

## Tipos de despesas do MVP

```text
despesa_operacional
despesa_administrativa
manutencao_imovel
fornecedor
comissao_corretor
outra
```

## Exemplos

```text
Energia da imobiliária
Internet
Aluguel da sala comercial
Material de escritório
Manutenção em imóvel administrado
Serviço de chaveiro
Serviço de limpeza
Comissão de corretor
```

---

# 22. Receita diversa

A funcionalidade deve permitir cadastro manual de receitas que não sejam aluguel.

## Exemplos

```text
Taxa de cadastro
Taxa administrativa avulsa
Serviço de vistoria
Serviço de intermediação
Receita eventual
Ajuste positivo
```

---

# 23. Tela principal da Gestão Financeira

A tela principal deve apresentar uma visão geral do financeiro.

## Cards de indicadores

```text
Receitas do mês
Despesas do mês
Saldo do mês
Aluguéis recebidos
Aluguéis em aberto
Aluguéis vencidos
Repasses pendentes
Repasses pagos
Cauções recebidas
```

## Gráficos

```text
Receitas x Despesas por mês
Recebimentos de aluguel por mês
Inadimplência por mês
Repasses pendentes x pagos
```

## Atalhos rápidos

```text
Nova receita
Nova despesa
Registrar pagamento de aluguel
Ver repasses pendentes
Ver inadimplência
Ver fluxo de caixa
```

---

# 24. Tela de lançamentos financeiros

A listagem deve usar tabela com paginação, busca, filtros e ordenação.

## Filtros

```text
Busca geral
Tipo: entrada ou saída
Categoria
Status
Forma de pagamento
Data de vencimento inicial
Data de vencimento final
Data de pagamento inicial
Data de pagamento final
Contrato
Imóvel
Cliente
Origem
```

## Colunas

```text
Código
Tipo
Categoria
Descrição
Cliente
Contrato
Imóvel
Valor
Vencimento
Pagamento
Forma
Status
Ações
```

## Ações

```text
Visualizar
Editar
Marcar como pago
Cancelar
Estornar
Excluir
```

## Regra de edição

```text
1. Lançamentos manuais podem ser editados enquanto estiverem pendentes.
2. Lançamentos originados automaticamente por contrato, parcela, caução ou repasse devem ter edição limitada.
3. Lançamentos pagos não devem permitir alteração livre de valor.
4. Alterações sensíveis devem gerar histórico.
```

---

# 25. Tela de cadastro de receita

Como possui poucos campos, pode ser feita em modal.

## Campos

```text
Descrição
Categoria
Cliente relacionado
Contrato relacionado
Imóvel relacionado
Valor
Data de vencimento
Data de recebimento
Forma de recebimento
Status
Observações
```

## Regras

```text
1. Valor deve ser maior que zero.
2. Receita pode ser salva como pendente ou paga.
3. Se status for pago, data de recebimento e forma de recebimento são obrigatórias.
4. Receita diversa não deve gerar repasse ao proprietário automaticamente.
```

---

# 26. Tela de cadastro de despesa

Como possui poucos campos, pode ser feita em modal.

## Campos

```text
Descrição
Categoria
Fornecedor ou cliente relacionado
Contrato relacionado
Imóvel relacionado
Valor
Data de vencimento
Data de pagamento
Forma de pagamento
Status
Observações
```

## Regras

```text
1. Valor deve ser maior que zero.
2. Despesa pode ser salva como pendente ou paga.
3. Se status for pago, data de pagamento e forma de pagamento são obrigatórias.
4. Despesas vinculadas a imóvel devem permitir identificar o imóvel relacionado.
5. Despesas vinculadas a contrato devem permitir identificar o contrato relacionado.
```

---

# 27. Tela de registro de pagamento de aluguel

Esta ação pode ser acessada por:

```text
Detalhes do contrato
Tab Parcelas
Gestão Financeira
Lista de inadimplência
Dashboard
```

## Campos

```text
Parcela
Contrato
Inquilino
Imóvel
Proprietário
Referência
Data de vencimento
Data de pagamento
Forma de pagamento
Valor do aluguel
Valor dos encargos
Valor da multa
Valor dos juros
Valor do desconto
Valor total
Valor pago
Observações
```

## Comportamento esperado

```text
1. Sistema carrega os dados da parcela.
2. Sistema calcula multa e juros com base na data de pagamento.
3. Usuário pode informar desconto manual.
4. Sistema recalcula o total.
5. Usuário confirma pagamento.
6. Sistema atualiza a parcela para paga ou pago_parcial.
7. Sistema cria entrada financeira.
8. Sistema gera repasse pendente ao proprietário.
```

---

# 28. Tela de repasses

A tela de repasses pode ser uma área própria dentro do financeiro.

## Filtros

```text
Busca geral
Proprietário
Contrato
Imóvel
Status
Referência
Data de geração
Data de pagamento
```

## Colunas

```text
Código
Proprietário
Imóvel
Contrato
Referência
Valor bruto
Taxa da imobiliária
Valor líquido
Status
Data de pagamento
Ações
```

## Ações

```text
Visualizar
Marcar como pago
Cancelar
Ver contrato
Ver parcela
```

---

# 29. Marcar repasse como pago

## Campos

```text
Data de pagamento
Forma de pagamento
Valor pago
Observações
```

## Regras

```text
1. Apenas repasses pendentes podem ser marcados como pagos.
2. Valor pago deve ser igual ao valor líquido, salvo ajuste autorizado.
3. Ao marcar como pago, o sistema deve criar ou atualizar uma saída financeira.
4. O repasse deve guardar data de pagamento e forma de pagamento.
5. A ação deve registrar usuário responsável.
```

---

# 30. Inadimplência

O financeiro deve permitir identificar parcelas vencidas e não pagas.

## Critério

```text
Parcela com data de vencimento menor que a data atual e status pendente ou pago_parcial.
```

## Indicadores

```text
Quantidade de parcelas vencidas
Valor total vencido
Quantidade de contratos inadimplentes
Quantidade de inquilinos inadimplentes
Maior atraso em dias
```

## Ações

```text
Registrar pagamento
Ver contrato
Enviar notificação por email
```

---

# 31. Fluxo de caixa

O fluxo de caixa deve exibir entradas, saídas e saldo por período.

## Visões sugeridas

```text
Diária
Mensal
Por período personalizado
```

## Campos exibidos

```text
Data
Entradas previstas
Entradas realizadas
Saídas previstas
Saídas realizadas
Saldo previsto
Saldo realizado
```

---

# 32. Relatórios do financeiro

## Relatórios MVP

```text
Receitas por período
Despesas por período
Fluxo de caixa
Aluguéis recebidos
Aluguéis em aberto
Aluguéis vencidos
Repasses pendentes
Repasses pagos
Cauções movimentadas
```

## Filtros comuns

```text
Período
Status
Categoria
Tipo
Cliente
Contrato
Imóvel
Forma de pagamento
```

---

# 33. Histórico financeiro

Toda ação relevante deve gerar histórico.

## Eventos sugeridos

```text
Lançamento criado
Lançamento editado
Lançamento pago
Lançamento cancelado
Lançamento estornado
Pagamento de aluguel registrado
Repasse gerado
Repasse pago
Caução recebida
Caução devolvida
Caução abatida
Caução retida
Despesa cadastrada
Receita cadastrada
```

## Campos do histórico

```text
id
uuid
entidade_tipo
entidade_id
acao
descricao
dados_anteriores
dados_novos
criado_por
created_at
```

---

# 34. Permissões do módulo

Permissões sugeridas:

```text
financeiro.visualizar
financeiro.criar
financeiro.editar
financeiro.excluir
financeiro.cancelar
financeiro.estornar
financeiro.marcar_como_pago
financeiro.registrar_pagamento_aluguel
financeiro.visualizar_repasses
financeiro.pagar_repasses
financeiro.visualizar_relatorios
financeiro.exportar_relatorios
```

---

# 35. Tabelas em português

Todas as tabelas devem usar nomes em português, em snake_case e sem acentos.

## Tabelas principais

```text
lancamentos_financeiros
categorias_financeiras
historicos_financeiros
```

## Tabelas já previstas no módulo de contratos e integradas ao financeiro

```text
parcelas_aluguel
repasses_proprietarios
caucoes_contrato
movimentacoes_caucao
```

---

# 36. Tabela categorias_financeiras

```text
id
uuid
nome
tipo
slug
descricao
ativa
created_at
updated_at
deleted_at
```

## Observações

```text
tipo: entrada ou saida
ativa: define se a categoria pode ser usada em novos lançamentos
```

---

# 37. Tabela lancamentos_financeiros

```text
id
uuid
codigo
tipo
categoria_financeira_id
contrato_locacao_id
parcela_aluguel_id
repasse_proprietario_id
caucao_contrato_id
movimentacao_caucao_id
imovel_id
cliente_id
descricao
valor
data_vencimento
data_pagamento
forma_pagamento
status
origem
observacoes
motivo_cancelamento
motivo_estorno
criado_por
pago_por
cancelado_por
estornado_por
created_at
updated_at
deleted_at
```

## Campo origem

O campo `origem` indica de onde o lançamento financeiro nasceu.

```text
manual
pagamento_aluguel
repasse_proprietario
caucao
movimentacao_caucao
despesa
receita_diversa
ajuste
```

---

# 38. Tabela historicos_financeiros

```text
id
uuid
lancamento_financeiro_id
entidade_tipo
entidade_id
acao
descricao
dados_anteriores
dados_novos
criado_por
created_at
```

---

# 39. Relacionamentos principais

## LancamentoFinanceiro

```text
Pertence a uma categoria financeira.
Pode pertencer a um contrato de locação.
Pode pertencer a uma parcela de aluguel.
Pode pertencer a um repasse ao proprietário.
Pode pertencer a uma caução.
Pode pertencer a uma movimentação de caução.
Pode pertencer a um imóvel.
Pode pertencer a um cliente.
Pertence a um usuário criador.
Pode pertencer a um usuário pagador.
Pode pertencer a um usuário cancelador.
Pode pertencer a um usuário estornador.
```

## CategoriaFinanceira

```text
Possui muitos lançamentos financeiros.
```

## HistoricoFinanceiro

```text
Pertence a um lançamento financeiro quando aplicável.
Registra ações sobre lançamentos, repasses, cauções e pagamentos.
```

---

# 40. Rotas sugeridas Laravel

```php
Route::resource('financeiro/lancamentos', LancamentoFinanceiroController::class);

Route::post('financeiro/lancamentos/{lancamento}/marcar-como-pago', [LancamentoFinanceiroController::class, 'marcarComoPago'])
    ->name('financeiro.lancamentos.marcar-como-pago');

Route::post('financeiro/lancamentos/{lancamento}/cancelar', [LancamentoFinanceiroController::class, 'cancelar'])
    ->name('financeiro.lancamentos.cancelar');

Route::post('financeiro/lancamentos/{lancamento}/estornar', [LancamentoFinanceiroController::class, 'estornar'])
    ->name('financeiro.lancamentos.estornar');

Route::get('financeiro/dashboard', [FinanceiroDashboardController::class, 'index'])
    ->name('financeiro.dashboard');

Route::get('financeiro/fluxo-caixa', [FluxoCaixaController::class, 'index'])
    ->name('financeiro.fluxo-caixa');

Route::get('financeiro/inadimplencia', [InadimplenciaController::class, 'index'])
    ->name('financeiro.inadimplencia');

Route::get('financeiro/repasses', [RepasseProprietarioController::class, 'index'])
    ->name('financeiro.repasses.index');

Route::post('financeiro/repasses/{repasse}/marcar-como-pago', [RepasseProprietarioController::class, 'marcarComoPago'])
    ->name('financeiro.repasses.marcar-como-pago');

Route::resource('financeiro/categorias', CategoriaFinanceiraController::class);
```

---

# 41. Páginas Vue/Inertia sugeridas

```text
resources/js/Pages/Financeiro/Dashboard.vue
resources/js/Pages/Financeiro/Lancamentos/Index.vue
resources/js/Pages/Financeiro/Lancamentos/Show.vue
resources/js/Pages/Financeiro/FluxoCaixa.vue
resources/js/Pages/Financeiro/Inadimplencia.vue
resources/js/Pages/Financeiro/Repasses/Index.vue
resources/js/Pages/Financeiro/Categorias/Index.vue
```

---

# 42. Componentes Vue sugeridos

```text
resources/js/Components/Financeiro/FinanceiroResumoCards.vue
resources/js/Components/Financeiro/GraficoReceitasDespesas.vue
resources/js/Components/Financeiro/GraficoRecebimentos.vue
resources/js/Components/Financeiro/GraficoInadimplencia.vue
resources/js/Components/Financeiro/TabelaLancamentos.vue
resources/js/Components/Financeiro/TabelaRepasses.vue
resources/js/Components/Financeiro/TabelaInadimplencia.vue
resources/js/Components/Financeiro/ModalReceita.vue
resources/js/Components/Financeiro/ModalDespesa.vue
resources/js/Components/Financeiro/ModalMarcarComoPago.vue
resources/js/Components/Financeiro/ModalCancelarLancamento.vue
resources/js/Components/Financeiro/ModalEstornarLancamento.vue
resources/js/Components/Financeiro/ModalPagarRepasse.vue
resources/js/Components/Financeiro/FiltrosFinanceiro.vue
resources/js/Components/Financeiro/StatusLancamentoBadge.vue
```

---

# 43. Services sugeridos

```text
LancamentoFinanceiroService
ReceitaFinanceiraService
DespesaFinanceiraService
PagamentoAluguelFinanceiroService
RepasseFinanceiroService
CaucaoFinanceiroService
FluxoCaixaService
InadimplenciaService
IndicadoresFinanceirosService
HistoricoFinanceiroService
```

---

# 44. Form Requests sugeridos

```text
StoreLancamentoFinanceiroRequest
UpdateLancamentoFinanceiroRequest
MarcarLancamentoComoPagoRequest
CancelarLancamentoFinanceiroRequest
EstornarLancamentoFinanceiroRequest
StoreCategoriaFinanceiraRequest
UpdateCategoriaFinanceiraRequest
PagarRepasseProprietarioRequest
```

---

# 45. Policies sugeridas

```text
LancamentoFinanceiroPolicy
CategoriaFinanceiraPolicy
RepasseProprietarioPolicy
RelatorioFinanceiroPolicy
```

---

# 46. Regras de negócio consolidadas

```text
1. Todo lançamento financeiro deve ser entrada ou saída.
2. Todo lançamento deve possuir categoria.
3. Lançamentos podem ser manuais ou automáticos.
4. Pagamento de aluguel gera entrada financeira.
5. Pagamento de aluguel calcula taxa da imobiliária.
6. Pagamento de aluguel gera repasse pendente ao proprietário.
7. Repasse pago deve gerar ou atualizar saída financeira.
8. Caução recebida gera entrada financeira, mas não é receita operacional.
9. Devolução de caução gera saída financeira.
10. Retenção e abatimento de caução devem gerar histórico.
11. Encargos não são receita automática da imobiliária.
12. Multas e juros não entram automaticamente no repasse do proprietário.
13. Lançamentos pagos não devem ser alterados livremente.
14. Cancelamentos devem exigir motivo.
15. Estornos devem preservar o lançamento original.
16. Exclusões devem usar Soft Delete.
17. Indicadores financeiros devem considerar status dos lançamentos.
18. Inadimplência deve considerar parcelas vencidas e não pagas.
19. Repasses devem preservar vínculo com contrato, parcela, imóvel e proprietário.
20. Relatórios devem permitir filtro por período.
```

---

# 47. Indicadores para dashboard financeiro

## Receitas do mês

Soma dos lançamentos de entrada pagos no mês.

## Despesas do mês

Soma dos lançamentos de saída pagos no mês.

## Saldo do mês

```text
Saldo = receitas pagas - despesas pagas
```

## Aluguéis recebidos

Soma das parcelas de aluguel pagas no mês.

## Aluguéis em aberto

Soma das parcelas pendentes com vencimento futuro ou no dia atual.

## Aluguéis vencidos

Soma das parcelas pendentes ou parcialmente pagas com vencimento anterior à data atual.

## Repasses pendentes

Soma dos repasses com status pendente.

## Repasses pagos

Soma dos repasses pagos no período.

---

# 48. Ordem recomendada de implementação

```text
1. Criar migration de categorias_financeiras.
2. Criar migration de lancamentos_financeiros.
3. Criar migration de historicos_financeiros.
4. Criar models e relacionamentos.
5. Criar enums/status de lançamentos, categorias, origem e formas de pagamento.
6. Criar seeds de categorias financeiras padrão.
7. Criar tela de dashboard financeiro.
8. Criar listagem de lançamentos financeiros.
9. Criar modal de receita manual.
10. Criar modal de despesa manual.
11. Criar ação de marcar lançamento como pago.
12. Criar ação de cancelar lançamento.
13. Criar ação de estornar lançamento.
14. Integrar pagamento de aluguel com lançamento financeiro.
15. Integrar pagamento de aluguel com geração de repasse.
16. Criar tela de repasses.
17. Criar ação de marcar repasse como pago.
18. Integrar caução com financeiro.
19. Criar tela de inadimplência.
20. Criar tela de fluxo de caixa.
21. Criar relatórios básicos.
22. Criar permissões do módulo.
```

---

# 49. Seeds iniciais de categorias

## Entradas

```text
Aluguel
Receita diversa
Taxa de administração
Multa por atraso
Juros por atraso
Caução
Ajuste positivo
```

## Saídas

```text
Repasse ao proprietário
Despesa operacional
Despesa administrativa
Fornecedor
Devolução de caução
Manutenção de imóvel
Comissão de corretor
Ajuste negativo
```

---

# 50. Observações para o MVP

Para manter o MVP simples, a primeira versão deve priorizar:

```text
Lançamentos financeiros simples.
Categorias financeiras básicas.
Receitas e despesas manuais.
Pagamento de aluguel integrado ao contrato.
Geração de repasse ao proprietário.
Pagamento manual de repasse.
Controle básico de caução.
Indicadores financeiros simples.
Fluxo de caixa por período.
Relatórios básicos.
```

Deixar para depois:

```text
Automação bancária.
PIX automático.
Boleto.
Conciliação.
Plano de contas avançado.
DRE.
Integração contábil.
```

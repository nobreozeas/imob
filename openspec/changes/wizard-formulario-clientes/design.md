## Context

O formulário atual (`FormularioCliente.vue`) exibe todos os campos em uma única página longa dividida por cards. A experiência funciona, mas é cognitivamente densa para novos usuários da imobiliária. A mudança converte essa experiência em um step wizard mantendo exatamente o mesmo backend (controller, requests, service, models).

O projeto usa Vue 3 + Inertia + TypeScript + DaisyUI + Tailwind CSS 4 sem bibliotecas de wizard externas.

## Goals / Non-Goals

**Goals:**
- Dividir o formulário em 5 etapas sequenciais com barra de progresso.
- Validar campos obrigatórios da etapa atual antes de permitir avançar.
- Permitir navegação para etapas anteriores sem perda de dados.
- Exibir resumo na última etapa antes do envio.
- Funcionar tanto no cadastro (`Create.vue`) quanto na edição (`Edit.vue`).
- Manter compatibilidade total com o `useForm` do Inertia (sem mudança de backend).

**Non-Goals:**
- Salvar rascunho por etapa no servidor (salvamento ocorre apenas no submit final).
- Animações complexas de transição entre etapas.
- Alterar qualquer lógica de backend.

## Decisões

### 1. Wizard puramente no frontend, submit único no final

**Decisão**: O wizard controla apenas a navegação entre etapas. O `useForm` do Inertia continua acumulando todos os campos e submete tudo em uma única requisição ao clicar em "Salvar" na última etapa.

**Alternativas consideradas**:
- Submit por etapa (PATCH parcial): rejeitado — aumenta complexidade do backend e exigiria lógica de estado parcial no servidor.
- Biblioteca externa de wizard (vue-step-wizard, FormKit): rejeitado — adiciona dependência desnecessária; o padrão é simples o suficiente para implementar nativamente.

**Rationale**: Mantém o contrato de API intacto e simplifica o rollback caso o usuário abandone o wizard.

### 2. Cinco etapas fixas

| Etapa | Componente | Campos |
|---|---|---|
| 1 | `WizardStep1DadosPrincipais.vue` | tipo_pessoa, nome/razao_social, nome_fantasia, CPF/CNPJ, RG, data_nascimento, observações |
| 2 | `WizardStep2Papeis.vue` | papeis[], validação: ao menos 1 papel |
| 3 | `WizardStep3Contatos.vue` | telefone_principal, whatsapp, telefone_secundario, email_principal, email_alternativo |
| 4 | `WizardStep4Endereco.vue` | cep, logradouro, numero, complemento, bairro, cidade, estado, ponto_referencia |
| 5 | `WizardStep5DadosAdicionais.vue` | dados de proprietário (condicional), dados de inquilino (condicional), resumo final |

**Rationale**: A etapa 2 (papéis) é separada intencionalmente — os papéis determinam quais seções da etapa 5 aparecem, e o usuário precisa tê-los escolhido antes de chegar aos dados adicionais.

### 3. Validação frontend por etapa (sem round-trip)

**Decisão**: Validação de campos obrigatórios da etapa é feita no próprio componente de etapa antes de emitir `next`. Os erros do servidor (422) continuam sendo exibidos via `form.errors` do Inertia após o submit final.

**Rationale**: Feedback imediato sem latência de rede; erros de unicidade (CPF duplicado) são capturados apenas no submit, que é aceitável.

### 4. Componente orquestrador `WizardCliente.vue`

**Decisão**: `WizardCliente.vue` recebe `modelValue` (o `useForm`), gerencia `etapaAtual` (1–5), renderiza a etapa correta via `v-if`, exibe a barra de progresso DaisyUI (`steps`) e os botões Anterior/Próximo/Salvar.

**Rationale**: Separa a lógica de navegação dos dados do formulário. Os componentes de etapa são "burros" — apenas renderizam e emitem eventos.

### 5. Remoção de `FormularioCliente.vue`

**Decisão**: O componente existente é removido após o wizard estar funcional.

**Rationale**: Evita dois caminhos de edição paralelos. O wizard cobre 100% dos campos do formulário anterior.

## Estrutura de Arquivos

```
resources/js/
  Components/
    Clientes/
      WizardCliente.vue               ← novo (orquestrador)
      WizardStep1DadosPrincipais.vue  ← novo
      WizardStep2Papeis.vue           ← novo
      WizardStep3Contatos.vue         ← novo
      WizardStep4Endereco.vue         ← novo
      WizardStep5DadosAdicionais.vue  ← novo (inclui resumo)
      FormularioCliente.vue           ← REMOVIDO
  Pages/
    Admin/
      Clientes/
        Create.vue   ← atualizado para usar WizardCliente
        Edit.vue     ← atualizado para usar WizardCliente
```

## Risks / Trade-offs

- **Risco**: Erros do servidor (422) retornam após o submit e o usuário está na etapa 5 — pode não ver onde está o erro se for de uma etapa anterior (ex: CPF duplicado na etapa 1). → **Mitigação**: Ao receber `form.errors`, o wizard navega automaticamente para a etapa com o primeiro erro.
- **Trade-off**: Usuários que já conhecem o formulário precisam clicar "Próximo" mais vezes. → Aceitável dado que o público-alvo (pequenas imobiliárias) tem alto volume de novos usuários e onboarding frequente.

## Migration Plan

1. Criar os 6 novos componentes Vue.
2. Atualizar `Create.vue` e `Edit.vue` para usar `WizardCliente`.
3. Remover `FormularioCliente.vue`.
4. Build e teste manual nos dois fluxos (cadastro e edição).
5. Sem migração de dados ou alteração de banco.

## Open Questions

- Os labels das etapas no `steps` do DaisyUI devem usar ícones ou apenas texto? (Recomendado: texto curto — mais legível no mobile)

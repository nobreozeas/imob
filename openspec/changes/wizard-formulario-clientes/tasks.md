## 1. Componentes de Etapa

- [x] 1.1 Criar `WizardStep1DadosPrincipais.vue` — campos: tipo_pessoa (radio), nome (PF), razao_social + nome_fantasia (PJ), CPF (PF) / CNPJ (PJ), RG, data_nascimento (PF), observações; emite `next` com validação de campos obrigatórios da etapa
- [x] 1.2 Criar `WizardStep2Papeis.vue` — checkboxes de proprietário e inquilino com visual destacado; emite `next` somente se ao menos um papel estiver selecionado; exibe erro inline se nenhum papel selecionado
- [x] 1.3 Criar `WizardStep3Contatos.vue` — campos: telefone_principal, whatsapp, telefone_secundario, email_principal, email_alternativo; sem validação obrigatória; emite `next` diretamente
- [x] 1.4 Criar `WizardStep4Endereco.vue` — campos: cep, logradouro, numero, complemento, bairro, cidade, estado (select UF), ponto_referencia; sem validação obrigatória; emite `next` diretamente
- [x] 1.5 Criar `WizardStep5DadosAdicionais.vue` — exibe seção de dados de proprietário (condicional: se `papeis` inclui proprietário) e dados de inquilino (condicional: se `papeis` inclui inquilino); exibe resumo de todos os dados preenchidos nas etapas anteriores acima das seções condicionais; contém o botão "Salvar"

## 2. Componente Orquestrador

- [x] 2.1 Criar `WizardCliente.vue` — recebe `modelValue` (objeto reativo com todos os campos do formulário) e `errors` (erros do servidor); gerencia `etapaAtual` (ref iniciando em 1); renderiza a etapa correta via `v-if`/`v-show`; exibe barra de progresso DaisyUI (`ul.steps`) com os labels: Dados, Papéis, Contatos, Endereço, Finalizar
- [x] 2.2 Implementar lógica de navegação em `WizardCliente.vue`: método `avancar()` (chamado pelo evento `next` da etapa), método `voltar()` (decrementa etapaAtual), exibir botão "Anterior" apenas quando etapaAtual > 1
- [x] 2.3 Implementar redirecionamento automático para etapa com erro: ao detectar mudança em `errors` (watch), mapear cada campo com erro para a sua etapa correspondente (mapa: etapa1 = [nome, razao_social, cpf, cnpj, rg, data_nascimento, tipo_pessoa]; etapa2 = [papeis]; etapa3 = [telefone_principal, email_principal, email_alternativo]; etapa4 = [cep, logradouro, cidade, estado]; etapa5 = [proprietario.*, inquilino.*]) e navegar para a etapa do primeiro campo com erro

## 3. Integração com Páginas

- [x] 3.1 Atualizar `Create.vue` — substituir `<FormularioCliente>` por `<WizardCliente>` mantendo o mesmo `useForm` e o evento de submit
- [x] 3.2 Atualizar `Edit.vue` — substituir `<FormularioCliente>` por `<WizardCliente>` mantendo o mesmo `useForm` pré-preenchido e o evento de submit

## 4. Limpeza

- [x] 4.1 Remover o arquivo `FormularioCliente.vue` de `resources/js/Components/Clientes/`

## 5. Validação

- [x] 5.1 Buildar o frontend (`npm run build`) e verificar ausência de erros de TypeScript/Vue
- [x] 5.2 Testar manualmente o fluxo completo de cadastro: percorrer todas as 5 etapas, verificar validação na Etapa 1 (sem nome) e Etapa 2 (sem papel), e confirmar que o cliente é salvo corretamente
- [x] 5.3 Testar manualmente o fluxo de edição: verificar que os campos estão pré-preenchidos na Etapa 1, navegar até a Etapa 5 e salvar
- [x] 5.4 Testar redirecionamento para etapa com erro: submeter sem CPF para verificar que o wizard vai para a Etapa 1 após resposta 422 do servidor

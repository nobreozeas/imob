## Context

Stack já estabelecida: Laravel 13, Inertia 3, Vue 3, TypeScript, Tailwind CSS 4, DaisyUI 5, PostgreSQL, Docker. Os módulos de Clientes e Imóveis foram implementados e servem como referência direta de padrões: UUID em PKs (`HasUuids`), SoftDeletes, Spatie Permissions com `App\Models\Permission` e `App\Models\Role`, Services em `App\Services\`, Policy registrada em `AppServiceProvider`, wizard multi-step (direto mutation via `v-model`), Inertia `useForm` como proxy reativo, método `$table` explícito nos models.

O contrato de locação é a entidade central que conecta `imoveis` (pelo `imovel_id`), `clientes` (como proprietário via `proprietario_id` e inquilino via `inquilino_id`) e usuários (como corretor e criador). Ele possui 6 sub-entidades: encargos, caução, multas, documentos, histórico e — futuramente — lançamentos financeiros.

## Goals / Non-Goals

**Goals:**
- CRUD completo de contratos com wizard de 9 etapas
- Fluxo de status controlado: rascunho → aguardando assinatura → ativo → (encerrado | rescindido | cancelado)
- Integração real com módulo de imóveis: status do imóvel muda automaticamente via contrato
- Registro de histórico em toda ação relevante
- Upload de documentos vinculados ao contrato
- Caução com controle de recebimento e devolução
- Regras de multa (atraso e rescisão) configuráveis por contrato
- Encargos e responsabilidades configuráveis
- Repasse ao proprietário com taxa de administração
- Permissões granulares

**Non-Goals:**
- Geração automática de PDF do contrato (estrutura preparada, não implementada)
- Assinatura digital
- Geração automática de lançamentos financeiros (estrutura preparada via `contrato_lancamentos_previstos`, não implementada)
- Envio de notificações por e-mail ou WhatsApp
- Cobrança via PIX ou boleto
- Renovação automática de contrato
- Aditivos contratuais

## Decisions

### 1. Número do contrato: gerado automaticamente no formato `LOC-{YYYYMM}-{seq}`

Seguindo o mesmo padrão do código do imóvel (`IMO-{YYYYMM}-{seq}`). O campo é exibido como readonly no wizard; o service gera se não fornecido. Permite override manual apenas em contratos rascunho.

Alternativa descartada: UUID como número público — ilegível para a equipe da imobiliária.

### 2. Sub-entidades como tabelas separadas com relações 1:1 ou 1:N

| Tabela | Tipo | Descrição |
|---|---|---|
| `contrato_encargos` | 1:N | Um encargo por tipo (IPTU, água, etc.) |
| `contrato_caucoes` | 1:1 | Única caução por contrato |
| `contrato_multas` | 1:1 | Única configuração de multas por contrato |
| `contrato_documentos` | 1:N | Múltiplos arquivos |
| `contrato_historicos` | 1:N | Log imutável de eventos |

A caução e as multas são 1:1 (sempre criadas junto com o contrato, mesmo que `possui_caucao = false` / `possui_multa_atraso = false`) para simplificar `updateOrCreate` no service.

Alternativa descartada: JSONB para encargos e multas — prejudica queries, índices e type safety.

### 3. Wizard de 9 etapas: submissão única no step 9 com `forceFormData`

Mesmo padrão dos wizards de clientes e imóveis. Arquivos de documentos são incluídos no form do step 8 com `forceFormData: true`. Step 9 exibe resumo e tem dois botões: "Salvar como Rascunho" e "Ativar Contrato". O submit envia um campo `acao: 'rascunho' | 'ativar'` junto com os dados.

Alternativa descartada: dois formulários separados — duplica validação e lógica de estado.

### 4. Proprietário preenchido automaticamente ao selecionar imóvel

No Step 1 do wizard, ao selecionar o imóvel (via `select` ou busca), o frontend faz uma leitura nos dados do imóvel já carregados via props e preenche automaticamente o campo `proprietario_id` (readonly). O controller carrega `imoveis disponíveis com proprietário` nos props.

Alternativa descartada: API endpoint separado para buscar proprietário — adiciona round-trip desnecessário quando os dados já estão nos props.

### 5. Fluxo de status do contrato

```
rascunho → aguardando_assinatura → ativo → encerrado
                                         ↘ rescindido
rascunho → cancelado
aguardando_assinatura → cancelado
```

Transições implementadas via `ContratoStatusService` em `DB::transaction` junto com atualização do imóvel e registro de histórico. O status `vencido` será gerenciado futuramente por um job agendado (fora do escopo).

### 6. Integração com módulo de imóveis: `temContratoAtivo()` real

O método `Imovel::temContratoAtivo()` atualmente retorna `false`. Nesta implementação passa a consultar `contratos_locacao` onde `imovel_id = $this->id AND status = 'ativo'`. A action `alterarStatus` do `ImovelController` já usa esse método para bloquear a transição manual para `disponivel`.

### 7. Histórico: log imutável, sem softDelete

`contrato_historicos` não tem `softDeletes` e não tem `update`. Cada evento gera um novo registro. O Service escreve no histórico a cada: criação, ativação, cancelamento, encerramento, rescisão, e alteração de campos sensíveis (imóvel, inquilino, valor, data início/fim, caução, multa, taxa de administração).

### 8. Encerramento e Rescisão: ações via modal na tela de detalhes

Em vez de páginas separadas, encerramento e rescisão são processados via `POST /contratos/{contrato}/encerrar` e `POST /contratos/{contrato}/rescindir` disparados por modais na tela Show, mantendo a UX simples. Os modais são componentes Vue locais na página Show.

### 9. Documentos do contrato: storage `public` disk, mesmo padrão de imóveis

`storage/app/public/contratos/{uuid}/` servido via symlink já criado. Formatos aceitos: PDF, JPEG, PNG, DOCX. Tamanho máximo: 20 MB. Upload no step 8 do wizard e também via ação dedicada na tela de detalhes.

### 10. Repasse: calculado mas não persistido automaticamente

O valor do repasse (`valor_repasse`) é calculado no service como `valor_aluguel - taxa_administracao` e exibido na UI como campo informativo. A persistência do repasse real pertence ao módulo financeiro (fora do escopo). O contrato armazena as regras (percentual ou valor fixo da taxa de administração, dia previsto, forma de repasse, dados bancários do proprietário copiados do `dados_proprietario` do cliente).

## Risks / Trade-offs

- **Wizard de 9 steps é longo**: Usuário pode se perder. → Mitigation: barra de progresso DaisyUI + permitir clicar nos steps já visitados + salvar como rascunho a qualquer momento via botão no header.
- **Proprietário como FK de cliente**: O proprietário do contrato vem do imóvel, mas o campo `dados_bancarios` pode mudar no cadastro do cliente depois do contrato criado. → Mitigation: copiar os dados bancários do proprietário no momento da criação do contrato (campos de repasse armazenam a cópia).
- **Concorrência na ativação**: Dois usuários podem tentar ativar contratos para o mesmo imóvel simultaneamente. → Mitigation: `DB::transaction` com `lockForUpdate()` na leitura do imóvel durante ativação.
- **`temContratoAtivo()` agora faz query**: Performance em listagem de imóveis que usa esse método. → Mitigation: o método é chamado apenas na action `alterarStatus`, não na listagem.
- **Formulário com arquivos (step 8)**: `form.put()` com `forceFormData` pode ter comportamento inesperado em alguns navegadores com muitos arquivos. → Mitigation: limitar a 10 documentos por contrato no validator.

## Open Questions

- Quando o módulo financeiro for implementado, os lançamentos previstos devem ser gerados na ativação do contrato. A tabela `contratos_locacao` tem campos suficientes (`valor_aluguel`, `dia_vencimento`, `data_inicio`, `data_fim`) para isso. Nenhuma ação adicional necessária agora.
- O status `vencido` (contrato além da data_fim ainda ativo) será gerenciado por um Artisan command/job agendado. Por ora, não é exibido na listagem diferentemente.

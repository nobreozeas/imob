## Context

O sistema de gestão imobiliária é uma aplicação web construída com Laravel 13 + Inertia.js + Vue 3 + TypeScript. Não existe ainda nenhum módulo de autenticação implementado. O Laravel fornece scaffolding nativo de autenticação (Fortify / Breeze), mas o produto exige fluxos customizados: senha temporária gerada automaticamente, troca obrigatória no primeiro acesso e controle de estado de usuário (`ativo`, `inativo`, `pendente_primeiro_acesso`).

A autenticação é o ponto de entrada de todo o sistema; nenhuma outra funcionalidade pode ser usada sem ela.

## Goals / Non-Goals

**Goals:**
- Login seguro com email e senha usando a autenticação nativa do Laravel (sessão via cookie)
- Geração automática de senha temporária e envio por email ao criar usuário
- Troca obrigatória de senha no primeiro acesso antes de acessar qualquer módulo interno
- Recuperação de senha por link temporário enviado por email
- Alteração de senha pelo usuário autenticado
- Proteção de todas as rotas internas com middleware `auth`
- Bloqueio de usuários inativos, excluídos ou em estado `pendente_primeiro_acesso` (exceto na tela de troca de senha)
- Registro de `ultimo_acesso_em`, `primeiro_acesso_em`, `convite_enviado_em`
- Layout visual fiel ao design fornecido (card centralizado, split-screen com imagem à esquerda)

**Non-Goals:**
- Autenticação em dois fatores (2FA)
- Login social (Google, Microsoft)
- Bloqueio automático por tentativas inválidas (para versão futura)
- Portal do proprietário ou do inquilino
- API REST de autenticação (o sistema usa Inertia, não SPA pura)

## Decisions

### 1. Usar autenticação nativa do Laravel (sessão + cookie) em vez de Sanctum/JWT

**Decisão**: Usar `Auth::attempt()` + sessão PHP nativa via Inertia.js (sem API stateless).

**Rationale**: O sistema é server-rendered via Inertia.js, não uma SPA com API separada. A autenticação por sessão é mais simples, mais segura por padrão (cookie HttpOnly, CSRF protection nativo do Laravel) e elimina a necessidade de gerenciar tokens no frontend.

**Alternativa considerada**: Laravel Sanctum com SPA cookies — descartado por adicionar complexidade desnecessária para este caso de uso.

### 2. Campo `deve_alterar_senha` em vez de status separado

**Decisão**: Adicionar o campo booleano `deve_alterar_senha` na tabela `users` (além do campo `status`).

**Rationale**: Separa claramente dois conceitos diferentes: o `status` do usuário (ativo/inativo/excluído) e a obrigação de trocar senha. Um usuário pode estar `ativo` mas ainda precisar trocar a senha. Isso permite que o admin force a troca sem inativar o usuário.

**Alternativa considerada**: Usar apenas o `status` com valor `pendente_primeiro_acesso` — descartado porque não permite forçar troca de senha para usuários que já estão ativos.

### 3. Middleware customizado `MustChangePassword` separado do middleware `auth`

**Decisão**: Criar um middleware `MustChangePassword` que redireciona para a tela de troca obrigatória se `deve_alterar_senha = true`.

**Rationale**: Mantém a responsabilidade separada: o middleware `auth` verifica se está autenticado, o `MustChangePassword` verifica se precisa trocar a senha. As rotas internas usam ambos em sequência. A rota de troca obrigatória usa apenas `auth`.

### 4. Senha temporária gerada pelo sistema (não pelo admin)

**Decisão**: O sistema gera uma senha aleatória de 12 caracteres (letras + números) automaticamente ao criar o usuário. O admin nunca vê nem define a senha.

**Rationale**: Elimina senhas padrão compartilhadas (ex: "123456"), garante que cada usuário receba credenciais únicas e força o fluxo seguro de primeiro acesso.

### 5. Usar `password_reset_tokens` nativo do Laravel para recuperação de senha

**Decisão**: Reutilizar a tabela e o broker de reset nativo do Laravel.

**Rationale**: O Laravel já implementa geração de token seguro, expiração configurável e invalidação após uso. Evita reinventar essa infraestrutura.

### 6. Layout de autenticação separado do layout interno

**Decisão**: Criar um layout Vue dedicado para as páginas de autenticação (sem sidebar, sem navbar interna), fiel ao design fornecido (split-screen com imagem/marketing à esquerda e formulário à direita).

**Rationale**: As páginas de autenticação têm UX completamente diferente das páginas internas. Um layout separado evita lógica condicional no layout principal.

## Risks / Trade-offs

- **Sessões em produção com múltiplos servidores** → Mitigation: Configurar `SESSION_DRIVER=database` ou Redis para sessões compartilhadas entre instâncias.
- **Email não chega ao usuário no primeiro acesso** → Mitigation: O admin pode reenviar o convite via ação na listagem de usuários; o sistema registra `convite_enviado_em`.
- **Link de recuperação de senha expirado** → Mitigation: O usuário pode solicitar um novo link; o tempo de expiração é configurável via `PASSWORD_RESET_EXPIRE` (padrão: 60 minutos).
- **Usuário burla o middleware de troca de senha** → Mitigation: O middleware `MustChangePassword` é aplicado a **todas** as rotas internas, não apenas ao dashboard. Não há rota interna acessível sem passar por ele.

## Migration Plan

1. Executar migration para adicionar campos customizados na tabela `users` (`deve_alterar_senha`, `ultimo_acesso_em`, `primeiro_acesso_em`, `convite_enviado_em`, `criado_por`, `status`)
2. Criar seeder do usuário administrador inicial (para bootstrap do sistema)
3. Configurar variáveis de ambiente de email (SMTP/Mailtrap para desenvolvimento)
4. Registrar middlewares no `bootstrap/app.php`
5. Configurar rotas de autenticação separadas das rotas protegidas
6. Deploy sem breaking changes (novo sistema, banco vazio)

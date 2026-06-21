## ADDED Requirements

### Requirement: Rotas internas são protegidas contra acesso não autenticado
O sistema SHALL bloquear o acesso a qualquer rota interna por usuários não autenticados e redirecioná-los para a tela de login.

#### Scenario: Usuário não autenticado tenta acessar rota interna
- **WHEN** um usuário sem sessão ativa tenta acessar qualquer rota interna (ex: `/dashboard`, `/imoveis`, `/contratos`)
- **THEN** o sistema redireciona para `/login`

#### Scenario: Redirecionamento após login para rota pretendida
- **WHEN** o usuário não autenticado é redirecionado para `/login` ao tentar acessar `/contratos-locacao` e então realiza o login com sucesso
- **THEN** o sistema redireciona de volta para `/contratos-locacao`

#### Scenario: Sessão expirada durante uso
- **WHEN** a sessão do usuário expira enquanto ele está usando o sistema e ele tenta realizar uma ação
- **THEN** o sistema redireciona para `/login` com mensagem "Sua sessão expirou. Faça login novamente."

### Requirement: Usuários inativos são bloqueados mesmo com sessão válida
O sistema SHALL verificar o status do usuário a cada requisição e bloquear usuários que foram inativados ou excluídos durante a sessão ativa.

#### Scenario: Usuário é inativado enquanto está logado
- **WHEN** um administrador inativa um usuário que possui sessão ativa e o usuário tenta acessar uma rota interna
- **THEN** o sistema encerra a sessão e redireciona para `/login` com mensagem "Seu acesso está inativo. Entre em contato com o administrador."

### Requirement: Usuários com deve_alterar_senha são bloqueados nas rotas internas
O sistema SHALL verificar `deve_alterar_senha` a cada requisição às rotas internas e redirecionar para a tela de troca obrigatória quando necessário.

#### Scenario: Usuário com deve_alterar_senha tenta acessar módulo interno
- **WHEN** um usuário autenticado com `deve_alterar_senha = true` tenta acessar qualquer rota interna exceto `/primeiro-acesso`
- **THEN** o sistema redireciona para `/primeiro-acesso`

### Requirement: Rotas públicas de autenticação são acessíveis sem autenticação
O sistema SHALL manter as rotas de autenticação (`/login`, `/esqueci-senha`, `/redefinir-senha/{token}`) acessíveis sem autenticação.

#### Scenario: Acesso às rotas públicas sem autenticação
- **WHEN** um usuário não autenticado acessa `/login`, `/esqueci-senha` ou `/redefinir-senha/{token}`
- **THEN** o sistema SHALL exibir a página correspondente sem exigir autenticação

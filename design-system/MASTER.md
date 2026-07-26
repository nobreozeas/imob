# ImobGestor — Design System (Master)

SaaS de gestão imobiliária. Identidade: **moderna, corporativa, limpa** — transmitir confiança, organização e produtividade. Base: tema **`corporate`** do DaisyUI 5 (já configurado como default em `resources/css/app.css`, não trocar).

> Ordem de leitura para uma página específica: primeiro `design-system/pages/<pagina>.md` (se existir), depois este arquivo.

## Cores (tokens semânticos do tema `corporate`)

Nunca usar hex cru no código de UI (componentes Vue). Sempre classes/tokens semânticos do DaisyUI (`bg-primary`, `text-error`, `border-base-300`, etc.) ou `var(--color-*)` quando precisar em CSS/JS (ex.: SweetAlert2).

| Token | Uso | Valor (oklch, tema `corporate`) |
|---|---|---|
| `--color-primary` | Ação principal, links, foco | `oklch(58% 0.158 241.966)` — azul corporativo |
| `--color-secondary` | Apoio, texto secundário em destaque | `oklch(55% 0.046 257.417)` — azul-acinzentado |
| `--color-accent` | Destaques pontuais | `oklch(60% 0.118 184.704)` — teal |
| `--color-neutral` | Elementos neutros de alto contraste | preto |
| `--color-base-100/200/300` | Fundo de card / página / bordas | branco → cinza claro → cinza médio |
| `--color-success` | Confirmações, status ativo/pago | verde |
| `--color-warning` | Alertas, pendências | amarelo |
| `--color-error` | Destrutivo, cancelamento, inadimplência | vermelho |
| `--color-info` | Informativo neutro | azul claro |

Fonte da verdade: `node_modules/daisyui/theme/corporate.css`. Não redefinir essas variáveis por página.

## Forma e elevação

- `--radius-selector/field/box: 0.25rem` — cantos discretos (não usar `rounded-full`/`rounded-2xl` fora de avatares/badges quando fizer sentido). Preferir `rounded-box` (cards), `rounded-field` (inputs/botões) implícitos nas classes `card`, `input`, `btn`, `select`.
- `--depth: 0; --noise: 0` → visual **flat**. Elevação só via `shadow-sm` + `border border-base-200`, nunca sombras pesadas (`shadow-xl`, `shadow-2xl`).
- Padrão de card já em uso (manter): `class="card bg-base-100 shadow-sm border border-base-200"`.

## Tipografia

- Fonte: **Instrument Sans**, carregada via diretiva `@fonts` do Laravel em `app.blade.php` (não adicionar outro import de fonte).
- Escala em uso: `text-xs` (labels/metadados), `text-sm` (corpo secundário), `text-base` (corpo), `text-xl`/`text-2xl` (títulos de página/cards de indicador).
- Peso: `font-bold` para `h1`/valores de destaque, `font-medium` para labels de formulário, `font-normal` para corpo.
- Título de página: `<h1 class="text-2xl font-bold text-base-content">`.
- Texto secundário/apagado: `text-base-content/50` a `/70` (nunca cinza hex fixo).

## Espaçamento

- Ritmo de 4px/8px (`gap-1`, `gap-2`, `gap-3`, `gap-4`, `p-4`, `p-6`).
- Conteúdo principal do layout: `main` com `p-6`.
- Grades de indicadores: `grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3`.
- Corpo de card compacto: `card-body py-3 px-4` (indicadores) ou `py-4` (filtros/formulários).

## Ícones

- Biblioteca única: **`lucide-vue-next`** (já é dependência do projeto). Nunca emoji como ícone. Nunca SVG inline colado no template — se um ícone não existir no set atual, importar o componente Lucide equivalente.
- Tamanho padrão: `class="w-4 h-4"` inline em botões/labels, `w-5 h-5` para ícones "standalone" (estado vazio, cabeçalhos), `w-8 h-8`/`w-12 h-12` para estados vazios grandes.
- Stroke consistente (padrão do Lucide, não sobrescrever `stroke-width` por ícone).
- Botões só-ícone precisam de `aria-label`.

## Componentes — convenções já estabelecidas (manter)

- **Botões**: `btn btn-primary` (ação principal), `btn btn-ghost btn-xs` (ações de linha em tabela), `btn btn-ghost btn-xs text-error` (destrutivo em linha), `btn btn-primary w-full` (submit de formulário auth). Não usar cores custom fora da paleta semântica (`btn-error`, `btn-success`, `btn-warning`).
- **Tabelas**: `table table-sm` dentro de `card ... overflow-x-auto`; cabeçalho `bg-base-200/50`; linhas com `hover`; estado vazio como `<tr>` com `colspan` centralizado, `text-base-content/40`.
- **Badges de status**: componentes dedicados (`BadgeStatus`, `BadgeTipo`, `BadgeFinalidade` etc.) — nunca montar badge ad-hoc inline; se precisar de um novo status, criar/estender o composable `use<Entidade>Status` + o componente `Badge*` correspondente.
- **Formulários**: `form-control`, `label` + `label-text font-medium`, `input input-bordered` / `select select-bordered`, erro como `label-text-alt text-error` abaixo do campo, `input-error` na borda quando há erro.
- **Paginação**: padrão do Inertia paginator já em `Imoveis/Index.vue` — `btn btn-sm` + `btn-primary` (ativo) / `btn-ghost`.
- **Diálogos de confirmação/alerta**: **nunca** `import Swal from 'sweetalert2'` diretamente. Sempre `import Swal from '@/lib/swal'` (mixin com o tema aplicado) e, quando o botão de confirmação não for a ação primária azul, usar `customClass: swalClass('error' | 'success' | 'warning')` em vez de `confirmButtonColor` em hex.

## Estados

- Loading: `loading loading-spinner loading-sm` dentro do próprio botão de submit, com o texto trocado condicionalmente (`v-if="form.processing"`).
- Disabled: `:disabled` nativo + opacidade reduzida automática do DaisyUI — não usar opacidade custom.
- Foco: manter o anel de foco padrão do DaisyUI (não remover `outline`/`ring` em inputs ou botões).
- Vazio: mensagem curta centralizada + `text-base-content/40`, sem ilustração pesada.

## Acessibilidade (checklist rápido antes de entregar UI)

- Contraste texto/fundo ≥ 4.5:1 (os tokens semânticos do tema já garantem isso — não sobrepor com opacidade abaixo de `/50` em texto de leitura).
- Todo botão só-ícone tem `aria-label`.
- Cor nunca é o único indicador de status (badges sempre têm texto, não só cor).
- `prefers-reduced-motion` respeitado — transições já são curtas (`transition-[width] duration-300` no sidebar é o único caso maior).

## Antipadrões a evitar

- Hex cru em templates Vue (`#3b82f6`, `#dc2626`, etc.) — usar tokens semânticos.
- SVG inline colado no template quando existe ícone Lucide equivalente.
- `Swal` importado direto de `sweetalert2` (quebra o tema visual do modal).
- Misturar `rounded-full`/sombras pesadas em cards/botões — o tema é flat e de cantos discretos.
- Badge/status montado inline com classes soltas em vez de usar os componentes `Badge*` + composables `use*Status`.

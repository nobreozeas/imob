<?php

namespace App\Http\Controllers\Usuarios;

use App\Http\Controllers\Controller;
use App\Http\Requests\Usuarios\StoreUsuarioRequest;
use App\Http\Requests\Usuarios\UpdateUsuarioRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\Usuarios\UsuarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UsuarioController extends Controller
{
    public function __construct(private UsuarioService $service) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $query = User::with('roles')
            ->when($request->busca, function ($q, $busca) {
                $q->where(function ($q) use ($busca) {
                    $q->where('name', 'ilike', "%{$busca}%")
                      ->orWhere('email', 'ilike', "%{$busca}%");
                });
            })
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->role, fn ($q, $v) => $q->whereHas('roles', fn ($r) => $r->where('name', $v)))
            ->when($request->primeiro_acesso_pendente, fn ($q) => $q->where('deve_alterar_senha', true));

        $usuarios = $query->orderBy('name')->paginate(20)->withQueryString();

        return Inertia::render('Admin/Usuarios/Index', [
            'usuarios' => $usuarios,
            'filtros'  => $request->only(['busca', 'status', 'role', 'primeiro_acesso_pendente']),
            'roles'    => Role::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('Admin/Usuarios/Create', [
            'roles' => Role::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $dados = $request->validated();
        $dados['criado_por'] = $request->user()->id;

        $this->service->criar($dados);

        return redirect()
            ->route('usuarios.index')
            ->with('status', 'Usuário criado com sucesso. O acesso inicial foi enviado por e-mail.');
    }

    public function edit(User $usuario): Response
    {
        $this->authorize('update', $usuario);

        $usuario->load('roles');

        return Inertia::render('Admin/Usuarios/Edit', [
            'usuario' => $usuario,
            'roles'   => Role::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateUsuarioRequest $request, User $usuario): RedirectResponse
    {
        $this->service->atualizar($usuario, $request->validated());

        return redirect()
            ->route('usuarios.index')
            ->with('status', 'Usuário atualizado com sucesso.');
    }

    public function alterarStatus(Request $request, User $usuario): RedirectResponse
    {
        $this->authorize('alterarStatus', $usuario);

        $request->validate([
            'status' => ['required', 'in:ativo,inativo'],
        ]);

        if ($request->status === 'ativo') {
            $this->service->ativar($usuario);
        } else {
            $this->service->inativar($usuario);
        }

        return back()->with('status', 'Status do usuário atualizado com sucesso.');
    }

    public function reenviarAcesso(User $usuario): RedirectResponse
    {
        $this->authorize('reenviarAcesso', $usuario);

        $this->service->reenviarAcesso($usuario);

        return back()->with('status', 'Acesso inicial reenviado com sucesso.');
    }
}

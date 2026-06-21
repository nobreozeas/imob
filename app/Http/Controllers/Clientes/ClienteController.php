<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clientes\StoreClienteRequest;
use App\Http\Requests\Clientes\UpdateClienteRequest;
use App\Models\Cliente;
use App\Services\Clientes\ClienteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClienteController extends Controller
{
    public function __construct(private ClienteService $service) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Cliente::class);

        $query = Cliente::with('papeis')
            ->when($request->busca, function ($q, $busca) {
                $q->where(function ($q) use ($busca) {
                    $q->where('nome', 'ilike', "%{$busca}%")
                      ->orWhere('razao_social', 'ilike', "%{$busca}%")
                      ->orWhere('cpf', 'like', "%{$busca}%")
                      ->orWhere('cnpj', 'like', "%{$busca}%");
                });
            })
            ->when($request->tipo_pessoa, fn ($q, $v) => $q->where('tipo_pessoa', $v))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->cidade, fn ($q, $v) => $q->where('cidade', 'ilike', "%{$v}%"))
            ->when($request->papel, fn ($q, $v) => $q->porPapel($v));

        $ordenarPor = in_array($request->ordenar_por, ['nome', 'razao_social', 'created_at', 'status'])
            ? $request->ordenar_por
            : 'created_at';
        $direcao = $request->direcao === 'asc' ? 'asc' : 'desc';

        $clientes = $query->orderBy($ordenarPor, $direcao)->paginate(20)->withQueryString();

        return Inertia::render('Admin/Clientes/Index', [
            'clientes' => $clientes,
            'filtros' => $request->only(['busca', 'tipo_pessoa', 'papel', 'status', 'cidade', 'ordenar_por', 'direcao']),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Cliente::class);

        return Inertia::render('Admin/Clientes/Create');
    }

    public function store(StoreClienteRequest $request): RedirectResponse
    {
        $cliente = $this->service->criar($request->validated());

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('status', 'Cliente cadastrado com sucesso.');
    }

    public function show(Cliente $cliente): Response
    {
        $this->authorize('view', $cliente);

        $cliente->load(['papeis', 'dadosProprietario', 'dadosInquilino']);

        return Inertia::render('Admin/Clientes/Show', [
            'cliente' => $cliente,
        ]);
    }

    public function edit(Cliente $cliente): Response
    {
        $this->authorize('update', $cliente);

        $cliente->load(['papeis', 'dadosProprietario', 'dadosInquilino']);
  
        return Inertia::render('Admin/Clientes/Edit', [
            'cliente' => $cliente,
        ]);
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $this->service->atualizar($cliente, $request->validated());

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('status', 'Cliente atualizado com sucesso.');
    }

    public function alterarStatus(Request $request, Cliente $cliente): RedirectResponse
    {
        $this->authorize('alterarStatus', $cliente);

        $request->validate([
            'status' => ['required', 'in:ativo,inativo'],
        ]);

        $this->service->alterarStatus($cliente, $request->status);

        $label = $request->status === Cliente::STATUS_ATIVO ? 'ativado' : 'inativado';

        return back()->with('status', "Cliente {$label} com sucesso.");
    }
}

<?php

namespace App\Http\Controllers\Imoveis;

use App\Http\Controllers\Controller;
use App\Http\Requests\Imoveis\StoreImovelRequest;
use App\Http\Requests\Imoveis\UpdateImovelRequest;
use App\Models\Cliente;
use App\Models\Imovel;
use App\Models\User;
use App\Services\Imoveis\ImovelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ImovelController extends Controller
{
    public function __construct(private ImovelService $service) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Imovel::class);

        $filtrosEscopo = function ($query) use ($request) {
            $query
                ->when($request->busca, function ($q, $busca) {
                    $q->where(function ($q) use ($busca) {
                        $q->where('titulo', 'ilike', "%{$busca}%")
                          ->orWhere('codigo', 'ilike', "%{$busca}%");
                    });
                })
                ->when($request->tipo, fn ($q, $v) => $q->where('tipo', $v))
                ->when($request->finalidade, fn ($q, $v) => $q->where('finalidade', $v))
                ->when($request->cidade, fn ($q, $v) => $q->where('cidade', 'ilike', "%{$v}%"))
                ->when($request->bairro, fn ($q, $v) => $q->where('bairro', 'ilike', "%{$v}%"))
                ->when($request->proprietario_id, fn ($q, $v) => $q->where('proprietario_id', $v));
        };

        $verExcluidos = $request->boolean('excluidos') && $request->user()->can('imoveis.restore');

        $query = Imovel::with(['proprietario', 'fotos'])
            ->when($verExcluidos, fn ($q) => $q->onlyTrashed());
        $filtrosEscopo($query);
        $query->when($request->status, fn ($q, $v) => $q->where('status', $v));

        $ordenarPor = in_array($request->ordenar_por, ['titulo', 'codigo', 'created_at', 'status', 'cidade'])
            ? $request->ordenar_por
            : 'created_at';
        $direcao = $request->direcao === 'asc' ? 'asc' : 'desc';

        $imoveis = $query->orderBy($ordenarPor, $direcao)->paginate(20)->withQueryString();

        $indicadoresQuery = Imovel::query();
        $filtrosEscopo($indicadoresQuery);
        $indicadores = $indicadoresQuery
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return Inertia::render('Admin/Imoveis/Index', [
            'imoveis'      => $imoveis,
            'filtros'      => $request->only(['busca', 'tipo', 'finalidade', 'status', 'cidade', 'bairro', 'proprietario_id', 'ordenar_por', 'direcao', 'excluidos']),
            'indicadores'  => [
                'total'         => $indicadores->sum(),
                'disponivel'    => $indicadores->get(Imovel::STATUS_DISPONIVEL, 0),
                'reservado'     => $indicadores->get(Imovel::STATUS_RESERVADO, 0),
                'alugado'       => $indicadores->get(Imovel::STATUS_ALUGADO, 0),
                'em_manutencao' => $indicadores->get(Imovel::STATUS_MANUTENCAO, 0),
                'inativo'       => $indicadores->get(Imovel::STATUS_INATIVO, 0),
            ],
            'proprietarios' => Cliente::with('papeis')
                ->whereHas('papeis', fn ($q) => $q->where('papel', 'proprietario'))
                ->orderBy('nome')
                ->get(['id', 'nome', 'razao_social', 'tipo_pessoa']),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Imovel::class);

        return Inertia::render('Admin/Imoveis/Create', [
            'proprietarios' => $this->proprietarios(),
            'corretores'    => $this->corretores(),
        ]);
    }

    public function store(StoreImovelRequest $request): RedirectResponse
    {
        $dados = $request->validated();
        $dados['criado_por'] = $request->user()->id;

        $imovel = $this->service->criar($dados);

        return redirect()
            ->route('imoveis.show', $imovel)
            ->with('status', 'Imóvel cadastrado com sucesso.');
    }

    public function show(Imovel $imovel): Response
    {
        $this->authorize('view', $imovel);

        $imovel->load(['proprietario', 'corretor', 'caracteristicas', 'dadosComerciais', 'fotos', 'documentos']);

        return Inertia::render('Admin/Imoveis/Show', [
            'imovel'     => $imovel,
            'historicos' => $imovel->historicos()->with('usuario:id,name')->paginate(20, pageName: 'historico_page'),
        ]);
    }

    public function edit(Imovel $imovel): Response
    {
        $this->authorize('update', $imovel);

        $imovel->load(['proprietario', 'corretor', 'caracteristicas', 'dadosComerciais', 'fotos', 'documentos']);

        return Inertia::render('Admin/Imoveis/Edit', [
            'imovel'        => $imovel,
            'proprietarios' => $this->proprietarios(),
            'corretores'    => $this->corretores(),
        ]);
    }

    public function update(UpdateImovelRequest $request, Imovel $imovel): RedirectResponse
    {
        $this->authorize('update', $imovel);

        $this->service->atualizar($imovel, $request->validated(), $request->user()->id);

        return redirect()
            ->route('imoveis.show', $imovel)
            ->with('status', 'Imóvel atualizado com sucesso.');
    }

    public function alterarStatus(Request $request, Imovel $imovel): RedirectResponse
    {
        $this->authorize('alterarStatus', $imovel);

        $request->validate([
            'status' => ['required', 'in:disponivel,reservado,alugado,em_manutencao,inativo'],
        ]);

        $this->service->alterarStatus($imovel, $request->status, $request->user()->id);

        return back()->with('status', 'Status do imóvel atualizado com sucesso.');
    }

    public function destroy(Request $request, Imovel $imovel): RedirectResponse
    {
        $this->authorize('delete', $imovel);

        $this->service->excluir($imovel, $request->user()->id);

        return redirect()
            ->route('imoveis.index')
            ->with('status', 'Imóvel excluído com sucesso.');
    }

    public function restore(Request $request, Imovel $imovel): RedirectResponse
    {
        $this->authorize('restore', $imovel);

        $this->service->restaurar($imovel, $request->user()->id);

        return back()->with('status', 'Imóvel restaurado com sucesso.');
    }

    private function proprietarios()
    {
        return Cliente::with('papeis')
            ->whereHas('papeis', fn ($q) => $q->where('papel', 'proprietario'))
            ->where('status', 'ativo')
            ->orderBy('nome')
            ->get(['id', 'nome', 'razao_social', 'tipo_pessoa']);
    }

    private function corretores()
    {
        return User::orderBy('name')->get(['id', 'name']);
    }
}

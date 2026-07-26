<?php

namespace App\Http\Controllers\Contratos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contratos\StoreContratoRequest;
use App\Http\Requests\Contratos\StoreRescisaoContratoRequest;
use App\Http\Requests\Contratos\UpdateContratoRequest;
use App\Models\Cliente;
use App\Models\ContratoDocumento;
use App\Models\ContratoLocacao;
use App\Models\Imovel;
use App\Models\User;
use App\Services\Contratos\ContratoDocumentoService;
use App\Services\Contratos\ContratoHistoricoService;
use App\Services\Contratos\ContratoLocacaoService;
use App\Services\Contratos\ContratoStatusService;
use App\Services\Contratos\RescisaoContratoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContratoLocacaoController extends Controller
{
    public function __construct(
        private ContratoLocacaoService $service,
        private ContratoStatusService $statusService,
        private ContratoDocumentoService $documentoService,
        private ContratoHistoricoService $historicoService,
        private RescisaoContratoService $rescisaoService,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', ContratoLocacao::class);

        $query = ContratoLocacao::with(['imovel', 'proprietario', 'inquilino'])
            ->when($request->busca, function ($q, $busca) {
                $q->where(function ($q) use ($busca) {
                    $q->where('numero', 'ilike', "%{$busca}%");
                });
            })
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->imovel_id, fn ($q, $v) => $q->where('imovel_id', $v))
            ->when($request->proprietario_id, fn ($q, $v) => $q->where('proprietario_id', $v))
            ->when($request->inquilino_id, fn ($q, $v) => $q->where('inquilino_id', $v))
            ->when($request->data_inicio_de, fn ($q, $v) => $q->whereDate('data_inicio', '>=', $v))
            ->when($request->data_inicio_ate, fn ($q, $v) => $q->whereDate('data_inicio', '<=', $v));

        $contratos = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Admin/Contratos/Index', [
            'contratos' => $contratos,
            'filtros'   => $request->only(['busca', 'status', 'imovel_id', 'proprietario_id', 'inquilino_id', 'data_inicio_de', 'data_inicio_ate']),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', ContratoLocacao::class);

        return Inertia::render('Admin/Contratos/Create', [
            'imoveis'    => $this->imoveisDisponiveis(),
            'inquilinos' => $this->inquilinos(),
            'corretores' => $this->corretores(),
        ]);
    }

    public function store(StoreContratoRequest $request): RedirectResponse
    {
        $contrato = $this->service->criar($request->validated(), $request->user());

        return redirect()
            ->route('contratos.show', $contrato)
            ->with('status', 'Contrato criado com sucesso.');
    }

    public function show(ContratoLocacao $contrato): Response
    {
        $this->authorize('view', $contrato);

        $contrato->load([
            'imovel', 'proprietario', 'inquilino', 'corretor', 'criadoPor',
            'encargos', 'caucao.movimentacoes.criador', 'multas',
            'documentos.criador', 'historicos.usuario',
            'parcelas', 'repasses.parcela',
        ]);

        return Inertia::render('Admin/Contratos/Show', [
            'contrato' => $contrato,
        ]);
    }

    public function edit(ContratoLocacao $contrato): Response
    {
        $this->authorize('update', $contrato);

        $contrato->load(['imovel', 'proprietario', 'inquilino', 'corretor', 'encargos', 'caucao', 'multas', 'documentos']);

        return Inertia::render('Admin/Contratos/Edit', [
            'contrato'   => $contrato,
            'imoveis'    => $this->imoveisDisponiveis($contrato->imovel_id),
            'inquilinos' => $this->inquilinos(),
            'corretores' => $this->corretores(),
        ]);
    }

    public function update(UpdateContratoRequest $request, ContratoLocacao $contrato): RedirectResponse
    {
        $this->service->atualizar($contrato, $request->validated(), $request->user());

        return redirect()
            ->route('contratos.show', $contrato)
            ->with('status', 'Contrato atualizado com sucesso.');
    }

    public function ativar(Request $request, ContratoLocacao $contrato): RedirectResponse
    {
        $this->authorize('ativar', $contrato);
        $this->statusService->ativar($contrato, $request->user()->id);

        return back()->with('status', 'Contrato ativado com sucesso.');
    }

    public function enviarParaAssinatura(Request $request, ContratoLocacao $contrato): RedirectResponse
    {
        $this->authorize('update', $contrato);
        $this->statusService->enviarParaAssinatura($contrato, $request->user()->id);

        return back()->with('status', 'Contrato enviado para assinatura.');
    }

    public function cancelar(Request $request, ContratoLocacao $contrato): RedirectResponse
    {
        $this->authorize('cancelar', $contrato);
        $this->statusService->cancelar($contrato, $request->user()->id);

        return back()->with('status', 'Contrato cancelado.');
    }

    public function encerrar(Request $request, ContratoLocacao $contrato): RedirectResponse
    {
        $this->authorize('encerrar', $contrato);

        $dados = $request->validate([
            'data_encerramento'  => ['required', 'date'],
            'motivo_encerramento' => ['nullable', 'string'],
            'valor_devolvido'    => ['nullable', 'numeric', 'min:0'],
            'data_devolucao_caucao' => ['nullable', 'date'],
            'observacao_caucao'  => ['nullable', 'string'],
        ]);

        $this->statusService->encerrar($contrato, $dados, $request->user()->id);

        return back()->with('status', 'Contrato encerrado com sucesso.');
    }

    public function rescindir(StoreRescisaoContratoRequest $request, ContratoLocacao $contrato): RedirectResponse
    {
        $this->authorize('rescindir', $contrato);

        $this->rescisaoService->rescindir($contrato, $request->validated(), $request->user()->id);

        return back()->with('status', 'Contrato rescindido.');
    }

    public function adicionarDocumento(Request $request, ContratoLocacao $contrato): RedirectResponse
    {
        $this->authorize('documentos', $contrato);

        $dados = $request->validate([
            'documento' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,docx', 'max:20480'],
            'tipo'      => ['nullable', 'in:contrato_assinado,laudo_vistoria,comprovante_caucao,outros'],
        ]);

        $this->documentoService->salvarDocumentos(
            $contrato,
            [$dados['documento']],
            [$dados['tipo'] ?? 'outros'],
            $request->user()->id,
        );

        $this->historicoService->registrar(
            $contrato,
            'documento_adicionado',
            "Documento '{$dados['documento']->getClientOriginalName()}' adicionado.",
            [],
            ['tipo' => $dados['tipo'] ?? 'outros'],
            $request->user()->id,
        );

        return back()->with('status', 'Documento adicionado com sucesso.');
    }

    public function removerDocumento(Request $request, ContratoLocacao $contrato, ContratoDocumento $documento): RedirectResponse
    {
        $this->authorize('documentos', $contrato);

        $this->documentoService->removerDocumento($documento);

        return back()->with('status', 'Documento removido.');
    }

    private function imoveisDisponiveis(?string $incluirImovelId = null)
    {
        return Imovel::with('proprietario')
            ->where(function ($q) use ($incluirImovelId) {
                $q->where('status', Imovel::STATUS_DISPONIVEL);
                if ($incluirImovelId) {
                    $q->orWhere('id', $incluirImovelId);
                }
            })
            ->where('finalidade', '!=', 'venda')
            ->orderBy('titulo')
            ->get(['id', 'codigo', 'titulo', 'proprietario_id', 'status'])
            ->map(fn ($i) => [
                'id'             => $i->id,
                'codigo'         => $i->codigo,
                'titulo'         => $i->titulo,
                'proprietario_id' => $i->proprietario_id,
                'proprietario_nome' => $i->proprietario?->nome ?? $i->proprietario?->razao_social,
            ]);
    }

    private function inquilinos()
    {
        return Cliente::with('papeis')
            ->whereHas('papeis', fn ($q) => $q->where('papel', 'inquilino'))
            ->where('status', 'ativo')
            ->orderBy('nome')
            ->get(['id', 'nome', 'razao_social', 'tipo_pessoa', 'cpf', 'cnpj']);
    }

    private function corretores()
    {
        return User::orderBy('name')->get(['id', 'name']);
    }
}

<?php

namespace App\Services\Contratos;

use App\Models\ContratoLocacao;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ContratoLocacaoService
{
    public function __construct(
        private ContratoDocumentoService $documentoService,
        private ContratoHistoricoService $historicoService,
        private ContratoStatusService $statusService,
    ) {}

    public function criar(array $dados, User $criador): ContratoLocacao
    {
        return DB::transaction(function () use ($dados, $criador) {
            if (empty($dados['numero'])) {
                $dados['numero'] = $this->gerarNumero();
            }

            $acao = $dados['acao'] ?? 'rascunho';
            $status = $acao === 'ativar'
                ? ContratoLocacao::STATUS_AGUARDANDO_ASSINATURA
                : ContratoLocacao::STATUS_RASCUNHO;

            $contrato = ContratoLocacao::create([
                'numero'                 => $dados['numero'],
                'status'                 => $status,
                'tipo_contrato'          => $dados['tipo_contrato'] ?? ContratoLocacao::TIPO_RESIDENCIAL,
                'objetivo_contrato'      => $dados['objetivo_contrato'] ?? 'aluguel',
                'imovel_id'              => $dados['imovel_id'],
                'proprietario_id'        => $dados['proprietario_id'],
                'inquilino_id'           => $dados['inquilino_id'],
                'corretor_id'            => $dados['corretor_id'] ?? null,
                'criado_por'             => $criador->id,
                'data_inicio'            => $dados['data_inicio'],
                'data_fim'               => $dados['data_fim'] ?? null,
                'dia_vencimento'         => $dados['dia_vencimento'],
                'duracao_meses'          => $dados['duracao_meses'] ?? null,
                'valor_aluguel'          => $dados['valor_aluguel'],
                'indice_reajuste'        => $dados['indice_reajuste'] ?? ContratoLocacao::INDICE_IGPM,
                'periodicidade_reajuste' => $dados['periodicidade_reajuste'] ?? 12,
                'data_primeiro_reajuste' => $dados['data_primeiro_reajuste'] ?? null,
                'tipo_taxa_administracao' => $dados['tipo_taxa_administracao'] ?? ContratoLocacao::TAXA_PERCENTUAL,
                'valor_taxa_administracao' => $dados['valor_taxa_administracao'] ?? 0,
                'dia_repasse'            => $dados['dia_repasse'] ?? null,
                'forma_repasse'          => $dados['forma_repasse'] ?? ContratoLocacao::REPASSE_PIX,
                'banco'                  => $dados['banco'] ?? null,
                'agencia'                => $dados['agencia'] ?? null,
                'conta'                  => $dados['conta'] ?? null,
                'tipo_conta'             => $dados['tipo_conta'] ?? null,
                'pix_key'                => $dados['pix_key'] ?? null,
                'observacoes'            => $dados['observacoes'] ?? null,
            ]);

            $this->sincronizarEncargos($contrato, $dados['encargos'] ?? []);

            $contrato->caucao()->create($this->dadosCaucao($dados));
            $contrato->multas()->create($this->dadosMultas($dados));

            if (!empty($dados['documentos_novos'])) {
                $this->documentoService->salvarDocumentos(
                    $contrato,
                    $dados['documentos_novos'],
                    $dados['tipos_documentos'] ?? [],
                    $criador->id,
                );
            }

            $this->historicoService->registrar(
                $contrato,
                'criacao',
                "Contrato {$contrato->numero} criado.",
                [],
                ['status' => $status, 'numero' => $contrato->numero],
                $criador->id,
            );

            if ($acao === 'ativar') {
                $this->statusService->ativar($contrato, $criador->id);
                $contrato->refresh();
            }

            return $contrato;
        });
    }

    public function atualizar(ContratoLocacao $contrato, array $dados, User $usuario): ContratoLocacao
    {
        if (!$contrato->permite_edicao_completa) {
            throw ValidationException::withMessages([
                'status' => 'Apenas contratos em rascunho podem ser editados completamente.',
            ]);
        }

        return DB::transaction(function () use ($contrato, $dados, $usuario) {
            $dadosAnteriores = [
                'valor_aluguel' => $contrato->valor_aluguel,
                'data_inicio'   => $contrato->data_inicio,
                'data_fim'      => $contrato->data_fim,
                'inquilino_id'  => $contrato->inquilino_id,
            ];

            $contrato->update([
                'numero'                 => $dados['numero'] ?? $contrato->numero,
                'tipo_contrato'          => $dados['tipo_contrato'] ?? $contrato->tipo_contrato,
                'objetivo_contrato'      => $dados['objetivo_contrato'] ?? $contrato->objetivo_contrato,
                'imovel_id'              => $dados['imovel_id'] ?? $contrato->imovel_id,
                'proprietario_id'        => $dados['proprietario_id'] ?? $contrato->proprietario_id,
                'inquilino_id'           => $dados['inquilino_id'] ?? $contrato->inquilino_id,
                'corretor_id'            => $dados['corretor_id'] ?? null,
                'data_inicio'            => $dados['data_inicio'] ?? $contrato->data_inicio,
                'data_fim'               => $dados['data_fim'] ?? null,
                'dia_vencimento'         => $dados['dia_vencimento'] ?? $contrato->dia_vencimento,
                'duracao_meses'          => $dados['duracao_meses'] ?? null,
                'valor_aluguel'          => $dados['valor_aluguel'] ?? $contrato->valor_aluguel,
                'indice_reajuste'        => $dados['indice_reajuste'] ?? $contrato->indice_reajuste,
                'periodicidade_reajuste' => $dados['periodicidade_reajuste'] ?? $contrato->periodicidade_reajuste,
                'data_primeiro_reajuste' => $dados['data_primeiro_reajuste'] ?? null,
                'tipo_taxa_administracao' => $dados['tipo_taxa_administracao'] ?? $contrato->tipo_taxa_administracao,
                'valor_taxa_administracao' => $dados['valor_taxa_administracao'] ?? $contrato->valor_taxa_administracao,
                'dia_repasse'            => $dados['dia_repasse'] ?? null,
                'forma_repasse'          => $dados['forma_repasse'] ?? $contrato->forma_repasse,
                'banco'                  => $dados['banco'] ?? null,
                'agencia'                => $dados['agencia'] ?? null,
                'conta'                  => $dados['conta'] ?? null,
                'tipo_conta'             => $dados['tipo_conta'] ?? null,
                'pix_key'                => $dados['pix_key'] ?? null,
                'observacoes'            => $dados['observacoes'] ?? null,
            ]);

            $this->sincronizarEncargos($contrato, $dados['encargos'] ?? []);

            $contrato->caucao()->updateOrCreate(
                ['contrato_id' => $contrato->id],
                $this->dadosCaucao($dados),
            );

            $contrato->multas()->updateOrCreate(
                ['contrato_id' => $contrato->id],
                $this->dadosMultas($dados),
            );

            if (!empty($dados['documentos_novos'])) {
                $this->documentoService->salvarDocumentos(
                    $contrato,
                    $dados['documentos_novos'],
                    $dados['tipos_documentos'] ?? [],
                    $usuario->id,
                );
            }

            $this->historicoService->registrar(
                $contrato,
                'alteracao',
                "Contrato {$contrato->numero} atualizado.",
                $dadosAnteriores,
                [
                    'valor_aluguel' => $contrato->valor_aluguel,
                    'data_inicio'   => $dados['data_inicio'] ?? $contrato->data_inicio,
                    'inquilino_id'  => $dados['inquilino_id'] ?? $contrato->inquilino_id,
                ],
                $usuario->id,
            );

            return $contrato->fresh();
        });
    }

    public function gerarNumero(): string
    {
        $prefixo = 'LOC-' . now()->format('Ym') . '-';

        $ultimo = DB::selectOne(
            "SELECT numero FROM contratos_locacao WHERE numero LIKE ? ORDER BY numero DESC LIMIT 1",
            [$prefixo . '%']
        );

        $seq = 1;
        if ($ultimo) {
            $partes = explode('-', $ultimo->numero);
            $seq = ((int) end($partes)) + 1;
        }

        return $prefixo . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    private function sincronizarEncargos(ContratoLocacao $contrato, array $encargos): void
    {
        $contrato->encargos()->delete();

        foreach ($encargos as $encargo) {
            $contrato->encargos()->create([
                'tipo_encargo' => $encargo['tipo_encargo'],
                'responsavel'  => $encargo['responsavel'] ?? 'nao_se_aplica',
                'observacao'   => $encargo['observacao'] ?? null,
            ]);
        }
    }

    private function dadosCaucao(array $dados): array
    {
        $caucao = $dados['caucao'] ?? [];

        return [
            'possui_caucao'           => (bool) ($caucao['possui_caucao'] ?? false),
            'tipo_caucao'             => $caucao['tipo_caucao'] ?? null,
            'valor_caucao'            => $caucao['valor_caucao'] ?? null,
            'data_recebimento_caucao' => $caucao['data_recebimento_caucao'] ?? null,
            'status_caucao'           => 'recebida',
        ];
    }

    private function dadosMultas(array $dados): array
    {
        $multas = $dados['multas'] ?? [];

        return [
            'possui_multa_atraso'       => (bool) ($multas['possui_multa_atraso'] ?? false),
            'percentual_multa_atraso'   => $multas['percentual_multa_atraso'] ?? null,
            'valor_juros_dia'           => $multas['valor_juros_dia'] ?? null,
            'possui_multa_rescisao'     => (bool) ($multas['possui_multa_rescisao'] ?? false),
            'percentual_multa_rescisao' => $multas['percentual_multa_rescisao'] ?? null,
            'base_calculo_rescisao'     => $multas['base_calculo_rescisao'] ?? null,
        ];
    }
}

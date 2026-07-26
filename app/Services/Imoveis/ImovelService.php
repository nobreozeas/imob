<?php

namespace App\Services\Imoveis;

use App\Models\Imovel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ImovelService
{
    public function __construct(
        private ImovelMidiaService $midia,
        private ImovelHistoricoService $historico,
    ) {}

    public function criar(array $dados): Imovel
    {
        return DB::transaction(function () use ($dados) {
            if (empty($dados['codigo'])) {
                $dados['codigo'] = $this->gerarCodigo();
            }

            $imovel = Imovel::create([
                'codigo'          => $dados['codigo'],
                'titulo'          => $dados['titulo'],
                'tipo'            => $dados['tipo'],
                'finalidade'      => $dados['finalidade'],
                'status'          => $dados['status'] ?? Imovel::STATUS_DISPONIVEL,
                'proprietario_id' => $dados['proprietario_id'],
                'corretor_id'     => $dados['corretor_id'] ?? null,
                'descricao'       => $dados['descricao'] ?? null,
                'cep'             => $dados['cep'] ?? null,
                'logradouro'      => $dados['logradouro'] ?? null,
                'numero'          => $dados['numero'] ?? null,
                'complemento'     => $dados['complemento'] ?? null,
                'bairro'          => $dados['bairro'] ?? null,
                'cidade'          => $dados['cidade'] ?? null,
                'estado'          => $dados['estado'] ?? null,
                'ponto_referencia' => $dados['ponto_referencia'] ?? null,
                'criado_por'      => $dados['criado_por'],
            ]);

            $imovel->caracteristicas()->create($this->dadosCaracteristicas($dados));
            $imovel->dadosComerciais()->create($this->dadosComerciaisData($dados));

            $this->midia->sincronizarFotos($imovel, $dados, $dados['criado_por']);
            $this->midia->sincronizarDocumentos($imovel, $dados, $dados['criado_por']);

            $this->historico->registrar(
                $imovel,
                'criacao',
                "Imóvel {$imovel->codigo} cadastrado.",
                usuarioId: $dados['criado_por'],
            );

            return $imovel;
        });
    }

    public function atualizar(Imovel $imovel, array $dados, ?string $usuarioId = null): Imovel
    {
        return DB::transaction(function () use ($imovel, $dados, $usuarioId) {
            $imovel->update([
                'codigo'          => $dados['codigo'] ?? $imovel->codigo,
                'titulo'          => $dados['titulo'],
                'tipo'            => $dados['tipo'],
                'finalidade'      => $dados['finalidade'],
                'status'          => $dados['status'],
                'proprietario_id' => $dados['proprietario_id'],
                'corretor_id'     => $dados['corretor_id'] ?? null,
                'descricao'       => $dados['descricao'] ?? null,
                'cep'             => $dados['cep'] ?? null,
                'logradouro'      => $dados['logradouro'] ?? null,
                'numero'          => $dados['numero'] ?? null,
                'complemento'     => $dados['complemento'] ?? null,
                'bairro'          => $dados['bairro'] ?? null,
                'cidade'          => $dados['cidade'] ?? null,
                'estado'          => $dados['estado'] ?? null,
                'ponto_referencia' => $dados['ponto_referencia'] ?? null,
            ]);

            $imovel->caracteristicas()->updateOrCreate(
                ['imovel_id' => $imovel->id],
                $this->dadosCaracteristicas($dados)
            );

            $imovel->dadosComerciais()->updateOrCreate(
                ['imovel_id' => $imovel->id],
                $this->dadosComerciaisData($dados)
            );

            $this->midia->sincronizarFotos($imovel, $dados, $usuarioId);
            $this->midia->sincronizarDocumentos($imovel, $dados, $usuarioId);

            $this->historico->registrar(
                $imovel,
                'atualizacao',
                "Imóvel {$imovel->codigo} atualizado.",
                usuarioId: $usuarioId,
            );

            return $imovel->fresh();
        });
    }

    public function alterarStatus(Imovel $imovel, string $status, ?string $usuarioId = null): void
    {
        if ($status === Imovel::STATUS_DISPONIVEL && $imovel->temContratoAtivo()) {
            throw ValidationException::withMessages([
                'status' => 'Não é possível definir o imóvel como disponível enquanto houver contrato ativo.',
            ]);
        }

        $statusAnterior = $imovel->status;

        $imovel->update(['status' => $status]);

        $this->historico->registrar(
            $imovel,
            'alteracao_status',
            "Status alterado de \"{$statusAnterior}\" para \"{$status}\".",
            ['status' => $statusAnterior],
            ['status' => $status],
            $usuarioId,
        );
    }

    public function excluir(Imovel $imovel, ?string $usuarioId = null): void
    {
        if ($imovel->temContratoAtivo()) {
            throw ValidationException::withMessages([
                'imovel' => 'Não é possível excluir um imóvel com contrato ativo.',
            ]);
        }

        $imovel->delete();

        $this->historico->registrar(
            $imovel,
            'exclusao',
            "Imóvel {$imovel->codigo} excluído.",
            usuarioId: $usuarioId,
        );
    }

    public function restaurar(Imovel $imovel, ?string $usuarioId = null): void
    {
        $imovel->restore();

        $this->historico->registrar(
            $imovel,
            'restauracao',
            "Imóvel {$imovel->codigo} restaurado.",
            usuarioId: $usuarioId,
        );
    }

    private function gerarCodigo(): string
    {
        $prefixo = 'IMO-' . now()->format('Ym') . '-';

        $ultimo = DB::selectOne(
            "SELECT codigo FROM imoveis WHERE codigo LIKE ? ORDER BY codigo DESC LIMIT 1",
            [$prefixo . '%']
        );

        $seq = 1;
        if ($ultimo) {
            $partes = explode('-', $ultimo->codigo);
            $seq = ((int) end($partes)) + 1;
        }

        return $prefixo . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    private function dadosCaracteristicas(array $dados): array
    {
        return [
            'area_total'            => $dados['caracteristicas']['area_total'] ?? null,
            'area_construida'       => $dados['caracteristicas']['area_construida'] ?? null,
            'quartos'               => $dados['caracteristicas']['quartos'] ?? 0,
            'suites'                => $dados['caracteristicas']['suites'] ?? 0,
            'banheiros'             => $dados['caracteristicas']['banheiros'] ?? 0,
            'vagas_garagem'         => $dados['caracteristicas']['vagas_garagem'] ?? 0,
            'mobiliado'             => $dados['caracteristicas']['mobiliado'] ?? false,
            'aceita_pet'            => $dados['caracteristicas']['aceita_pet'] ?? false,
            'possui_piscina'        => $dados['caracteristicas']['possui_piscina'] ?? false,
            'possui_quintal'        => $dados['caracteristicas']['possui_quintal'] ?? false,
            'possui_varanda'        => $dados['caracteristicas']['possui_varanda'] ?? false,
            'outras_caracteristicas' => $dados['caracteristicas']['outras_caracteristicas'] ?? null,
        ];
    }

    private function dadosComerciaisData(array $dados): array
    {
        return [
            'valor_aluguel'          => $dados['dados_comerciais']['valor_aluguel'] ?? null,
            'valor_venda'            => $dados['dados_comerciais']['valor_venda'] ?? null,
            'valor_condominio'       => $dados['dados_comerciais']['valor_condominio'] ?? null,
            'valor_iptu'             => $dados['dados_comerciais']['valor_iptu'] ?? null,
            'condominio_incluso'     => $dados['dados_comerciais']['condominio_incluso'] ?? false,
            'valor_caucao_sugerido'  => $dados['dados_comerciais']['valor_caucao_sugerido'] ?? null,
            'observacoes_comerciais' => $dados['dados_comerciais']['observacoes_comerciais'] ?? null,
        ];
    }
}

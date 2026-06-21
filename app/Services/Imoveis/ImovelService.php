<?php

namespace App\Services\Imoveis;

use App\Models\Imovel;
use App\Models\ImovelDocumento;
use App\Models\ImovelFoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ImovelService
{
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

            $this->sincronizarFotos($imovel, $dados);
            $this->sincronizarDocumentos($imovel, $dados);

            return $imovel;
        });
    }

    public function atualizar(Imovel $imovel, array $dados): Imovel
    {
        return DB::transaction(function () use ($imovel, $dados) {
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

            $this->sincronizarFotos($imovel, $dados);
            $this->sincronizarDocumentos($imovel, $dados);

            return $imovel->fresh();
        });
    }

    public function alterarStatus(Imovel $imovel, string $status): void
    {
        if ($status === Imovel::STATUS_DISPONIVEL && $imovel->temContratoAtivo()) {
            throw ValidationException::withMessages([
                'status' => 'Não é possível definir o imóvel como disponível enquanto houver contrato ativo.',
            ]);
        }

        $imovel->update(['status' => $status]);
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

    private function sincronizarFotos(Imovel $imovel, array $dados): void
    {
        // Remove fotos marcadas para remoção
        if (!empty($dados['fotos_remover'])) {
            $fotos = ImovelFoto::whereIn('id', $dados['fotos_remover'])
                ->where('imovel_id', $imovel->id)
                ->get();

            foreach ($fotos as $foto) {
                Storage::disk('public')->delete($foto->caminho);
                $foto->delete();
            }
        }

        // Salva novas fotos
        if (!empty($dados['fotos_novas'])) {
            $pasta = "imoveis/{$imovel->id}/fotos";
            $ordem = $imovel->fotos()->max('ordem') ?? 0;

            foreach ($dados['fotos_novas'] as $arquivo) {
                if ($arquivo instanceof UploadedFile) {
                    $caminho = $arquivo->store($pasta, 'public');
                    $ordem++;
                    $imovel->fotos()->create([
                        'caminho'      => $caminho,
                        'nome_original' => $arquivo->getClientOriginalName(),
                        'is_principal' => false,
                        'ordem'        => $ordem,
                    ]);
                }
            }
        }

        // Atualiza foto principal
        if (!empty($dados['foto_principal_id'])) {
            $imovel->fotos()->update(['is_principal' => false]);
            $imovel->fotos()
                ->where('id', $dados['foto_principal_id'])
                ->update(['is_principal' => true]);
        } elseif ($imovel->fotos()->where('is_principal', true)->doesntExist()) {
            // Define a primeira foto como principal automaticamente
            $primeira = $imovel->fotos()->orderBy('ordem')->first();
            if ($primeira) {
                $primeira->update(['is_principal' => true]);
            }
        }
    }

    private function sincronizarDocumentos(Imovel $imovel, array $dados): void
    {
        // Remove documentos marcados para remoção
        if (!empty($dados['documentos_remover'])) {
            $documentos = ImovelDocumento::whereIn('id', $dados['documentos_remover'])
                ->where('imovel_id', $imovel->id)
                ->get();

            foreach ($documentos as $doc) {
                Storage::disk('public')->delete($doc->caminho);
                $doc->delete();
            }
        }

        // Salva novos documentos
        if (!empty($dados['documentos_novos'])) {
            $pasta = "imoveis/{$imovel->id}/documentos";
            $tipos = $dados['tipos_documentos'] ?? [];

            foreach ($dados['documentos_novos'] as $index => $arquivo) {
                if ($arquivo instanceof UploadedFile) {
                    $caminho = $arquivo->store($pasta, 'public');
                    $imovel->documentos()->create([
                        'caminho'      => $caminho,
                        'nome_original' => $arquivo->getClientOriginalName(),
                        'tipo'         => $tipos[$index] ?? null,
                    ]);
                }
            }
        }
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
            'responsavel_iptu'       => $dados['dados_comerciais']['responsavel_iptu'] ?? null,
            'responsavel_agua'       => $dados['dados_comerciais']['responsavel_agua'] ?? null,
            'responsavel_energia'    => $dados['dados_comerciais']['responsavel_energia'] ?? null,
            'responsavel_condominio' => $dados['dados_comerciais']['responsavel_condominio'] ?? null,
            'valor_caucao_sugerido'  => $dados['dados_comerciais']['valor_caucao_sugerido'] ?? null,
            'observacoes_comerciais' => $dados['dados_comerciais']['observacoes_comerciais'] ?? null,
        ];
    }
}

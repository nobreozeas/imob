<?php

namespace App\Services\Clientes;

use App\Models\Cliente;
use App\Models\ClientePapel;
use Illuminate\Support\Facades\DB;

class ClienteService
{
    public function criar(array $dados): Cliente
    {
        return DB::transaction(function () use ($dados) {
            $cliente = Cliente::create([
                ...$this->camposCliente($dados),
                'criado_por' => auth()->id(),
            ]);

            $this->sincronizarPapeis($cliente, $dados['papeis'] ?? []);
            $this->sincronizarDadosAdicionais($cliente, $dados);

            return $cliente->load(['papeis', 'dadosProprietario', 'dadosInquilino']);
        });
    }

    public function atualizar(Cliente $cliente, array $dados): Cliente
    {
        return DB::transaction(function () use ($cliente, $dados) {
            $cliente->update($this->camposCliente($dados));

            $this->sincronizarPapeis($cliente, $dados['papeis'] ?? []);
            $this->sincronizarDadosAdicionais($cliente, $dados);

            return $cliente->load(['papeis', 'dadosProprietario', 'dadosInquilino']);
        });
    }

    public function alterarStatus(Cliente $cliente, string $status): void
    {
        $cliente->update(['status' => $status]);
    }

    public function verificarVinculosPapel(Cliente $cliente, string $papel): bool
    {
        // Quando módulos de imóveis e contratos forem implementados,
        // este método verificará vínculos ativos antes de permitir remoção do papel.
        return false;
    }

    public function sincronizarDadosAdicionais(Cliente $cliente, array $dados): void
    {
        $papeis = $cliente->papeis()->pluck('papel')->toArray();

        if (in_array(ClientePapel::PROPRIETARIO, $papeis)) {
            $camposProprietario = $this->camposProprietario($dados);
            $cliente->dadosProprietario()->updateOrCreate(
                ['cliente_id' => $cliente->id],
                $camposProprietario
            );
        } else {
            $cliente->dadosProprietario()->delete();
        }

        if (in_array(ClientePapel::INQUILINO, $papeis)) {
            $camposInquilino = $this->camposInquilino($dados);
            $cliente->dadosInquilino()->updateOrCreate(
                ['cliente_id' => $cliente->id],
                $camposInquilino
            );
        } else {
            $cliente->dadosInquilino()->delete();
        }
    }

    private function sincronizarPapeis(Cliente $cliente, array $papeis): void
    {
        $existentes = $cliente->papeis()->pluck('papel')->toArray();
        $novos = array_values(array_unique($papeis));

        $adicionar = array_diff($novos, $existentes);
        $remover = array_diff($existentes, $novos);

        foreach ($adicionar as $papel) {
            $cliente->papeis()->create(['papel' => $papel]);
        }

        if (!empty($remover)) {
            $cliente->papeis()->whereIn('papel', $remover)->delete();
        }
    }

    private function camposCliente(array $dados): array
    {
        return array_filter([
            'tipo_pessoa' => $dados['tipo_pessoa'] ?? null,
            'nome' => $dados['nome'] ?? null,
            'razao_social' => $dados['razao_social'] ?? null,
            'nome_fantasia' => $dados['nome_fantasia'] ?? null,
            'cpf' => $dados['cpf'] ?? null,
            'cnpj' => $dados['cnpj'] ?? null,
            'rg' => $dados['rg'] ?? null,
            'data_nascimento' => $dados['data_nascimento'] ?? null,
            'telefone_principal' => $dados['telefone_principal'] ?? null,
            'whatsapp' => $dados['whatsapp'] ?? null,
            'telefone_secundario' => $dados['telefone_secundario'] ?? null,
            'email_principal' => $dados['email_principal'] ?? null,
            'email_alternativo' => $dados['email_alternativo'] ?? null,
            'cep' => $dados['cep'] ?? null,
            'logradouro' => $dados['logradouro'] ?? null,
            'numero' => $dados['numero'] ?? null,
            'complemento' => $dados['complemento'] ?? null,
            'bairro' => $dados['bairro'] ?? null,
            'cidade' => $dados['cidade'] ?? null,
            'estado' => $dados['estado'] ?? null,
            'ponto_referencia' => $dados['ponto_referencia'] ?? null,
            'observacoes' => $dados['observacoes'] ?? null,
            'status' => $dados['status'] ?? Cliente::STATUS_ATIVO,
        ], fn ($v) => $v !== null);
    }

    private function camposProprietario(array $dados): array
    {
        return [
            'banco' => $dados['proprietario']['banco'] ?? null,
            'agencia' => $dados['proprietario']['agencia'] ?? null,
            'conta' => $dados['proprietario']['conta'] ?? null,
            'tipo_conta' => $dados['proprietario']['tipo_conta'] ?? null,
            'chave_pix' => $dados['proprietario']['chave_pix'] ?? null,
            'tipo_chave_pix' => $dados['proprietario']['tipo_chave_pix'] ?? null,
            'percentual_administracao' => $dados['proprietario']['percentual_administracao'] ?? null,
            'emite_nota_fiscal' => $dados['proprietario']['emite_nota_fiscal'] ?? false,
            'preferencia_recebimento' => $dados['proprietario']['preferencia_recebimento'] ?? null,
            'observacoes_repasse' => $dados['proprietario']['observacoes_repasse'] ?? null,
        ];
    }

    private function camposInquilino(array $dados): array
    {
        return [
            'profissao' => $dados['inquilino']['profissao'] ?? null,
            'renda_mensal' => $dados['inquilino']['renda_mensal'] ?? null,
            'local_trabalho' => $dados['inquilino']['local_trabalho'] ?? null,
            'telefone_comercial' => $dados['inquilino']['telefone_comercial'] ?? null,
            'contato_emergencia' => $dados['inquilino']['contato_emergencia'] ?? null,
            'observacoes_cadastrais' => $dados['inquilino']['observacoes_cadastrais'] ?? null,
            'restricoes' => $dados['inquilino']['restricoes'] ?? null,
        ];
    }
}

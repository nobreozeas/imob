<?php

namespace App\Http\Requests\Clientes;

use App\Models\Cliente;
use App\Models\ClientePapel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Cliente::class);
    }

    public function rules(): array
    {
        $tipoPessoa = $this->input('tipo_pessoa');

        return [
            'tipo_pessoa' => ['required', Rule::in([Cliente::TIPO_FISICA, Cliente::TIPO_JURIDICA])],
            'nome' => [Rule::requiredIf($tipoPessoa === Cliente::TIPO_FISICA), 'nullable', 'string', 'max:255'],
            'razao_social' => [Rule::requiredIf($tipoPessoa === Cliente::TIPO_JURIDICA), 'nullable', 'string', 'max:255'],
            'nome_fantasia' => ['nullable', 'string', 'max:255'],
            'cpf' => [Rule::requiredIf($tipoPessoa === Cliente::TIPO_FISICA), 'nullable', 'string', 'max:14', 'unique:clientes,cpf'],
            'cnpj' => [Rule::requiredIf($tipoPessoa === Cliente::TIPO_JURIDICA), 'nullable', 'string', 'max:18', 'unique:clientes,cnpj'],
            'rg' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['nullable', 'date'],
            'telefone_principal' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'telefone_secundario' => ['nullable', 'string', 'max:20'],
            'email_principal' => ['nullable', 'email', 'max:255'],
            'email_alternativo' => ['nullable', 'email', 'max:255'],
            'cep' => ['nullable', 'string', 'max:9'],
            'logradouro' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'size:2'],
            'ponto_referencia' => ['nullable', 'string', 'max:255'],
            'observacoes' => ['nullable', 'string'],
            'papeis' => ['required', 'array', 'min:1'],
            'papeis.*' => [Rule::in([ClientePapel::PROPRIETARIO, ClientePapel::INQUILINO])],
            'proprietario' => ['nullable', 'array'],
            'proprietario.banco' => ['nullable', 'string', 'max:100'],
            'proprietario.agencia' => ['nullable', 'string', 'max:20'],
            'proprietario.conta' => ['nullable', 'string', 'max:20'],
            'proprietario.tipo_conta' => ['nullable', 'string', 'max:50'],
            'proprietario.chave_pix' => ['nullable', 'string', 'max:255'],
            'proprietario.tipo_chave_pix' => ['nullable', Rule::in(['cpf', 'cnpj', 'email', 'telefone', 'aleatoria'])],
            'proprietario.percentual_administracao' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'proprietario.emite_nota_fiscal' => ['nullable', 'boolean'],
            'proprietario.preferencia_recebimento' => ['nullable', 'string', 'max:100'],
            'proprietario.observacoes_repasse' => ['nullable', 'string'],
            'inquilino' => ['nullable', 'array'],
            'inquilino.profissao' => ['nullable', 'string', 'max:100'],
            'inquilino.renda_mensal' => ['nullable', 'numeric', 'min:0'],
            'inquilino.local_trabalho' => ['nullable', 'string', 'max:255'],
            'inquilino.telefone_comercial' => ['nullable', 'string', 'max:20'],
            'inquilino.contato_emergencia' => ['nullable', 'string', 'max:255'],
            'inquilino.observacoes_cadastrais' => ['nullable', 'string'],
            'inquilino.restricoes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_pessoa.required' => 'O tipo de pessoa é obrigatório.',
            'nome.required' => 'O nome completo é obrigatório para pessoa física.',
            'razao_social.required' => 'A razão social é obrigatória para pessoa jurídica.',
            'cpf.required' => 'O CPF é obrigatório para pessoa física.',
            'cpf.unique' => 'Este CPF já está cadastrado no sistema.',
            'cnpj.required' => 'O CNPJ é obrigatório para pessoa jurídica.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado no sistema.',
            'papeis.required' => 'Selecione ao menos um papel para o cliente.',
            'papeis.min' => 'Selecione ao menos um papel para o cliente.',
            'email_principal.email' => 'O e-mail principal deve ser um endereço válido.',
            'email_alternativo.email' => 'O e-mail alternativo deve ser um endereço válido.',
        ];
    }
}

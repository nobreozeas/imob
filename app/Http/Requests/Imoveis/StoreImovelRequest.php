<?php

namespace App\Http\Requests\Imoveis;

use App\Models\Imovel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreImovelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo'          => ['required', 'string', 'max:255'],
            'codigo'          => ['nullable', 'string', 'max:50', 'unique:imoveis,codigo'],
            'tipo'            => ['required', Rule::in(['apartamento', 'casa', 'comercial', 'sala', 'galpao', 'terreno', 'rural', 'outro'])],
            'finalidade'      => ['required', Rule::in(['aluguel', 'venda', 'aluguel_venda'])],
            'status'          => ['required', Rule::in(['disponivel', 'reservado', 'alugado', 'em_manutencao', 'inativo'])],
            'proprietario_id' => ['required', 'uuid', 'exists:clientes,id'],
            'corretor_id'     => ['nullable', 'uuid', 'exists:users,id'],
            'descricao'       => ['nullable', 'string'],
            // Endereço
            'cep'             => ['nullable', 'string', 'max:9'],
            'logradouro'      => ['nullable', 'string', 'max:255'],
            'numero'          => ['nullable', 'string', 'max:20'],
            'complemento'     => ['nullable', 'string', 'max:100'],
            'bairro'          => ['nullable', 'string', 'max:100'],
            'cidade'          => ['nullable', 'string', 'max:100'],
            'estado'          => ['nullable', 'string', 'size:2'],
            'ponto_referencia' => ['nullable', 'string', 'max:255'],
            // Características
            'caracteristicas.area_total'            => ['nullable', 'numeric', 'min:0'],
            'caracteristicas.area_construida'       => ['nullable', 'numeric', 'min:0'],
            'caracteristicas.quartos'               => ['nullable', 'integer', 'min:0'],
            'caracteristicas.suites'                => ['nullable', 'integer', 'min:0'],
            'caracteristicas.banheiros'             => ['nullable', 'integer', 'min:0'],
            'caracteristicas.vagas_garagem'         => ['nullable', 'integer', 'min:0'],
            'caracteristicas.mobiliado'             => ['nullable', 'boolean'],
            'caracteristicas.aceita_pet'            => ['nullable', 'boolean'],
            'caracteristicas.possui_piscina'        => ['nullable', 'boolean'],
            'caracteristicas.possui_quintal'        => ['nullable', 'boolean'],
            'caracteristicas.possui_varanda'        => ['nullable', 'boolean'],
            'caracteristicas.outras_caracteristicas' => ['nullable', 'string'],
            // Dados comerciais
            'dados_comerciais.valor_aluguel'          => ['nullable', 'numeric', 'min:0'],
            'dados_comerciais.valor_venda'            => ['nullable', 'numeric', 'min:0'],
            'dados_comerciais.valor_condominio'       => ['nullable', 'numeric', 'min:0'],
            'dados_comerciais.valor_iptu'             => ['nullable', 'numeric', 'min:0'],
            'dados_comerciais.condominio_incluso'     => ['nullable', 'boolean'],
            'dados_comerciais.valor_caucao_sugerido'  => ['nullable', 'numeric', 'min:0'],
            'dados_comerciais.observacoes_comerciais' => ['nullable', 'string'],
            // Fotos e documentos
            'fotos_novas'         => ['nullable', 'array'],
            'fotos_novas.*'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'foto_principal_id'   => ['nullable', 'string'],
            'fotos_remover'       => ['nullable', 'array'],
            'fotos_remover.*'     => ['nullable', 'string'],
            'documentos_novos'    => ['nullable', 'array'],
            'documentos_novos.*'  => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,docx', 'max:20480'],
            'tipos_documentos'    => ['nullable', 'array'],
            'tipos_documentos.*'  => ['nullable', 'string', 'max:100'],
            'documentos_remover'  => ['nullable', 'array'],
            'documentos_remover.*' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'titulo'          => 'título',
            'codigo'          => 'código interno',
            'tipo'            => 'tipo do imóvel',
            'finalidade'      => 'finalidade',
            'status'          => 'status',
            'proprietario_id' => 'proprietário',
            'corretor_id'     => 'corretor responsável',
        ];
    }
}

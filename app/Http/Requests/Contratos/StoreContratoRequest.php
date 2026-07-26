<?php

namespace App\Http\Requests\Contratos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero'          => ['nullable', 'string', 'max:50', 'unique:contratos_locacao,numero'],
            'tipo_contrato'   => ['nullable', Rule::in(['residencial', 'comercial', 'temporada'])],
            'objetivo_contrato' => ['nullable', Rule::in(['aluguel', 'temporada'])],
            'acao'            => ['nullable', Rule::in(['rascunho', 'ativar'])],

            // Partes (obrigatórios)
            'imovel_id'       => ['required', 'uuid', 'exists:imoveis,id'],
            'proprietario_id' => ['required', 'uuid', 'exists:clientes,id'],
            'inquilino_id'    => ['required', 'uuid', 'exists:clientes,id'],
            'corretor_id'     => ['nullable', 'uuid', 'exists:users,id'],

            // Dados da locação (obrigatórios)
            'data_inicio'     => ['required', 'date'],
            'data_fim'        => ['nullable', 'date', 'after:data_inicio'],
            'dia_vencimento'  => ['required', 'integer', 'min:1', 'max:31'],
            'duracao_meses'   => ['nullable', 'integer', 'min:1'],

            // Valores (obrigatórios)
            'valor_aluguel'           => ['required', 'numeric', 'min:0'],
            'indice_reajuste'         => ['nullable', Rule::in(['igpm', 'ipca', 'inpc', 'fixo'])],
            'periodicidade_reajuste'  => ['nullable', 'integer', 'min:1'],
            'data_primeiro_reajuste'  => ['nullable', 'date'],

            // Repasse
            'tipo_taxa_administracao'  => ['nullable', Rule::in(['percentual', 'valor_fixo'])],
            'valor_taxa_administracao' => ['nullable', 'numeric', 'min:0'],
            'gerar_parcelas_automaticamente' => ['nullable', 'boolean'],
            'quantidade_parcelas'      => ['nullable', 'integer', 'min:1'],
            'dia_repasse'              => ['nullable', 'integer', 'min:1', 'max:31'],
            'forma_repasse'            => ['nullable', Rule::in(['pix', 'transferencia', 'dinheiro'])],
            'banco'                    => ['nullable', 'string', 'max:100'],
            'agencia'                  => ['nullable', 'string', 'max:20'],
            'conta'                    => ['nullable', 'string', 'max:20'],
            'tipo_conta'               => ['nullable', Rule::in(['corrente', 'poupanca'])],
            'pix_key'                  => ['nullable', 'string', 'max:150'],

            'observacoes' => ['nullable', 'string'],

            // Encargos
            'encargos'               => ['nullable', 'array'],
            'encargos.*.tipo_encargo' => ['required_with:encargos', Rule::in(['iptu', 'condominio', 'agua', 'energia', 'gas', 'internet', 'outros'])],
            'encargos.*.responsavel'  => ['required_with:encargos', Rule::in(['proprietario', 'inquilino', 'nao_se_aplica'])],
            'encargos.*.valor_estimado' => ['nullable', 'numeric', 'min:0'],
            'encargos.*.cobrar_junto_aluguel' => ['nullable', 'boolean'],
            'encargos.*.observacao'   => ['nullable', 'string', 'max:255'],

            // Caução
            'caucao'                          => ['nullable', 'array'],
            'caucao.possui_caucao'             => ['nullable', 'boolean'],
            'caucao.tipo_caucao'              => ['nullable', Rule::in(['dinheiro', 'imovel', 'fiador', 'seguro_fianca', 'deposito_bancario'])],
            'caucao.valor_caucao'             => ['nullable', 'numeric', 'min:0'],
            'caucao.data_recebimento_caucao'  => ['nullable', 'date'],

            // Multas
            'multas'                            => ['nullable', 'array'],
            'multas.possui_multa_atraso'        => ['nullable', 'boolean'],
            'multas.percentual_multa_atraso'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'multas.valor_juros_dia'            => ['nullable', 'numeric', 'min:0'],
            'multas.dias_tolerancia_atraso'      => ['nullable', 'integer', 'min:0'],
            'multas.possui_multa_rescisao'      => ['nullable', 'boolean'],
            'multas.percentual_multa_rescisao'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'multas.base_calculo_rescisao'      => ['nullable', Rule::in(['alugueis_restantes', 'valor_fixo'])],

            // Documentos
            'documentos_novos'    => ['nullable', 'array', 'max:10'],
            'documentos_novos.*'  => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,docx', 'max:20480'],
            'tipos_documentos'    => ['nullable', 'array'],
            'tipos_documentos.*'  => ['nullable', Rule::in(['contrato_assinado', 'laudo_vistoria', 'comprovante_caucao', 'outros'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'imovel_id'      => 'imóvel',
            'proprietario_id' => 'proprietário',
            'inquilino_id'   => 'inquilino',
            'data_inicio'    => 'data de início',
            'dia_vencimento' => 'dia de vencimento',
            'valor_aluguel'  => 'valor do aluguel',
        ];
    }
}

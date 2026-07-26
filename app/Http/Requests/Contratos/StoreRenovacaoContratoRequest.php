<?php

namespace App\Http\Requests\Contratos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRenovacaoContratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data_renovacao' => ['required', 'date'],
            'nova_data_inicio' => ['required', 'date'],
            'nova_data_fim' => ['nullable', 'date', 'after:nova_data_inicio'],
            'duracao_meses' => ['nullable', 'integer', 'min:1'],
            'valor_aluguel_novo' => ['nullable', 'numeric', 'min:0.01'],
            'tipo_taxa_administracao' => ['nullable', Rule::in(['percentual', 'valor_fixo'])],
            'valor_taxa_administracao' => ['nullable', 'numeric', 'min:0'],
            'manter_encargos' => ['nullable', 'boolean'],
            'manter_regras_multa' => ['nullable', 'boolean'],
            'gerar_novas_parcelas' => ['nullable', 'boolean'],
            'caucao_acao' => ['nullable', Rule::in(['manter', 'devolver', 'complementar'])],
            'observacoes' => ['nullable', 'string'],
        ];
    }
}

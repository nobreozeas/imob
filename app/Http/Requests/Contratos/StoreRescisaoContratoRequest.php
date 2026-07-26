<?php

namespace App\Http\Requests\Contratos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRescisaoContratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data_rescisao' => ['required', 'date'],
            'motivo' => ['required', 'string'],
            'solicitado_por' => ['required', Rule::in(['locatario', 'locador', 'imobiliaria', 'acordo'])],
            'destino_imovel' => ['required', Rule::in(['disponivel', 'inativo'])],
            'acao_parcelas_futuras' => ['required', Rule::in(['cancelar_parcelas_futuras', 'manter_parcelas_futuras'])],
            'valor_desconto' => ['nullable', 'numeric', 'min:0'],
            'valor_caucao_abatida' => ['nullable', 'numeric', 'min:0'],
            'valor_caucao_retida' => ['nullable', 'numeric', 'min:0'],
            'valor_caucao_devolvida' => ['nullable', 'numeric', 'min:0'],
            'motivo_retencao_caucao' => ['nullable', 'string'],
            'referencia_debito_caucao' => ['nullable', 'string'],
            'observacoes' => ['nullable', 'string'],
        ];
    }
}

<?php

namespace App\Http\Requests\Contratos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMovimentacaoCaucaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_movimentacao' => ['required', Rule::in(['recebimento', 'devolucao', 'abatimento', 'retencao_parcial', 'retencao_integral', 'ajuste'])],
            'valor' => ['required', 'numeric', 'min:0.01'],
            'data_movimentacao' => ['required', 'date'],
            'forma_movimentacao' => ['nullable', Rule::in(['pix', 'dinheiro', 'cartao_credito', 'cartao_debito', 'transferencia', 'boleto', 'outro'])],
            'descricao' => ['nullable', 'string'],
            'referencia_debito' => ['nullable', 'string'],
        ];
    }
}

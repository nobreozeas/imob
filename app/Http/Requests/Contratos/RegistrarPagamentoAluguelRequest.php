<?php

namespace App\Http\Requests\Contratos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrarPagamentoAluguelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data_pagamento' => ['required', 'date'],
            'forma_pagamento' => ['required', Rule::in(['pix', 'dinheiro', 'cartao_credito', 'cartao_debito', 'transferencia', 'boleto', 'outro'])],
            'valor_pago' => ['required', 'numeric', 'min:0.01'],
            'valor_desconto' => ['nullable', 'numeric', 'min:0'],
            'observacoes' => ['nullable', 'string'],
        ];
    }
}

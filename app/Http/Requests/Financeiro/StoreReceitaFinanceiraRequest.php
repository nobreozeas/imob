<?php

namespace App\Http\Requests\Financeiro;

use App\Models\CategoriaFinanceira;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReceitaFinanceiraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categoria_financeira_id' => [
                'required', 'uuid',
                Rule::exists('categorias_financeiras', 'id')->where('ativa', true)->where('tipo', CategoriaFinanceira::TIPO_ENTRADA),
            ],
            'cliente_id' => ['nullable', 'uuid', 'exists:clientes,id'],
            'contrato_id' => ['nullable', 'uuid', 'exists:contratos_locacao,id'],
            'imovel_id' => ['nullable', 'uuid', 'exists:imoveis,id'],
            'descricao' => ['required', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'gt:0'],
            'data_vencimento' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['pendente', 'pago'])],
            'data_pagamento' => ['required_if:status,pago', 'nullable', 'date'],
            'forma_pagamento' => ['required_if:status,pago', 'nullable', Rule::in(['pix', 'dinheiro', 'cartao_credito', 'cartao_debito', 'transferencia', 'boleto', 'cheque', 'outro'])],
            'observacoes' => ['nullable', 'string'],
        ];
    }
}

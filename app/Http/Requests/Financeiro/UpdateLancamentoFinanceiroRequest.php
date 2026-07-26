<?php

namespace App\Http\Requests\Financeiro;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLancamentoFinanceiroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categoria_financeira_id' => ['required', 'uuid', 'exists:categorias_financeiras,id'],
            'cliente_id' => ['nullable', 'uuid', 'exists:clientes,id'],
            'contrato_id' => ['nullable', 'uuid', 'exists:contratos_locacao,id'],
            'imovel_id' => ['nullable', 'uuid', 'exists:imoveis,id'],
            'descricao' => ['required', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'gt:0'],
            'data_vencimento' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string'],
        ];
    }
}

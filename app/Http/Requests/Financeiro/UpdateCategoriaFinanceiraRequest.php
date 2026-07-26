<?php

namespace App\Http\Requests\Financeiro;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoriaFinanceiraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoria = $this->route('categoria');

        return [
            'nome' => ['required', 'string', 'max:255'],
            'tipo' => ['required', Rule::in(['entrada', 'saida'])],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categorias_financeiras', 'slug')->ignore($categoria)],
            'descricao' => ['nullable', 'string'],
            'ativa' => ['nullable', 'boolean'],
        ];
    }
}

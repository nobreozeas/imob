<?php

namespace App\Http\Requests\Financeiro;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoriaFinanceiraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'tipo' => ['required', Rule::in(['entrada', 'saida'])],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categorias_financeiras,slug'],
            'descricao' => ['nullable', 'string'],
            'ativa' => ['nullable', 'boolean'],
        ];
    }
}

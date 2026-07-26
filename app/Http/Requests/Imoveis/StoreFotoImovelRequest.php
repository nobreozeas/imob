<?php

namespace App\Http\Requests\Imoveis;

use Illuminate\Foundation\Http\FormRequest;

class StoreFotoImovelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fotos'   => ['required', 'array', 'min:1'],
            'fotos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}

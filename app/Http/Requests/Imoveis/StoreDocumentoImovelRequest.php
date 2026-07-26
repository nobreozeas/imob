<?php

namespace App\Http\Requests\Imoveis;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoImovelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'documento' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,docx', 'max:20480'],
            'tipo'      => ['nullable', 'string', 'max:100'],
        ];
    }
}

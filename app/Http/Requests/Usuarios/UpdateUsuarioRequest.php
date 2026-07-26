<?php

namespace App\Http\Requests\Usuarios;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('usuario'));
    }

    public function rules(): array
    {
        return [
            'name'   => ['required', 'string', 'min:3', 'max:255'],
            'role'   => ['required', 'string', Rule::exists('roles', 'name')->where('guard_name', 'web')],
            'status' => ['required', Rule::in(['ativo', 'inativo'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'   => 'nome',
            'role'   => 'perfil',
            'status' => 'status',
        ];
    }
}

<?php

namespace App\Http\Requests\Usuarios;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\User::class);
    }

    public function rules(): array
    {
        return [
            'name'   => ['required', 'string', 'min:3', 'max:255'],
            'email'  => ['required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'role'   => ['required', 'string', Rule::exists('roles', 'name')->where('guard_name', 'web')],
            'status' => ['required', Rule::in(['ativo', 'inativo'])],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'   => 'nome',
            'email'  => 'e-mail',
            'role'   => 'perfil',
            'status' => 'status',
        ];
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AlteracaoSenhaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class AlteracaoSenhaController extends Controller
{
    public function update(AlteracaoSenhaRequest $request): RedirectResponse
    {
        $user = auth()->user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors([
                'current_password' => 'A senha atual informada está incorreta.',
            ]);
        }

        $user->update(['password' => $request->input('password')]);

        return back()->with('status', 'Senha alterada com sucesso.');
    }
}

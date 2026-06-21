<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustChangePassword
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->deve_alterar_senha) {
            return redirect()->route('primeiro-acesso');
        }

        return $next($request);
    }
}

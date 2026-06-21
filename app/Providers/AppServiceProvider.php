<?php

namespace App\Providers;

use App\Models\Cliente;
use App\Models\ContratoLocacao;
use App\Models\Imovel;
use App\Policies\ClientePolicy;
use App\Policies\ContratoLocacaoPolicy;
use App\Policies\ImovelPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends AuthServiceProvider
{
    protected $policies = [
        Cliente::class        => ClientePolicy::class,
        Imovel::class         => ImovelPolicy::class,
        ContratoLocacao::class => ContratoLocacaoPolicy::class,
    ];

    public function register(): void {}

    public function boot(): void
    {
        $this->registerPolicies();
        URL::forceRootUrl(config('app.url'));
    }
}

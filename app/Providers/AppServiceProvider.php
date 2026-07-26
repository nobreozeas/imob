<?php

namespace App\Providers;

use App\Models\CategoriaFinanceira;
use App\Models\Cliente;
use App\Models\ContratoLocacao;
use App\Models\Imovel;
use App\Models\LancamentoFinanceiro;
use App\Models\RepasseProprietario;
use App\Models\Role;
use App\Models\User;
use App\Policies\CategoriaFinanceiraPolicy;
use App\Policies\ClientePolicy;
use App\Policies\ContratoLocacaoPolicy;
use App\Policies\ImovelPolicy;
use App\Policies\LancamentoFinanceiroPolicy;
use App\Policies\PerfilPolicy;
use App\Policies\RelatorioFinanceiroPolicy;
use App\Policies\RepasseProprietarioPolicy;
use App\Policies\UsuarioPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends AuthServiceProvider
{
    protected $policies = [
        Cliente::class        => ClientePolicy::class,
        Imovel::class         => ImovelPolicy::class,
        ContratoLocacao::class => ContratoLocacaoPolicy::class,
        RepasseProprietario::class => RepasseProprietarioPolicy::class,
        User::class           => UsuarioPolicy::class,
        Role::class           => PerfilPolicy::class,
        LancamentoFinanceiro::class => LancamentoFinanceiroPolicy::class,
        CategoriaFinanceira::class => CategoriaFinanceiraPolicy::class,
    ];

    public function register(): void {}

    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('ver-relatorios-financeiros', [RelatorioFinanceiroPolicy::class, 'view']);
        URL::forceRootUrl(config('app.url'));
    }
}

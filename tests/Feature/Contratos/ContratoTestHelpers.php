<?php

namespace Tests\Feature\Contratos;

use App\Models\Cliente;
use App\Models\ClientePapel;
use App\Models\ContratoLocacao;
use App\Models\Imovel;
use App\Models\Permission;
use App\Models\User;
use Database\Seeders\CategoriaFinanceiraSeeder;

trait ContratoTestHelpers
{
    protected function setUp(): void
    {
        parent::setUp();

        (new CategoriaFinanceiraSeeder())->run();
    }

    private function usuarioComPermissoes(array $permissoes): User
    {
        $user = User::factory()->create(['deve_alterar_senha' => false]);

        foreach ($permissoes as $permissao) {
            Permission::firstOrCreate(['name' => $permissao, 'guard_name' => 'web']);
        }

        $user->givePermissionTo($permissoes);

        return $user;
    }

    private function criarCliente(string $papel): Cliente
    {
        $cliente = Cliente::create([
            'tipo_pessoa' => 'fisica',
            'nome' => ucfirst($papel) . ' Teste',
            'cpf' => (string) random_int(10000000000, 99999999999),
            'status' => 'ativo',
        ]);

        ClientePapel::create(['cliente_id' => $cliente->id, 'papel' => $papel]);

        return $cliente;
    }

    private function criarImovel(?Cliente $proprietario = null, string $status = Imovel::STATUS_ALUGADO): Imovel
    {
        $proprietario ??= $this->criarCliente('proprietario');
        $criador = User::factory()->create(['deve_alterar_senha' => false]);

        return Imovel::create([
            'codigo' => 'IMO-' . uniqid(),
            'titulo' => 'Casa Teste',
            'tipo' => 'casa',
            'finalidade' => 'aluguel',
            'status' => $status,
            'proprietario_id' => $proprietario->id,
            'criado_por' => $criador->id,
        ]);
    }

    private function criarContrato(array $overrides = []): ContratoLocacao
    {
        $proprietario = $overrides['proprietario'] ?? $this->criarCliente('proprietario');
        $inquilino = $overrides['inquilino'] ?? $this->criarCliente('inquilino');
        $imovel = $overrides['imovel'] ?? $this->criarImovel($proprietario);
        $criador = $overrides['criador'] ?? User::factory()->create(['deve_alterar_senha' => false]);

        $contrato = ContratoLocacao::create([
            'numero' => 'CTR-' . uniqid(),
            'status' => $overrides['status'] ?? ContratoLocacao::STATUS_ATIVO,
            'tipo_contrato' => 'residencial',
            'objetivo_contrato' => 'aluguel',
            'imovel_id' => $imovel->id,
            'proprietario_id' => $proprietario->id,
            'inquilino_id' => $inquilino->id,
            'criado_por' => $criador->id,
            'data_inicio' => $overrides['data_inicio'] ?? now()->subMonths(6),
            'data_fim' => array_key_exists('data_fim', $overrides) ? $overrides['data_fim'] : now()->addMonths(6),
            'dia_vencimento' => $overrides['dia_vencimento'] ?? 5,
            'valor_aluguel' => $overrides['valor_aluguel'] ?? 1500,
            'tipo_taxa_administracao' => $overrides['tipo_taxa_administracao'] ?? ContratoLocacao::TAXA_PERCENTUAL,
            'valor_taxa_administracao' => $overrides['valor_taxa_administracao'] ?? 10,
            'gerar_parcelas_automaticamente' => $overrides['gerar_parcelas_automaticamente'] ?? false,
        ]);

        $contrato->caucao()->create($overrides['caucao'] ?? ['possui_caucao' => false]);
        $contrato->multas()->create($overrides['multas'] ?? ['possui_multa_atraso' => false, 'possui_multa_rescisao' => false]);

        return $contrato->fresh(['caucao', 'multas']);
    }
}

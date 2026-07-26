<?php

namespace Database\Seeders;

use App\Models\ClientePapel;
use App\Models\User;
use App\Services\Clientes\ClienteService;
use App\Services\Imoveis\ImovelService;
use Illuminate\Database\Seeder;

class ImovelClienteDemoSeeder extends Seeder
{
    public function run(): void
    {
        $criador = User::where('email', 'admin@imobgestor.com.br')->first() ?? User::first();

        $proprietario = app(ClienteService::class)->criar([
            'tipo_pessoa' => 'fisica',
            'nome' => 'João da Silva',
            'cpf' => '11144477735',
            'telefone_principal' => '(11) 98888-1111',
            'whatsapp' => '(11) 98888-1111',
            'email_principal' => 'joao.proprietario@example.com',
            'cep' => '01310-100',
            'logradouro' => 'Avenida Paulista',
            'numero' => '1000',
            'bairro' => 'Bela Vista',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'status' => 'ativo',
            'papeis' => [ClientePapel::PROPRIETARIO],
            'proprietario' => [
                'banco' => 'Banco do Brasil',
                'agencia' => '1234-5',
                'conta' => '98765-4',
                'tipo_conta' => 'corrente',
                'chave_pix' => 'joao.proprietario@example.com',
                'tipo_chave_pix' => 'email',
                'percentual_administracao' => 10,
                'emite_nota_fiscal' => false,
                'preferencia_recebimento' => 'pix',
            ],
        ]);

        $inquilino = app(ClienteService::class)->criar([
            'tipo_pessoa' => 'fisica',
            'nome' => 'Maria Oliveira',
            'cpf' => '52998224725',
            'telefone_principal' => '(11) 97777-2222',
            'whatsapp' => '(11) 97777-2222',
            'email_principal' => 'maria.inquilina@example.com',
            'cep' => '04538-133',
            'logradouro' => 'Rua Funchal',
            'numero' => '200',
            'bairro' => 'Vila Olímpia',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'status' => 'ativo',
            'papeis' => [ClientePapel::INQUILINO],
            'inquilino' => [
                'profissao' => 'Analista de Sistemas',
                'renda_mensal' => 6500,
                'local_trabalho' => 'Tech Solutions Ltda',
                'telefone_comercial' => '(11) 3333-4444',
                'contato_emergencia' => 'Pedro Oliveira - (11) 96666-5555',
            ],
        ]);

        app(ImovelService::class)->criar([
            'titulo' => 'Apartamento 2 quartos na Vila Olímpia',
            'tipo' => 'apartamento',
            'finalidade' => 'aluguel',
            'proprietario_id' => $proprietario->id,
            'descricao' => 'Apartamento reformado, próximo ao metrô, condomínio com portaria 24h.',
            'cep' => '04538-133',
            'logradouro' => 'Rua Funchal',
            'numero' => '150',
            'bairro' => 'Vila Olímpia',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'criado_por' => $criador->id,
            'caracteristicas' => [
                'area_total' => 65,
                'area_construida' => 60,
                'quartos' => 2,
                'suites' => 1,
                'banheiros' => 2,
                'vagas_garagem' => 1,
                'mobiliado' => false,
                'aceita_pet' => true,
            ],
            'dados_comerciais' => [
                'valor_aluguel' => 3200,
                'valor_condominio' => 650,
                'valor_iptu' => 120,
                'condominio_incluso' => false,
                'valor_caucao_sugerido' => 3200,
            ],
        ]);

        $this->command->info('Seeder de demonstração: 1 proprietário, 1 inquilino e 1 imóvel criados.');
    }
}

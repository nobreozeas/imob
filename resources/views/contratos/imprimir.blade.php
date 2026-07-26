<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Contrato {{ $contrato->numero }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Georgia, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #111;
            max-width: 210mm;
            margin: 0 auto;
            padding: 20mm 18mm;
        }
        h1 {
            text-align: center;
            font-size: 15pt;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .numero-contrato {
            text-align: center;
            font-size: 10pt;
            color: #555;
            margin-bottom: 24px;
        }
        h2 {
            font-size: 12pt;
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 6px;
        }
        p { margin: 0 0 8px; text-align: justify; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        table th, table td {
            border: 1px solid #999;
            padding: 4px 8px;
            font-size: 11pt;
            text-align: left;
        }
        .assinaturas {
            margin-top: 60px;
        }
        .linha-assinatura {
            margin-top: 50px;
            text-align: center;
        }
        .linha-assinatura .traco {
            border-top: 1px solid #111;
            width: 70%;
            margin: 0 auto 4px;
        }
        .btn-imprimir {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 16px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-family: Arial, sans-serif;
            font-size: 13px;
            cursor: pointer;
        }
        @media print {
            .btn-imprimir { display: none; }
            body { padding: 0; }
        }
        @media screen {
            body { background: #f3f4f6; }
        }
    </style>
</head>
<body>
    @php
        $nomeCliente = function ($cliente) {
            if (!$cliente) return '—';
            return $cliente->tipo_pessoa === 'juridica' ? ($cliente->razao_social ?? '—') : ($cliente->nome ?? '—');
        };
        $documentoCliente = function ($cliente) {
            if (!$cliente) return '—';
            return $cliente->tipo_pessoa === 'juridica' ? ($cliente->cnpj ?? '—') : ($cliente->cpf ?? '—');
        };
        $endereco = function ($entidade) {
            if (!$entidade || !$entidade->logradouro) return null;
            $partes = array_filter([
                $entidade->logradouro . ($entidade->numero ? ', ' . $entidade->numero : ''),
                $entidade->complemento,
                $entidade->bairro,
                ($entidade->cidade ? $entidade->cidade . ($entidade->estado ? '/' . $entidade->estado : '') : null),
                ($entidade->cep ? 'CEP ' . $entidade->cep : null),
            ]);
            return implode(', ', $partes);
        };
        $moeda = function ($valor) {
            if ($valor === null) return '—';
            return 'R$ ' . number_format((float) $valor, 2, ',', '.');
        };
        $data = function ($valor) {
            if (!$valor) return null;
            return \Carbon\Carbon::parse($valor)->format('d/m/Y');
        };
        $labelsIndice = ['igpm' => 'IGP-M', 'ipca' => 'IPCA', 'inpc' => 'INPC', 'fixo' => 'reajuste fixo'];
        $labelsEncargo = ['iptu' => 'IPTU', 'condominio' => 'Condomínio', 'agua' => 'Água', 'energia' => 'Energia', 'gas' => 'Gás', 'internet' => 'Internet', 'outros' => 'Outros'];
        $labelsResponsavel = ['proprietario' => 'Locador', 'inquilino' => 'Locatário', 'nao_se_aplica' => 'Não se aplica'];
        $labelsCaucao = ['dinheiro' => 'Depósito em dinheiro', 'imovel' => 'Imóvel', 'fiador' => 'Fiador', 'seguro_fianca' => 'Seguro fiança', 'deposito_bancario' => 'Depósito bancário'];
    @endphp

    <button class="btn-imprimir" onclick="window.print()">Imprimir</button>

    <h1>Contrato de Locação Residencial</h1>
    <p class="numero-contrato">Contrato nº {{ $contrato->numero }}</p>

    <h2>1. Das Partes</h2>
    <p>
        <strong>LOCADOR(A):</strong> {{ $nomeCliente($contrato->proprietario) }},
        {{ $contrato->proprietario?->tipo_pessoa === 'juridica' ? 'CNPJ' : 'CPF' }} nº {{ $documentoCliente($contrato->proprietario) }}
        @if($endereco($contrato->proprietario)), residente/sede em {{ $endereco($contrato->proprietario) }}@endif.
    </p>
    <p>
        <strong>LOCATÁRIO(A):</strong> {{ $nomeCliente($contrato->inquilino) }},
        {{ $contrato->inquilino?->tipo_pessoa === 'juridica' ? 'CNPJ' : 'CPF' }} nº {{ $documentoCliente($contrato->inquilino) }}
        @if($endereco($contrato->inquilino)), residente/sede em {{ $endereco($contrato->inquilino) }}@endif.
    </p>
    @if($contrato->corretor)
        <p><strong>CORRETOR(A) RESPONSÁVEL:</strong> {{ $contrato->corretor->name }}.</p>
    @endif

    <h2>2. Do Objeto</h2>
    <p>
        O presente contrato tem por objeto a locação do imóvel situado em
        {{ $endereco($contrato->imovel) ?? $contrato->imovel?->titulo ?? '—' }},
        de código interno {{ $contrato->imovel?->codigo }}.
    </p>

    <h2>3. Do Prazo</h2>
    <p>
        A locação vigorará a partir de {{ $data($contrato->data_inicio) }}
        @if($contrato->data_fim)
            até {{ $data($contrato->data_fim) }}
        @endif
        @if($contrato->duracao_meses)
            , totalizando {{ $contrato->duracao_meses }} meses.
        @else
            .
        @endif
    </p>

    <h2>4. Do Valor e Reajuste</h2>
    <p>
        O valor mensal do aluguel é de {{ $moeda($contrato->valor_aluguel) }}, com vencimento todo dia
        {{ $contrato->dia_vencimento }} de cada mês.
        @if($contrato->indice_reajuste)
            O valor será reajustado a cada {{ $contrato->periodicidade_reajuste }} meses, com base no índice {{ $labelsIndice[$contrato->indice_reajuste] ?? $contrato->indice_reajuste }}.
        @endif
    </p>

    @if($contrato->encargos && count($contrato->encargos))
        <h2>5. Dos Encargos</h2>
        <table>
            <thead>
                <tr><th>Encargo</th><th>Responsável</th><th>Observação</th></tr>
            </thead>
            <tbody>
                @foreach($contrato->encargos as $encargo)
                    <tr>
                        <td>{{ $labelsEncargo[$encargo->tipo_encargo] ?? $encargo->tipo_encargo }}</td>
                        <td>{{ $labelsResponsavel[$encargo->responsavel] ?? $encargo->responsavel }}</td>
                        <td>{{ $encargo->observacao ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($contrato->multas && ($contrato->multas->possui_multa_atraso || $contrato->multas->possui_multa_rescisao))
        <h2>6. Das Multas</h2>
        @if($contrato->multas->possui_multa_atraso)
            <p>
                Em caso de atraso no pagamento do aluguel, incidirá multa de {{ $contrato->multas->percentual_multa_atraso }}%
                sobre o valor devido, acrescida de juros de {{ $contrato->multas->valor_juros_dia }}% ao dia,
                observada tolerância de {{ $contrato->multas->dias_tolerancia_atraso ?? 0 }} dia(s).
            </p>
        @endif
        @if($contrato->multas->possui_multa_rescisao)
            <p>
                Em caso de rescisão antecipada e imotivada, aplicar-se-á multa de {{ $contrato->multas->percentual_multa_rescisao }}%,
                calculada sobre {{ $contrato->multas->base_calculo_rescisao === 'alugueis_restantes' ? 'os aluguéis restantes do contrato' : 'valor fixo estabelecido' }}.
            </p>
        @endif
    @endif

    @if($contrato->caucao && $contrato->caucao->possui_caucao)
        <h2>7. Da Garantia</h2>
        <p>
            A título de garantia locatícia, foi constituída {{ $labelsCaucao[$contrato->caucao->tipo_caucao] ?? 'garantia' }},
            no valor de {{ $moeda($contrato->caucao->valor_caucao) }}.
        </p>
    @endif

    <p>
        E, por estarem justas e contratadas, as partes assinam o presente instrumento em duas vias de igual teor.
    </p>

    <p style="text-align: right; margin-top: 24px;">
        {{ $contrato->imovel?->cidade ?? '—' }}, {{ now()->translatedFormat('d \d\e F \d\e Y') }}.
    </p>

    <div class="assinaturas">
        <div class="linha-assinatura">
            <div class="traco"></div>
            <p>{{ $nomeCliente($contrato->proprietario) }} — Locador(a)</p>
        </div>
        <div class="linha-assinatura">
            <div class="traco"></div>
            <p>{{ $nomeCliente($contrato->inquilino) }} — Locatário(a)</p>
        </div>
        @if($contrato->corretor)
            <div class="linha-assinatura">
                <div class="traco"></div>
                <p>{{ $contrato->corretor->name }} — Corretor(a)</p>
            </div>
        @endif
        <div class="linha-assinatura">
            <div class="traco"></div>
            <p>Testemunha</p>
        </div>
        <div class="linha-assinatura">
            <div class="traco"></div>
            <p>Testemunha</p>
        </div>
    </div>
</body>
</html>

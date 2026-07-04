<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Borderò Lavoro #{{ $work->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 24px;
            font-size: 12px;
            line-height: 1.5;
            color: #2b2b2b;
        }
        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #1f4e79;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header-left {
            display: table-cell;
            width: 40%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 60%;
            vertical-align: top;
            text-align: right;
        }
        .logo {
            max-width: 160px;
            height: auto;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1f4e79;
        }
        .company-details {
            font-size: 11px;
            margin-top: 4px;
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1f4e79;
            margin: 14px 0 10px;
            text-transform: uppercase;
        }
        .info-box {
            background: #f7f9fc;
            border: 1px solid #e3e7ef;
            padding: 10px 12px;
            margin-bottom: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        th, td {
            border: 1px solid #d9dfe9;
            padding: 8px;
            vertical-align: top;
        }
        th {
            background: #1f4e79;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .badge-success { background: #d4edda; color: #155724; padding: 2px 6px; border-radius: 3px; }
        .badge-warning { background: #fff3cd; color: #856404; padding: 2px 6px; border-radius: 3px; }
        .badge-danger  { background: #f8d7da; color: #721c24; padding: 2px 6px; border-radius: 3px; }
        .footer {
            margin-top: 18px;
            font-size: 10px;
            color: #666;
            text-align: center;
            border-top: 1px solid #e3e7ef;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            @if(file_exists(public_path('img/logo/logo.jpg')))
                <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('img/logo/logo.jpg'))) }}" alt="Logo" class="logo">
            @endif
        </div>
        <div class="header-right">
            <div class="company-name">TEP SRL</div>
            <div class="company-details">
                P.IVA: 16433631005<br>
                Via Conca d'Oro 327 00141 Roma <br>
                Tel: 342 896 4506<br>
                Email: tepsrl@smaltimentieservizi.it
            </div>
        </div>
    </div>

    @php
        $customerName = $work->customer
            ? ($work->customer->customer_type == 'fisica' ? $work->customer->full_name : $work->customer->ragione_sociale)
            : 'N/D';
        $dataLavoro = $work->data_esecuzione ? \Carbon\Carbon::parse($work->data_esecuzione)->format('d/m/Y') : 'N/D';
    @endphp

    <div class="title">Borderò – {{ $customerName }} – {{ $dataLavoro }}</div>

    <div class="info-box">
        <strong>Lavoro:</strong> #{{ $work->id }} — {{ $work->tipo_lavoro }}<br>
        <strong>Cliente:</strong> {{ $customerName }}<br>
        <strong>Data:</strong> {{ $dataLavoro }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Nome Pezzo</th>
                <th class="text-right">Quantità</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bordero->pezzi as $riga)
                <tr>
                    <td>{{ $riga->nome_pezzo }}</td>
                    <td class="text-right">{{ $riga->quantita }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Nessun pezzo registrato.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @php
        $badgeClass = match($bordero->status) {
            'Completo' => 'badge-success',
            'Non realizzabile' => 'badge-danger',
            default => 'badge-warning',
        };
    @endphp
    <p><strong>Status:</strong> <span class="{{ $badgeClass }}">{{ $bordero->status }}</span></p>

    <p><strong>Note Tecniche:</strong></p>
    <div class="info-box">{{ $bordero->note_tecniche ?: '—' }}</div>

    <div class="footer">
        Documento generato il {{ now()->format('d/m/Y H:i') }} - {{ env('NOME_AZIENDA', config('app.name')) }}.
    </div>
</body>
</html>

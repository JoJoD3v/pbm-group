<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ricevuta {{ $ricevuta->numero_ricevuta }}</title>
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
        .section {
            margin-top: 14px;
        }
        .section-title {
            font-weight: bold;
            color: #1f4e79;
            margin-bottom: 6px;
        }
        .meta {
            display: table;
            width: 100%;
            margin-top: 6px;
        }
        .meta-left,
        .meta-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .meta-right {
            text-align: right;
        }
        .box {
            background: #f7f9fc;
            border: 1px solid #e3e7ef;
            padding: 10px 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
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
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            color: #ffffff;
            background: #6c757d;
        }
        .badge-success {
            background: #28a745;
        }
        .badge-danger {
            background: #dc3545;
        }
        .total {
            font-size: 14px;
            font-weight: bold;
            color: #1f4e79;
        }
        .signature {
            margin-top: 22px;
            text-align: center;
        }
        .signature img {
            max-width: 260px;
            max-height: 120px;
            border: 1px solid #d9dfe9;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 220px;
            margin: 18px auto 6px;
        }
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
            <div class="company-name">{{ env('NOME_AZIENDA', config('app.name')) }}</div>
            <div class="company-details">
                {{ env('INDIRIZZO_AZIENDA', '-') }}<br>
                P.IVA: {{ env('PARTITA_IVA_AZIENDA', '-') }}<br>
                Tel: {{ env('TELEFONO_AZIENDA', '-') }}<br>
                Email: {{ env('EMAIL_AZIENDA', '-') }}
            </div>
        </div>
    </div>

    <div class="title">Ricevuta Lavoro N. {{ $ricevuta->numero_ricevuta }}</div>

    <div class="meta">
        <div class="meta-left">
            <strong>Data emissione:</strong> {{ $ricevuta->created_at->format('d/m/Y H:i') }}<br>
            <strong>Ricevente:</strong> {{ $ricevuta->nome_ricevente }}
        </div>
        <div class="meta-right">
            <strong>Fattura richiesta:</strong> {{ $ricevuta->fattura ? 'Si' : 'No' }}<br>
            <strong>Riserva controlli:</strong> {{ $ricevuta->riserva_controlli ? 'Si' : 'No' }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Dati Cliente</div>
        <div class="box">
            <strong>{{ $customer->customer_type == 'fisica' ? $customer->full_name : $customer->ragione_sociale }}</strong><br>
            @if($customer->address)
                Indirizzo: {{ $customer->address }}<br>
            @endif
            @if($customer->phone)
                Telefono: {{ $customer->phone }}<br>
            @endif
            @if($customer->email)
                Email: {{ $customer->email }}<br>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Dettagli Lavoro</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 26%;">Lavoro</th>
                    <th style="width: 18%;">Data/Ora</th>
                    <th style="width: 28%;">Partenza</th>
                    <th style="width: 28%;">Destinazione</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $work->tipo_lavoro }}</strong><br>
                        ID: {{ $work->id }}<br>
                        @if($work->materiale)
                            Materiale: {{ $work->materiale }}<br>
                        @endif
                        @if($work->codice_eer)
                            Codice EER: {{ $work->codice_eer }}<br>
                        @endif
                        @if($work->modalita_pagamento)
                            Pagamento: {{ $work->modalita_pagamento }}
                        @endif
                    </td>
                    <td>
                        {{ $work->data_esecuzione ? \Carbon\Carbon::parse($work->data_esecuzione)->format('d/m/Y H:i') : 'Non specificata' }}
                    </td>
                    <td>
                        {{ $work->nome_partenza ?? 'N/D' }}<br>
                        <small>{{ $work->indirizzo_partenza ?? 'N/D' }}</small>
                    </td>
                    <td>
                        {{ $work->nome_destinazione ?? 'N/D' }}<br>
                        <small>{{ $work->indirizzo_destinazione ?? 'N/D' }}</small>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Pagamento</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40%;">Stato pagamento</th>
                    <th style="width: 30%;">Importo dovuto</th>
                    <th style="width: 30%;">Importo pagato</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <span class="badge {{ $ricevuta->pagamento_effettuato ? 'badge-success' : 'badge-danger' }}">
                            {{ $ricevuta->pagamento_effettuato ? 'PAGATO' : 'NON PAGATO' }}
                        </span>
                    </td>
                    <td class="text-right">
                        @if($work->costo_lavoro)
                            € {{ number_format($work->costo_lavoro, 2, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($ricevuta->pagamento_effettuato && $ricevuta->somma_pagamento)
                            € {{ number_format($ricevuta->somma_pagamento, 2, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
        @if($work->costo_lavoro)
            <div class="text-right" style="margin-top: 6px;">
                <span class="total">TOTALE: € {{ number_format($work->costo_lavoro, 2, ',', '.') }}</span>
            </div>
        @endif
    </div>

    <div class="signature">
        <div class="section-title">Firma del ricevente</div>
        @if(\Illuminate\Support\Str::startsWith($ricevuta->firma_base64, 'data:image'))
            <img src="{{ $ricevuta->firma_base64 }}" alt="Firma">
        @else
            <div class="signature-line"></div>
            <div>{{ $ricevuta->nome_ricevente }}</div>
        @endif
    </div>

    <div class="footer">
        Documento generato il {{ now()->format('d/m/Y H:i') }} - {{ env('NOME_AZIENDA', config('app.name')) }}.
    </div>
</body>
</html>

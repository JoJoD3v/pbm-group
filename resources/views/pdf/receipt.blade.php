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
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 20px;
        }
        
        .logo-section {
            display: table-cell;
            width: 40%;
            vertical-align: top;
        }
        
        .logo {
            max-width: 150px;
            height: auto;
        }
        
        .company-section {
            display: table-cell;
            width: 60%;
            vertical-align: top;
            text-align: right;
            padding-left: 20px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #0056b3;
            margin-bottom: 10px;
        }
        
        .company-details {
            font-size: 11px;
            line-height: 1.5;
        }
        
        .receipt-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #0056b3;
            margin: 30px 0 20px 0;
            text-transform: uppercase;
        }
        
        .receipt-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .receipt-info-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .receipt-info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        
        .customer-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #0056b3;
            margin-bottom: 20px;
        }
        
        .customer-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            color: #0056b3;
        }
        
        .work-details {
            margin-bottom: 20px;
        }
        
        .work-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .work-table th {
            background-color: #0056b3;
            color: white;
            padding: 12px 8px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
        }
        
        .work-table td {
            padding: 10px 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .work-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .total-section {
            display: table;
            width: 100%;
            margin-top: 20px;
            border-top: 2px solid #0056b3;
            padding-top: 15px;
        }
        
        .payment-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .total-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        
        .total-amount {
            font-size: 16px;
            font-weight: bold;
            color: #0056b3;
            margin-top: 10px;
        }
        
        .payment-status {
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
        }
        
        .payment-paid {
            background-color: #28a745;
            color: white;
        }
        
        .payment-unpaid {
            background-color: #dc3545;
            color: white;
        }
        
        .signature-section {
            margin-top: 40px;
            text-align: center;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 30px auto 5px auto;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .notes {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }
        
        .notes-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>    <!-- Header con logo e dati azienda -->
    <div class="header">
        <div class="logo-section">
            @if(file_exists(public_path('img/logo/logo.jpg')))
                <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('img/logo/logo.jpg'))) }}" alt="Logo Azienda" class="logo">
            @endif
        </div>
        <div class="company-section">
            <div class="company-name">{{ env('NOME_AZIENDA') }}</div>
            <div class="company-details">
                {{ env('INDIRIZZO_AZIENDA') }}<br>
                P.IVA: {{ env('PARTITA_IVA_AZIENDA') }}<br>
                Tel: {{ env('TELEFONO_AZIENDA') }}<br>
                Email: {{ env('EMAIL_AZIENDA') }}
            </div>
        </div>
    </div>

    <!-- Titolo ricevuta -->
    <div class="receipt-title">
        Ricevuta di Lavoro N. {{ $ricevuta->numero_ricevuta }}
    </div>

    <!-- Informazioni ricevuta -->
    <div class="receipt-info">
        <div class="receipt-info-left">
            <strong>Data emissione:</strong> {{ $ricevuta->created_at->format('d/m/Y') }}<br>
            <strong>Ricevente:</strong> {{ $ricevuta->nome_ricevente }}
        </div>
        <div class="receipt-info-right">
            <strong>Fattura richiesta:</strong> {{ $ricevuta->fattura ? 'Sì' : 'No' }}<br>
            @if($ricevuta->riserva_controlli)
                <strong>Riserva controlli:</strong> Sì
            @endif
        </div>
    </div>    <!-- Informazioni cliente -->
    <div class="customer-info">
        <div class="customer-title">Dati Cliente</div>
        <strong>{{ $customer->customer_type == 'fisica' ? $customer->full_name : $customer->ragione_sociale }}</strong><br>
        @if($customer->email)
            Email: {{ $customer->email }}<br>
        @endif
        @if($customer->phone)
            Telefono: {{ $customer->phone }}<br>
        @endif
        @if($customer->address)
            Indirizzo: {{ $customer->address }}
        @endif
    </div>

    <!-- Dettagli del lavoro -->
    <div class="work-details">
        <table class="work-table">            <thead>
                <tr>
                    <th style="width: 40%">Descrizione Lavoro</th>
                    <th style="width: 20%">Data Esecuzione</th>
                    <th style="width: 20%">Destinazione</th>
                    <th style="width: 20%">Importo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $work->tipo_lavoro }}</strong>
                        @if($work->materiale)
                            <br><small><strong>Materiale:</strong> {{ $work->materiale }}</small>
                        @endif
                        @if($work->codice_eer)
                            <br><small><strong>Codice EER:</strong> {{ $work->codice_eer }}</small>
                        @endif
                    </td>
                    <td>{{ $work->data_esecuzione ? \Carbon\Carbon::parse($work->data_esecuzione)->format('d/m/Y') : 'Non specificata' }}</td>
                    <td>
                        {{ $work->nome_destinazione }}<br>
                        <small>{{ $work->indirizzo_destinazione }}</small>
                    </td>
                    <td style="text-align: right;">
                        @if($work->costo_lavoro)
                            € {{ number_format($work->costo_lavoro, 2, ',', '.') }}
                        @else
                            Da definire
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Sezione totali e pagamento -->
    <div class="total-section">
        <div class="payment-info">
            <strong>Stato Pagamento:</strong><br>
            <span class="payment-status {{ $ricevuta->pagamento_effettuato ? 'payment-paid' : 'payment-unpaid' }}">
                {{ $ricevuta->pagamento_effettuato ? 'PAGATO' : 'NON PAGATO' }}
            </span>
            @if($ricevuta->pagamento_effettuato && $ricevuta->somma_pagamento)
                <br><br><strong>Importo pagato:</strong> € {{ number_format($ricevuta->somma_pagamento, 2, ',', '.') }}
            @endif        </div>
        <div class="total-info">
            @if($work->costo_lavoro)
                <div class="total-amount">
                    TOTALE: € {{ number_format($work->costo_lavoro, 2, ',', '.') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Note aggiuntive -->
    @if($ricevuta->riserva_controlli)
    <div class="notes">
        <div class="notes-title">Nota Importante:</div>
        Questo lavoro è soggetto a riserva controlli.
    </div>
    @endif

    <!-- Sezione firma -->
    <div class="signature-section">
        <div class="signature-title">Firma del ricevente</div>
        <div class="signature-line"></div>
        <div>{{ $ricevuta->nome_ricevente }}</div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Ricevuta generata il {{ now()->format('d/m/Y H:i') }} - {{ env('NOME_AZIENDA') }}<br>
        Questo documento costituisce ricevuta del lavoro svolto
    </div>
</body>
</html>

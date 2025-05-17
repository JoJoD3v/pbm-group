<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ricevuta Lavoro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 150px;
        }
        h1 {
            color: #2c3e50;
        }
        .receipt-info {
            margin-bottom: 20px;
        }
        .receipt-details {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Ricevuta Lavoro {{ $ricevuta->numero_ricevuta }}</h1>
        </div>
        
        <div class="receipt-info">
            <p>Gentile {{ $customer->ragione_sociale ?? $customer->full_name }},</p>
            <p>Le inviamo la ricevuta relativa al lavoro effettuato in data {{ date('d/m/Y', strtotime($work->data_esecuzione)) }}.</p>
        </div>
        
        <div class="receipt-details">
            <h2>Dettagli Ricevuta</h2>
            <table>
                <tr>
                    <th>Numero Ricevuta:</th>
                    <td>{{ $ricevuta->numero_ricevuta }}</td>
                </tr>
                <tr>
                    <th>Data:</th>
                    <td>{{ date('d/m/Y', strtotime($ricevuta->created_at)) }}</td>
                </tr>
                <tr>
                    <th>Tipo Lavoro:</th>
                    <td>{{ $work->tipo_lavoro }}</td>
                </tr>
                <tr>
                    <th>Materiale:</th>
                    <td>{{ $work->materiale }}</td>
                </tr>
                <tr>
                    <th>Indirizzo:</th>
                    <td>{{ $work->indirizzo_destinazione }}</td>
                </tr>
                <tr>
                    <th>Importo:</th>
                    <td>€ {{ number_format($work->costo_lavoro, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Pagamento Effettuato:</th>
                    <td>{{ $ricevuta->pagamento_effettuato ? 'Sì' : 'No' }}</td>
                </tr>
                @if($ricevuta->pagamento_effettuato)
                <tr>
                    <th>Somma Pagata:</th>
                    <td>€ {{ number_format($ricevuta->somma_pagamento, 2, ',', '.') }}</td>
                </tr>
                @endif
                <tr>
                    <th>Ricevente:</th>
                    <td>{{ $ricevuta->nome_ricevente }}</td>
                </tr>
            </table>
        </div>
        
        <div class="footer">
            <p>Grazie per aver scelto i nostri servizi.</p>
            <p>Per qualsiasi informazione, non esiti a contattarci.</p>
        </div>
    </div>
</body>
</html> 
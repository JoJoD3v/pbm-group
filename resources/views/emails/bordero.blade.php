<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Borderò Lavoro</title>
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
        h1 {
            color: #2c3e50;
        }
        .bordero-info {
            margin-bottom: 20px;
        }
        .bordero-details {
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
            <h1>Borderò Lavoro #{{ $work->id }}</h1>
        </div>

        <div class="bordero-info">
            <p>Gentile {{ $recipient->ragione_sociale ?? $recipient->full_name }},</p>
            <p>Le inviamo in allegato il borderò relativo al lavoro {{ $work->data_esecuzione ? 'effettuato in data '.date('d/m/Y', strtotime($work->data_esecuzione)) : 'in oggetto' }}.</p>
        </div>

        <div class="bordero-details">
            <h2>Dettagli Borderò</h2>
            <table>
                <tr>
                    <th>Lavoro:</th>
                    <td>#{{ $work->id }}</td>
                </tr>
                <tr>
                    <th>Tipo Lavoro:</th>
                    <td>{{ $work->tipo_lavoro }}</td>
                </tr>
                <tr>
                    <th>Status Borderò:</th>
                    <td>{{ $bordero->status }}</td>
                </tr>
                @if($bordero->note_tecniche)
                <tr>
                    <th>Note Tecniche:</th>
                    <td>{{ $bordero->note_tecniche }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="footer">
            <p>Grazie per aver scelto i nostri servizi.</p>
            <p>Per qualsiasi informazione, non esiti a contattarci.</p>
        </div>
    </div>
</body>
</html>

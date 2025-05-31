<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenziali di accesso - TEP SRL Group</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            line-height: 1.6;
            color: #1E1E1E;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #D17825 0%, #3B2A1C 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 500;
        }
        .content {
            padding: 30px;
        }
        .credentials-box {
            background-color: #f8f9fa;
            border: 2px solid #D17825;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .credential-item {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .credential-item:last-child {
            border-bottom: none;
        }
        .credential-label {
            font-weight: 500;
            color: #3B2A1C;
            display: inline-block;
            width: 100px;
        }
        .credential-value {
            color: #1E1E1E;
            font-family: monospace;
            background-color: #ffffff;
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .footer {
            background-color: #4C4C4C;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background-color: #D17825;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 500;
        }
        .btn:hover {
            background-color: #3B2A1C;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TEP SRL Group</h1>
            <p>Gestionale Trasporto e Smaltimento Rifiuti</p>
        </div>
        
        <div class="content">
            <h2>Benvenuto/a {{ $user->first_name }} {{ $user->last_name }}!</h2>
            
            <p>È stato creato un account per te nel sistema gestionale di TEP SRL Group. Di seguito trovi le tue credenziali di accesso:</p>
            
            <div class="credentials-box">
                <h3 style="margin-top: 0; color: #D17825;">Credenziali di Accesso</h3>
                
                <div class="credential-item">
                    <span class="credential-label">Email:</span>
                    <span class="credential-value">{{ $user->email }}</span>
                </div>
                
                <div class="credential-item">
                    <span class="credential-label">Password:</span>
                    <span class="credential-value">{{ $password }}</span>
                </div>
                
                <div class="credential-item">
                    <span class="credential-label">Ruolo:</span>
                    <span class="credential-value">{{ ucfirst($user->role) }}</span>
                </div>
            </div>
            
            <div class="warning">
                <strong>⚠️ Importante:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Conserva queste credenziali in un luogo sicuro</li>
                    <li>Ti consigliamo di cambiare la password al primo accesso</li>
                    <li>Non condividere mai le tue credenziali con altri</li>
                </ul>
            </div>
            
            <p>Puoi accedere al sistema utilizzando il seguente link:</p>
            <a href="{{ url('/login') }}" class="btn">Accedi al Sistema</a>
            
            <p>Se hai domande o problemi di accesso, contatta l'amministratore di sistema.</p>
            
            <p>Cordiali saluti,<br>
            <strong>Team TEP SRL Group</strong></p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} TEP SRL Group - Gestionale Trasporto e Smaltimento Rifiuti</p>
            <p>Questa è una email automatica, non rispondere a questo messaggio.</p>
        </div>
    </div>
</body>
</html>

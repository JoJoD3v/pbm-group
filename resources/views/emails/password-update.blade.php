<!DOCTYPE html>
<html>
<head>
    <title>Aggiornamento Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        h1 {
            color: #2c3e50;
            margin-top: 0;
        }
        .credentials {
            background: #fff;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #eee;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
        .alert {
            background: #fff3cd;
            border: 1px solid #ffd700;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Aggiornamento Password</h1>
        
        <p>Ciao {{ $name }},</p>
        
        <p>La tua password per l'accesso al sistema è stata aggiornata con successo.</p>
        
        <div class="alert">
            <strong>⚠️ Importante:</strong> Questa email contiene informazioni sensibili. Ti consigliamo di cambiare la password al primo accesso.
        </div>
        
        <div class="credentials">
            <h3>Le tue nuove credenziali di accesso:</h3>
            <p><strong>Email:</strong> {{ $email }}</p>
            <p><strong>Password:</strong> {{ $password }}</p>
        </div>
        
        <p>Puoi accedere al sistema utilizzando queste credenziali.</p>
        
        <p><strong>Per motivi di sicurezza, ti consigliamo di:</strong></p>
        <ul>
            <li>Cambiare la password al primo accesso</li>
            <li>Utilizzare una password sicura (almeno 8 caratteri)</li>
            <li>Non condividere le tue credenziali con nessuno</li>
            <li>Eliminare questa email dopo aver preso nota delle credenziali</li>
        </ul>
        
        <p>Se non hai richiesto questo aggiornamento o hai domande, contatta immediatamente l'amministratore del sistema.</p>
        
        <p>Grazie!</p>
        
        <div class="footer">
            <p>Questo messaggio è stato generato automaticamente. Non rispondere a questa email.</p>
            <p>&copy; {{ date('Y') }} Sistema di Gestione Aziendale</p>
        </div>
    </div>
</body>
</html>

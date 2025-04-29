<!DOCTYPE html>
<html>
<head>
    <title>Benvenuto nel Sistema</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Benvenuto nel Sistema!</h1>
        
        <p>Gentile {{ $name }},</p>
        
        <p>Il tuo account è stato creato con successo. Di seguito trovi le credenziali di accesso:</p>
        
        <div class="credentials">
            <p><strong>Email:</strong> {{ $email }}</p>
            <p><strong>Password provvisoria:</strong> {{ $password }}</p>
        </div>
        
        <p>Ti consigliamo di cambiare la password al primo accesso per motivi di sicurezza.</p>
        
        <p>Per accedere al sistema, visita la pagina di login e inserisci le credenziali fornite.</p>
        
        <p>Cordiali saluti,<br>
        Il Team</p>
        
        <div class="footer">
            <p>Questa è un'email automatica, si prega di non rispondere.</p>
        </div>
    </div>
</body>
</html>

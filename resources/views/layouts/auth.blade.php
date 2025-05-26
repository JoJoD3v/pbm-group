<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login') - PBM Group</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts - Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,0,1;wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <style>
        .auth-body {
            background: linear-gradient(135deg, #1F2937 0%, #3B2A1C 50%, #D17825 100%);
            min-height: 100vh;
            font-family: 'Roboto', sans-serif;
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        
        .auth-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        
        .auth-left {
            background: linear-gradient(135deg, #1F2937 0%, #3B2A1C 100%);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
        }
        
        .auth-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .auth-logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }
        
        .auth-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }
        
        .auth-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 300;
            position: relative;
            z-index: 1;
        }
        
        .auth-right {
            padding: 3rem;
        }
        
        .auth-form-title {
            color: #3B2A1C;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .auth-form-subtitle {
            color: #4C4C4C;
            margin-bottom: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
        }
        
        .form-floating .form-control {
            border: 2px solid #9CA3AF;
            border-radius: 12px;
            padding: 1rem 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-floating .form-control:focus {
            border-color: #D17825;
            box-shadow: 0 0 0 0.25rem rgba(209, 120, 37, 0.15);
        }
        
        .form-floating label {
            color: #4C4C4C;
            font-weight: 500;
        }
        
        .btn-auth {
            background: linear-gradient(135deg, #D17825 0%, #F4A261 100%);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(209, 120, 37, 0.3);
            background: linear-gradient(135deg, #F4A261 0%, #D17825 100%);
        }
        
        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #721c24;
        }
        
        @media (max-width: 768px) {
            .auth-left {
                padding: 2rem;
            }
            
            .auth-right {
                padding: 2rem;
            }
            
            .auth-logo {
                width: 80px;
                height: 80px;
            }
            
            .auth-title {
                font-size: 2rem;
            }
            
            .auth-form-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-card">
            <div class="row g-0 h-100">
                <div class="col-lg-6">
                    <div class="auth-left h-100">
                        <div>
                            <img src="{{ asset('img/logo/logo.jpg') }}" alt="PBM Group Logo" class="auth-logo">
                            <h1 class="auth-title">PBM Group</h1>
                            <p class="auth-subtitle">Gestionale Trasporto e Smaltimento Rifiuti</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="auth-right h-100 d-flex flex-column justify-content-center">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

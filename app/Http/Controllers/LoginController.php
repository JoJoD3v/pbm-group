<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Mostra il form di login
    public function showLoginForm()
    {
        return view('login');
    }

    // Gestisce il processo di login (accetta email oppure username)
    public function login(Request $request)
    {
        // Validazione dei dati in ingresso
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Inserisci la tua email o il tuo username.',
            'password.required' => 'Inserisci la password.',
        ]);

        // Se il valore inserito e' una email cerca su users.email, altrimenti su users.username
        $campo = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $campo     => $request->login,
            'password' => $request->password,
        ];

        // Tentativo di autenticazione
        if (Auth::attempt($credentials)) {
            // Rigenera la sessione per prevenire attacchi di session fixation
            $request->session()->regenerate();

            // Reindirizza l'utente alla dashboard o a un'altra pagina protetta
            return redirect()->intended('dashboard');
        }

        // In caso di credenziali errate, ritorna al form con un messaggio di errore
        return back()->withErrors([
            'login' => 'Credenziali non valide.',
        ])->onlyInput('login');
    }

    // Gestisce il logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

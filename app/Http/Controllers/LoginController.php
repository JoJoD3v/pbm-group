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

    // Gestisce il processo di login
    public function login(Request $request)
    {
        // Validazione dei dati in ingresso
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Tentativo di autenticazione
        if (Auth::attempt($credentials)) {
            // Rigenera la sessione per prevenire attacchi di session fixation
            $request->session()->regenerate();

            // (Facoltativo) Se desideri, puoi salvare il ruolo nella sessione:
            // $request->session()->put('role', Auth::user()->role);

            // Reindirizza l'utente alla dashboard o a un'altra pagina protetta
            return redirect()->intended('dashboard');
        }

        // In caso di credenziali errate, ritorna al form con un messaggio di errore
        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Credenziali non valide.',
            ])->onlyInput('email');
        }
        
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

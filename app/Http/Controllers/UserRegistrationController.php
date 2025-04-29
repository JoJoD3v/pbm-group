<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserRegistrationController extends Controller
{
    // Mostra il form di registrazione
    public function showRegistrationForm()
    {
        return view('register');
    }

    // Gestisce la registrazione
    public function register(Request $request)
    {
        // Validazione dei dati
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'role'       => 'required|in:sviluppatore,amministratore,dipendente',
        ]);

        // Genera una password casuale (10 caratteri, ad esempio)
        $password = Str::random(10);

        // Crea l'utente salvando la password in forma hash
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'role'       => $request->role,
            'password'   => Hash::make($password),
        ]);

        // Qui potresti, ad esempio, inviare un'email con la password

        // Ritorna una view di conferma mostrando la password in chiaro
        return view('registration_success', compact('user', 'password'));
    }
}

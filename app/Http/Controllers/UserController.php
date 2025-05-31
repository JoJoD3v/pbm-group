<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCredentialsMail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validazione dei dati
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'phone'      => 'nullable|string|max:20',
            'role'       => 'required|in:sviluppatore,amministratore,dipendente',
            'password'   => 'required|string|min:8|confirmed',
        ]);

        // Crea l'utente
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'name'       => $request->first_name . ' ' . $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'role'       => $request->role,
            'password'   => Hash::make($request->password),
        ]);

        // Invia email con le credenziali
        try {
            Mail::to($user->email)->send(new UserCredentialsMail($user, $request->password));
        } catch (\Exception $e) {
            // Log dell'errore ma non bloccare la creazione dell'utente
            \Log::error('Errore invio email credenziali: ' . $e->getMessage());
        }

        return redirect()->route('users.index')
            ->with('success', 'Utente creato con successo. Email con le credenziali inviata a: ' . $user->email);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validazione dei dati
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone'      => 'nullable|string|max:20',
            'role'       => 'required|in:sviluppatore,amministratore,dipendente',
            'password'   => 'nullable|string|min:8|confirmed',
        ]);

        // Aggiorna i dati dell'utente
        $updateData = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'name'       => $request->first_name . ' ' . $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'role'       => $request->role,
        ];

        // Se è stata fornita una nuova password, aggiornala
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);

            // Invia email con le nuove credenziali
            try {
                Mail::to($user->email)->send(new UserCredentialsMail($user, $request->password));
            } catch (\Exception $e) {
                \Log::error('Errore invio email credenziali aggiornate: ' . $e->getMessage());
            }
        }

        $user->update($updateData);

        return redirect()->route('users.index')
            ->with('success', 'Utente aggiornato con successo.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Impedisce l'eliminazione dell'utente corrente
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Non puoi eliminare il tuo stesso account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utente eliminato con successo.');
    }
}
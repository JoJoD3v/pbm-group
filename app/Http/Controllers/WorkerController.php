<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class WorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workers = Worker::all();
        return view('workers.index', compact('workers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('workers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_worker' => 'required|string|max:255',
            'cognome_worker' => 'required|string|max:255',
            'license_worker' => 'required|string|max:255',
            'worker_email' => 'required|email|max:255|unique:workers,worker_email|unique:users,email',
            'phone_worker' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
        ]);

        // Genera automaticamente l'ID lavoratore
        $lastWorker = Worker::orderBy('id', 'desc')->first();
        $nextId = $lastWorker ? $lastWorker->id + 1 : 1;
        $id_worker = 'TEP-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        // Crea il worker
        $worker = Worker::create([
            'id_worker' => $id_worker,
            'name_worker' => $request->name_worker,
            'cognome_worker' => $request->cognome_worker,
            'license_worker' => $request->license_worker,
            'worker_email' => $request->worker_email,
            'phone_worker' => $request->phone_worker,
        ]);

        // Crea l'utente associato con la password fornita
        $user = User::create([
            'first_name' => $request->name_worker,
            'last_name' => $request->cognome_worker,
            'email' => $request->worker_email,
            'role' => 'dipendente',
            'phone' => $request->phone_worker,
            'password' => Hash::make($request->password),
        ]);

        // Invia email con le credenziali di accesso
        $this->sendWelcomeEmail($user, $request->password);

        return redirect()->route('workers.index')
                         ->with('success', 'Lavoratore creato con successo. ID generato: ' . $id_worker . '. Email inviata con le credenziali.');
    }

    /**
     * Invia email di benvenuto con password provvisoria
     */
    private function sendWelcomeEmail($user, $password)
    {
        $data = [
            'name' => $user->first_name,
            'email' => $user->email,
            'password' => $password
        ];

        Mail::send('emails.welcome', $data, function($message) use ($user) {
            $message->to($user->email, $user->first_name . ' ' . $user->last_name)
                    ->subject('Benvenuto nel sistema - Credenziali di accesso');
        });
    }

    /**
     * Invia email con la nuova password aggiornata
     */
    private function sendPasswordUpdateEmail($user, $password)
    {
        $data = [
            'name' => $user->first_name,
            'email' => $user->email,
            'password' => $password
        ];

        Mail::send('emails.password-update', $data, function($message) use ($user) {
            $message->to($user->email, $user->first_name . ' ' . $user->last_name)
                    ->subject('Aggiornamento password - Nuove credenziali di accesso');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Worker $worker)
    {
        return view('workers.show', compact('worker'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Worker $worker)
    {
        return view('workers.edit', compact('worker'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Worker $worker)
    {
        $request->validate([
            'name_worker' => 'required|string|max:255',
            'cognome_worker' => 'required|string|max:255',
            'license_worker' => 'required|string|max:255',
            'worker_email' => 'required|email|max:255|unique:workers,worker_email,' . $worker->id . '|unique:users,email,' . User::where('email', $worker->worker_email)->first()?->id,
            'phone_worker' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
        ]);

        // Salva la vecchia email per verificare se è cambiata
        $oldEmail = $worker->worker_email;
        
        // Aggiorna il worker
        $worker->update($request->except('password'));
        
        // Trova l'utente associato
        $user = User::where('email', $oldEmail)->first();
        
        if ($user) {
            $userData = [
                'first_name' => $request->name_worker,
                'last_name' => $request->cognome_worker,
                'email' => $request->worker_email,
                'phone' => $request->phone_worker,
            ];
            
            // Se è stata fornita una nuova password, aggiornala
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
                
                // Invia email con la nuova password
                $this->sendPasswordUpdateEmail($user, $request->password);
            }
            
            $user->update($userData);
        }
        
        return redirect()->route('workers.index')
                         ->with('success', 'Lavoratore aggiornato con successo.' . ($request->filled('password') ? ' Email inviata con le nuove credenziali.' : ''));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Worker $worker)
    {
        // Elimina anche l'utente associato
        $user = User::where('email', $worker->worker_email)->first();
        if ($user) {
            $user->delete();
        }
        
        $worker->delete();
        return redirect()->route('workers.index')
                         ->with('success', 'Lavoratore e relativo account utente eliminati con successo.');
    }
}

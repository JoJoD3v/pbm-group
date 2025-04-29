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
            'id_worker' => 'required|string|max:255|unique:workers',
            'name_worker' => 'required|string|max:255',
            'cognome_worker' => 'required|string|max:255',
            'license_worker' => 'required|string|max:255',
            'worker_email' => 'required|email|max:255|unique:workers,worker_email|unique:users,email',
        ]);

        // Crea il worker
        $worker = Worker::create($request->all());

        // Genera una password casuale
        $password = Str::random(10);

        // Crea l'utente associato
        $user = User::create([
            'first_name' => $request->name_worker,
            'last_name' => $request->cognome_worker,
            'email' => $request->worker_email,
            'role' => 'dipendente',
            'phone' => null, // Opzionale, può essere aggiornato in seguito
            'password' => Hash::make($password),
        ]);

        // Invia email con la password provvisoria
        $this->sendWelcomeEmail($user, $password);

        return redirect()->route('workers.index')
                         ->with('success', 'Lavoratore creato con successo e account utente generato.');
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
            'id_worker' => 'required|string|max:255|unique:workers,id_worker,' . $worker->id,
            'name_worker' => 'required|string|max:255',
            'cognome_worker' => 'required|string|max:255',
            'license_worker' => 'required|string|max:255',
            'worker_email' => 'required|email|max:255|unique:workers,worker_email,' . $worker->id . '|unique:users,email,' . User::where('email', $worker->worker_email)->first()?->id,
        ]);

        // Salva la vecchia email per verificare se è cambiata
        $oldEmail = $worker->worker_email;
        
        // Aggiorna il worker
        $worker->update($request->all());
        
        // Se l'email è cambiata, aggiorna anche l'utente associato
        if ($oldEmail != $request->worker_email) {
            $user = User::where('email', $oldEmail)->first();
            if ($user) {
                $user->update([
                    'first_name' => $request->name_worker,
                    'last_name' => $request->cognome_worker,
                    'email' => $request->worker_email,
                ]);
            }
        } else {
            // Aggiorna comunque nome e cognome dell'utente
            $user = User::where('email', $request->worker_email)->first();
            if ($user) {
                $user->update([
                    'first_name' => $request->name_worker,
                    'last_name' => $request->cognome_worker,
                ]);
            }
        }
        
        return redirect()->route('workers.index')
                         ->with('success', 'Lavoratore aggiornato con successo.');
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

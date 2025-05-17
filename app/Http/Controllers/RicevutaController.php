<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Ricevuta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RicevutaController extends Controller
{
    /**
     * Mostra il form per creare una nuova ricevuta
     */
    public function create($workId)
    {
        // Recupera il lavoro
        $work = Work::with('customer')->findOrFail($workId);
        
        // Verifica che il lavoratore sia associato al lavoro
        $worker = Auth::user()->worker;
        if (!$worker || !$work->workers->contains($worker->id)) {
            return redirect()->route('worker.jobs')
                ->with('error', 'Non sei autorizzato ad accedere a questo lavoro.');
        }
        
        // Genera un numero di ricevuta univoco
        $numeroRicevuta = $this->generateRicevutaNumber();
        
        return view('worker.ricevute.create', compact('work', 'numeroRicevuta'));
    }
    
    /**
     * Memorizza una nuova ricevuta nel database
     */
    public function store(Request $request)
    {
        Log::info('Ricevuta store: inizio processo di salvataggio');
        
        // Log tutti i dati ricevuti (eccetto firma)
        Log::info('Dati ricevuti:', $request->except(['firma_base64']));
        
        try {
            // Validazione dei dati
            $validatedData = $request->validate([
                'work_id' => 'required|exists:works,id',
                'numero_ricevuta' => 'required|string|unique:ricevute,numero_ricevuta',
                'fattura' => 'required|boolean',
                'riserva_controlli' => 'boolean',
                'nome_ricevente' => 'required|string|max:255',
                'firma_base64' => 'required|string',
                'pagamento_effettuato' => 'required|boolean',
                'somma_pagamento' => 'required_if:pagamento_effettuato,1|nullable|numeric',
                'foto_bolla' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB
            ]);
            
            // Assicura il valore booleano corretto per riserva_controlli
            $validatedData['riserva_controlli'] = isset($validatedData['riserva_controlli']) && $validatedData['riserva_controlli'] == 1 ? 1 : 0;
            
            Log::info('Dati validati con successo', array_keys($validatedData));
            
            // Recupera il lavoro
            $work = Work::with('customer')->findOrFail($request->work_id);
            
            // Salva l'immagine della bolla se presente
            if ($request->hasFile('foto_bolla')) {
                Log::info('File foto_bolla presente, salvataggio in corso');
                $path = $request->file('foto_bolla')->store('bolle', 'public');
                $validatedData['foto_bolla'] = $path;
                Log::info('File foto_bolla salvato in: ' . $path);
            } else {
                Log::info('Nessun file foto_bolla ricevuto');
            }
            
            // Gestione campo somma_pagamento quando pagamento_effettuato è false
            if (!$validatedData['pagamento_effettuato']) {
                $validatedData['somma_pagamento'] = null;
            }
            
            // Crea la ricevuta
            $ricevuta = Ricevuta::create($validatedData);
            Log::info('Ricevuta creata con ID: ' . $ricevuta->id);
            
            // Aggiorna lo stato del lavoro a "Concluso"
            $work->status_lavoro = 'Concluso';
            $work->save();
            Log::info('Stato lavoro aggiornato a Concluso');
            
            // Invia email al cliente se è richiesta la fattura
            if ($ricevuta->fattura && $work->customer->email) {
                $this->sendReceiptEmail($ricevuta);
                Log::info('Email ricevuta inviata al cliente: ' . $work->customer->email);
            }
            
            return redirect()->route('worker.jobs')
                ->with('success', 'Ricevuta generata con successo.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Errore di validazione: ', [
                'errors' => $e->errors(),
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Errore nel salvataggio della ricevuta: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Errore nel salvataggio della ricevuta: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Genera un numero di ricevuta univoco
     */
    private function generateRicevutaNumber()
    {
        $prefix = 'TEP-';
        $lastRicevuta = Ricevuta::orderBy('id', 'desc')->first();
        
        if ($lastRicevuta) {
            // Estrai il numero dalla ultima ricevuta
            $lastNumber = intval(str_replace($prefix, '', $lastRicevuta->numero_ricevuta));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Formatta il numero con zeri iniziali
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Invia l'email con la ricevuta al cliente
     */
    private function sendReceiptEmail(Ricevuta $ricevuta)
    {
        $work = $ricevuta->work;
        $customer = $work->customer;
        
        try {
            Mail::send('emails.receipt', [
                'ricevuta' => $ricevuta,
                'work' => $work,
                'customer' => $customer
            ], function ($message) use ($customer, $ricevuta) {
                $message->to($customer->email)
                        ->subject('Ricevuta lavoro TEP-' . $ricevuta->numero_ricevuta);
                
                // Allega la foto della bolla se disponibile
                if ($ricevuta->foto_bolla) {
                    $message->attach(storage_path('app/public/' . $ricevuta->foto_bolla));
                }
            });
        } catch (\Exception $e) {
            Log::error('Errore invio email ricevuta: ' . $e->getMessage());
        }
    }
}

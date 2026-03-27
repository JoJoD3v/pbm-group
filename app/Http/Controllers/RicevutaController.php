<?php

namespace App\Http\Controllers;

use App\Models\Ricevuta;
use App\Models\Work;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
        if (! $worker || ! $work->workers->contains($worker->id)) {
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
                Log::info('File foto_bolla salvato in: '.$path);
            } else {
                Log::info('Nessun file foto_bolla ricevuto');
            }

            // Gestione campo somma_pagamento quando pagamento_effettuato è false
            if (! $validatedData['pagamento_effettuato']) {
                $validatedData['somma_pagamento'] = null;
            }

            // Crea la ricevuta
            $ricevuta = Ricevuta::create($validatedData);
            Log::info('Ricevuta creata con ID: '.$ricevuta->id);

            // Invia email al cliente se è richiesta la fattura
            if ($ricevuta->fattura && $work->customer->email) {
                $this->sendReceiptEmail($ricevuta);
                Log::info('Email ricevuta inviata al cliente: '.$work->customer->email);
            }

            return redirect()->route('worker.jobs')
                ->with('success', 'Ricevuta generata con successo.');
        } catch (ValidationException $e) {
            Log::error('Errore di validazione: ', [
                'errors' => $e->errors(),
            ]);

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Errore nel salvataggio della ricevuta: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());

            return back()->with('error', 'Errore nel salvataggio della ricevuta: '.$e->getMessage())->withInput();
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
        return $prefix.str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Genera il PDF della ricevuta
     */
    private function generateReceiptPDF(Ricevuta $ricevuta)
    {
        $work = $ricevuta->work;
        $customer = $work->customer;

        // Genera l'HTML dalla view
        $html = view('pdf.receipt', [
            'ricevuta' => $ricevuta,
            'work' => $work,
            'customer' => $customer,
        ])->render();

        // Configura dompdf
        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Crea il nome del file
        $fileName = 'ricevuta_'.$ricevuta->numero_ricevuta.'.pdf';
        $filePath = storage_path('app/temp/'.$fileName);

        // Assicurati che la directory temp esista
        if (! file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        // Salva il file temporaneamente
        file_put_contents($filePath, $dompdf->output());

        return $filePath;
    }

    /**
     * Invia l'email con la ricevuta al cliente
     */
    private function sendReceiptEmail(Ricevuta $ricevuta)
    {
        $work = $ricevuta->work;
        $customer = $work->customer;

        try {
            // Genera il PDF
            $pdfPath = $this->generateReceiptPDF($ricevuta);

            Mail::send('emails.receipt', [
                'ricevuta' => $ricevuta,
                'work' => $work,
                'customer' => $customer,
            ], function ($message) use ($customer, $ricevuta, $pdfPath) {
                $message->to($customer->email)
                    ->subject('Ricevuta lavoro '.$ricevuta->numero_ricevuta);

                // Allega il PDF della ricevuta
                $message->attach($pdfPath, [
                    'as' => 'ricevuta_'.$ricevuta->numero_ricevuta.'.pdf',
                    'mime' => 'application/pdf',
                ]);

                // Allega la foto della bolla se disponibile
                if ($ricevuta->foto_bolla) {
                    $message->attach(storage_path('app/public/'.$ricevuta->foto_bolla));
                }
            });

            // Rimuovi il file temporaneo
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

        } catch (\Exception $e) {
            Log::error('Errore invio email ricevuta: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());
        }
    }

    /**
     * Scarica il PDF della ricevuta
     */
    public function downloadPDF($ricevutaId)
    {
        try {
            $ricevuta = Ricevuta::with(['work.customer'])->findOrFail($ricevutaId);

            // Verifica che l'utente sia autorizzato (dipendente assegnato o admin/sviluppatore)
            $user = Auth::user();
            $role = strtolower($user->role ?? '');
            $isAdmin = in_array($role, ['amministratore', 'sviluppatore']);
            $worker = $user->worker;
            $isAssignedWorker = $worker && $ricevuta->work->workers->contains($worker->id);

            if (! $isAdmin && ! $isAssignedWorker) {
                return redirect()->back()->with('error', 'Non sei autorizzato ad accedere a questa ricevuta.');
            }

            $work = $ricevuta->work;
            $customer = $work->customer;

            // Genera l'HTML dalla view
            $html = view('pdf.receipt', [
                'ricevuta' => $ricevuta,
                'work' => $work,
                'customer' => $customer,
            ])->render();

            // Configura dompdf
            $options = new Options;
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isRemoteEnabled', false);
            $options->set('isHtml5ParserEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Nome del file
            $fileName = 'ricevuta_'.$ricevuta->numero_ricevuta.'.pdf';

            // Restituisci il PDF per il download
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$fileName.'"',
            ]);

        } catch (\Exception $e) {
            Log::error('Errore download PDF ricevuta: '.$e->getMessage());

            return redirect()->back()->with('error', 'Errore nel download del PDF.');
        }
    }

    /**
     * Visualizza la foto bolla in modo sicuro
     */
    public function viewBolla($ricevutaId)
    {
        try {
            $ricevuta = Ricevuta::with(['work.workers'])->findOrFail($ricevutaId);

            if (! $ricevuta->foto_bolla) {
                return redirect()->back()->with('error', 'Bolla non disponibile.');
            }

            $user = Auth::user();
            $role = strtolower($user->role ?? '');
            $isAdmin = in_array($role, ['amministratore', 'sviluppatore']);

            $worker = $user->worker;
            $isAssignedWorker = $worker && $ricevuta->work && $ricevuta->work->workers->contains($worker->id);

            if (! $isAdmin && ! $isAssignedWorker) {
                return redirect()->back()->with('error', 'Non sei autorizzato ad accedere a questa bolla.');
            }

            if (! Storage::disk('public')->exists($ricevuta->foto_bolla)) {
                return redirect()->back()->with('error', 'File bolla non trovato.');
            }

            $filePath = Storage::disk('public')->path($ricevuta->foto_bolla);

            return response()->file($filePath);
        } catch (\Exception $e) {
            Log::error('Errore visualizzazione bolla: '.$e->getMessage());

            return redirect()->back()->with('error', 'Errore nella visualizzazione della bolla.');
        }
    }
}

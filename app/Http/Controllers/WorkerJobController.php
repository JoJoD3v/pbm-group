<?php

namespace App\Http\Controllers;

use App\Models\CashMovement;
use App\Models\CreditCard;
use App\Models\Work;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkerJobController extends Controller
{
    /**
     * Mostra l'elenco dei lavori assegnati al dipendente
     */
    public function index(Request $request)
    {
        // Recupera l'utente autenticato
        $user = Auth::user();
        Log::info('WorkerJobController: utente autenticato ID: '.$user->id.', Email: '.$user->email.', Ruolo: '.$user->role);

        // Cerca il worker associato all'utente usando la relazione
        $worker = $user->worker;

        if (! $worker) {
            // Prova anche con la query diretta per debug
            $workerByQuery = Worker::where('worker_email', $user->email)->first();

            if ($workerByQuery) {
                Log::error('WorkerJobController: worker trovato solo con query diretta, non attraverso la relazione');
                $worker = $workerByQuery;
            } else {
                Log::error("WorkerJobController: worker non trovato per l'email: ".$user->email);

                // Debug: mostra tutti i worker nel database
                $allWorkers = Worker::all();
                if ($allWorkers->count() > 0) {
                    Log::info('WorkerJobController: elenco di tutti i worker nel database:');
                    foreach ($allWorkers as $w) {
                        Log::info('Worker ID: '.$w->id.', Email: '.$w->worker_email);
                    }
                } else {
                    Log::info('WorkerJobController: nessun worker nel database');
                }

                return redirect()->route('dashboard')
                    ->with('error', 'Profilo dipendente non trovato. Contatta l\'amministratore.');
            }
        }

        Log::info('WorkerJobController: worker trovato ID: '.$worker->id.', Nome: '.$worker->getFullNameAttribute());

        try {
            $currentDate = $request->input('data', Carbon::today()->format('Y-m-d'));
            $dayStart = Carbon::parse($currentDate)->startOfDay();
            $dayEnd = Carbon::parse($currentDate)->endOfDay();

            // Recupera i lavori non assegnati con data di esecuzione nel giorno selezionato
            $works = Work::whereDoesntHave('workers')
                ->with('customer')
                ->whereBetween('data_esecuzione', [$dayStart, $dayEnd])
                ->orderBy('data_esecuzione')
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('WorkerJobController: trovati '.$works->count().' lavori non assegnati per '.$currentDate);

            return view('worker.jobs.index', compact('works', 'worker', 'currentDate'));
        } catch (\Exception $e) {
            Log::error('WorkerJobController: errore nel recupero dei lavori: '.$e->getMessage());

            return redirect()->route('dashboard')
                ->with('error', 'Errore nel caricamento dei lavori. Contatta l\'amministratore.');
        }
    }

    /**
     * Mostra i dettagli di un lavoro specifico
     */
    public function show($id)
    {
        // Recupera l'utente autenticato
        $user = Auth::user();

        // Cerca il worker associato all'utente usando la relazione
        $worker = $user->worker;

        if (! $worker) {
            // Prova anche con la query diretta per debug
            $worker = Worker::where('worker_email', $user->email)->first();

            if (! $worker) {
                return redirect()->route('dashboard')
                    ->with('error', 'Profilo dipendente non trovato. Contatta l\'amministratore.');
            }
        }

        try {
            // Recupera il lavoro con l'ID specificato
            $work = Work::with(['deposit', 'warehouseDestinazione'])->findOrFail($id);

            // Verifica se il lavoratore è associato al lavoro o se il lavoro non è assegnato a nessuno
            if (! $worker->works->contains($id) && $work->workers->count() > 0) {
                return redirect()->route('worker.jobs')
                    ->with('error', 'Lavoro non accessibile.');
            }

            $carteAssegnate = $worker->assignedCreditCards()->select('credit_cards.*')->get();

            return view('worker.jobs.show', compact('work', 'worker', 'carteAssegnate'));
        } catch (\Exception $e) {
            Log::error('WorkerJobController: errore nel recupero del lavoro '.$id.': '.$e->getMessage());

            return redirect()->route('worker.jobs')
                ->with('error', 'Lavoro non trovato o non accessibile.');
        }
    }

    /**
     * Assegna un lavoro non assegnato al dipendente loggato
     */
    public function assumiLavoro($id)
    {
        // Recupera l'utente autenticato
        $user = Auth::user();
        $worker = $user->worker;

        if (! $worker) {
            return redirect()->route('dashboard')
                ->with('error', 'Profilo dipendente non trovato. Contatta l\'amministratore.');
        }

        try {
            // Recupera il lavoro
            $work = Work::findOrFail($id);

            // Verifica che il lavoro non sia gia' assegnato a qualcuno
            if ($work->workers->count() > 0) {
                return redirect()->route('worker.jobs')
                    ->with('error', 'Questo lavoro è già stato assegnato.');
            }

            // Verifica che il lavoro abbia data di esecuzione odierna
            $todayStart = Carbon::today()->startOfDay();
            $todayEnd = Carbon::today()->endOfDay();
            if (! $work->data_esecuzione || $work->data_esecuzione < $todayStart || $work->data_esecuzione > $todayEnd) {
                return redirect()->route('worker.jobs')
                    ->with('error', 'Puoi assumere solo lavori con data di esecuzione odierna.');
            }

            // Assegna il lavoro al worker
            $work->workers()->attach($worker->id);
            $work->status_lavoro = 'Preso in Carico';
            $work->save();

            return redirect()->route('worker.jobs')
                ->with('success', 'Lavoro assegnato con successo.');

        } catch (\Exception $e) {
            Log::error("WorkerJobController: errore nell'assegnazione del lavoro ".$id.': '.$e->getMessage());

            return redirect()->route('worker.jobs')
                ->with('error', 'Errore nell\'assegnazione del lavoro. Contatta l\'amministratore.');
        }
    }

    /**
     * Aggiorna lo stato del lavoro assegnato al dipendente
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_lavoro' => 'required|string|in:Lavoro Iniziato,Lavoro Completato,Lavoro Annullato',
        ]);

        $user = Auth::user();
        $worker = $user->worker;

        if (! $worker) {
            return redirect()->route('dashboard')
                ->with('error', 'Profilo dipendente non trovato. Contatta l\'amministratore.');
        }

        try {
            $work = Work::findOrFail($id);

            if (! $work->workers->contains($worker->id)) {
                return redirect()->route('worker.jobs')
                    ->with('error', 'Non sei autorizzato ad aggiornare questo lavoro.');
            }

            $work->status_lavoro = $request->status_lavoro;
            $work->save();

            return redirect()->route('worker.jobs.show', $work->id)
                ->with('success', 'Stato lavoro aggiornato con successo.');
        } catch (\Exception $e) {
            Log::error('WorkerJobController: errore aggiornamento stato lavoro '.$id.': '.$e->getMessage());

            return redirect()->route('worker.jobs.show', $id)
                ->with('error', 'Errore nell\'aggiornamento dello stato. Contatta l\'amministratore.');
        }
    }

    /**
     * Registra una spesa dal fondo cassa o dalla carta del dipendente, collegata al lavoro
     */
    public function storeSpesaLavoro(Request $request, $id)
    {
        $user = Auth::user();
        $worker = $user->worker;

        if (! $worker) {
            return redirect()->route('dashboard')
                ->with('error', 'Profilo dipendente non trovato. Contatta l\'amministratore.');
        }

        $request->validate([
            'metodo_pagamento' => 'required|in:contanti,carta',
            'importo' => 'required|numeric|min:0.01',
            'causale' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $work = Work::with('customer')->findOrFail($id);

            $customerName = $work->customer
                ? ($work->customer->ragione_sociale ?? $work->customer->full_name)
                : 'Cliente sconosciuto';

            $motivo = $request->causale.' - Lavoro #'.$work->id.' ('.$customerName.')';

            $creditCardId = null;

            if ($request->metodo_pagamento === 'carta') {
                $carteAssegnate = $worker->assignedCreditCards()->get();

                if ($carteAssegnate->isEmpty()) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Nessuna carta prepagata assegnata a questo dipendente.');
                }

                $creditCardId = $request->input('credit_card_id');

                if (! $creditCardId && $carteAssegnate->count() === 1) {
                    $creditCardId = $carteAssegnate->first()->id;
                }

                if (! $creditCardId) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Seleziona una carta prepagata.');
                }

                $carta = CreditCard::find($creditCardId);

                if (! $worker->assignedCreditCards->contains($carta)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'La carta selezionata non è assegnata a questo dipendente.');
                }

                $carta->fondo_carta -= $request->importo;
                $carta->save();

                DB::table('credit_card_recharges')->insert([
                    'credit_card_id' => $creditCardId,
                    'user_id' => Auth::id(),
                    'importo' => -$request->importo,
                    'data_ricarica' => Carbon::now(),
                    'note' => 'Spesa lavoro: '.$motivo,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            } else {
                $worker->fondo_cassa -= $request->importo;
                $worker->save();
            }

            CashMovement::create([
                'worker_id' => $worker->id,
                'work_id' => $work->id,
                'tipo_movimento' => 'uscita',
                'importo' => $request->importo,
                'motivo' => $motivo,
                'metodo_pagamento' => $request->metodo_pagamento,
                'credit_card_id' => $creditCardId,
                'data_movimento' => Carbon::now()->toDateString(),
            ]);

            DB::commit();

            return redirect()->route('worker.jobs.show', $work->id)
                ->with('success', 'Spesa registrata con successo.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('WorkerJobController: errore spesa lavoro '.$id.': '.$e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Errore durante la registrazione della spesa: '.$e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Worker;
use App\Models\Work;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WorkerJobController extends Controller
{
    /**
     * Mostra l'elenco dei lavori assegnati al dipendente
     */
    public function index()
    {
        // Recupera l'utente autenticato
        $user = Auth::user();
        Log::info("WorkerJobController: utente autenticato ID: " . $user->id . ", Email: " . $user->email . ", Ruolo: " . $user->role);
        
        // Cerca il worker associato all'utente usando la relazione
        $worker = $user->worker;
        
        if (!$worker) {
            // Prova anche con la query diretta per debug
            $workerByQuery = Worker::where('worker_email', $user->email)->first();
            
            if ($workerByQuery) {
                Log::error("WorkerJobController: worker trovato solo con query diretta, non attraverso la relazione");
                $worker = $workerByQuery;
            } else {
                Log::error("WorkerJobController: worker non trovato per l'email: " . $user->email);
                
                // Debug: mostra tutti i worker nel database
                $allWorkers = Worker::all();
                if ($allWorkers->count() > 0) {
                    Log::info("WorkerJobController: elenco di tutti i worker nel database:");
                    foreach ($allWorkers as $w) {
                        Log::info("Worker ID: " . $w->id . ", Email: " . $w->worker_email);
                    }
                } else {
                    Log::info("WorkerJobController: nessun worker nel database");
                }
                
                return redirect()->route('dashboard')
                    ->with('error', 'Profilo dipendente non trovato. Contatta l\'amministratore.');
            }
        }
        
        Log::info("WorkerJobController: worker trovato ID: " . $worker->id . ", Nome: " . $worker->getFullNameAttribute());
        
        try {
            // Data odierna
            $today = Carbon::today()->format('Y-m-d');
            
            // Recupera i lavori assegnati al dipendente con data di esecuzione odierna
            $assignedWorks = $worker->works()
                ->where('data_esecuzione', $today)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Recupera i lavori non assegnati a nessuno con data di esecuzione odierna
            $unassignedWorks = Work::whereDoesntHave('workers')
                ->where('data_esecuzione', $today)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Unisce le due collezioni
            $works = $assignedWorks->concat($unassignedWorks);
            
            Log::info("WorkerJobController: trovati " . $works->count() . " lavori per oggi");
            
            return view('worker.jobs.index', compact('works', 'worker'));
        } catch (\Exception $e) {
            Log::error("WorkerJobController: errore nel recupero dei lavori: " . $e->getMessage());
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
        
        if (!$worker) {
            // Prova anche con la query diretta per debug
            $worker = Worker::where('worker_email', $user->email)->first();
            
            if (!$worker) {
                return redirect()->route('dashboard')
                    ->with('error', 'Profilo dipendente non trovato. Contatta l\'amministratore.');
            }
        }
        
        try {
            // Recupera il lavoro con l'ID specificato
            $work = Work::findOrFail($id);
            
            // Verifica se il lavoratore è associato al lavoro o se il lavoro non è assegnato a nessuno
            if (!$worker->works->contains($id) && $work->workers->count() > 0) {
                return redirect()->route('worker.jobs')
                    ->with('error', 'Lavoro non accessibile.');
            }
            
            return view('worker.jobs.show', compact('work', 'worker'));
        } catch (\Exception $e) {
            Log::error("WorkerJobController: errore nel recupero del lavoro " . $id . ": " . $e->getMessage());
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
        
        if (!$worker) {
            return redirect()->route('dashboard')
                ->with('error', 'Profilo dipendente non trovato. Contatta l\'amministratore.');
        }
        
        try {
            // Recupera il lavoro
            $work = Work::findOrFail($id);
            
            // Verifica che il lavoro non sia già assegnato a qualcuno
            if ($work->workers->count() > 0) {
                return redirect()->route('worker.jobs')
                    ->with('error', 'Questo lavoro è già stato assegnato.');
            }
            
            // Verifica che il lavoro abbia data di esecuzione odierna
            $today = Carbon::today()->format('Y-m-d');
            if ($work->data_esecuzione != $today) {
                return redirect()->route('worker.jobs')
                    ->with('error', 'Puoi assumere solo lavori con data di esecuzione odierna.');
            }
            
            // Assegna il lavoro al worker
            $work->workers()->attach($worker->id);
            
            return redirect()->route('worker.jobs')
                ->with('success', 'Lavoro assegnato con successo.');
                
        } catch (\Exception $e) {
            Log::error("WorkerJobController: errore nell'assegnazione del lavoro " . $id . ": " . $e->getMessage());
            return redirect()->route('worker.jobs')
                ->with('error', 'Errore nell\'assegnazione del lavoro. Contatta l\'amministratore.');
        }
    }
} 
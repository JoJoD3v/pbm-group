<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Worker;

class WorkAssignmentController extends Controller
{
    /**
     * Mostra la pagina con l'elenco delle assegnazioni
     */
    public function index()
    {
        $works = Work::all();
        
        // Ottieni tutte le assegnazioni esistenti
        $assignments = [];
        foreach ($works as $work) {
            foreach ($work->workers as $worker) {
                $assignments[] = [
                    'work_id' => $work->id,
                    'worker_id' => $worker->id,
                    'work_name' => $work->tipo_lavoro,
                    'worker_name' => $worker->full_name,
                    'customer_name' => $work->customer->full_name ?? $work->customer->ragione_sociale ?? 'N/D',
                    'indirizzo_partenza' => $work->indirizzo_partenza ?? 'N/D',
                    'indirizzo_destinazione' => $work->indirizzo_destinazione ?? 'N/D',
                    'materiale' => $work->materiale ?? 'N/D',
                    'status_lavoro' => $work->status_lavoro ?? 'N/D',
                ];
            }
        }
        
        return view('works.assignments.index', compact('assignments'));
    }
    
    /**
     * Mostra il form per creare una nuova assegnazione
     */
    public function create()
    {
        $works = Work::all();
        $workers = Worker::all();
        
        return view('works.assignments.create', compact('works', 'workers'));
    }
    
    /**
     * Salva una nuova assegnazione
     */
    public function store(Request $request)
    {
        $request->validate([
            'work_id' => 'required|exists:works,id',
            'worker_id' => 'required|exists:workers,id',
        ]);
        
        $work = Work::findOrFail($request->work_id);
        $worker = Worker::findOrFail($request->worker_id);
        
        // Verifica se l'assegnazione esiste già
        if (!$work->workers->contains($worker->id)) {
            $work->workers()->attach($worker->id);
            return redirect()->route('work.assignments.create')
                             ->with('success', 'Lavoro assegnato con successo.');
        }
        
        return redirect()->route('work.assignments.create')
                         ->with('error', 'Questo lavoro è già assegnato a questo lavoratore.');
    }
    
    /**
     * Rimuovi un'assegnazione
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'work_id' => 'required|exists:works,id',
            'worker_id' => 'required|exists:workers,id',
        ]);
        
        $work = Work::findOrFail($request->work_id);
        $work->workers()->detach($request->worker_id);
        
        return redirect()->route('work.assignments.index')
                         ->with('success', 'Assegnazione rimossa con successo.');
    }
}

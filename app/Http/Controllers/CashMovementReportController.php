<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashMovement;
use App\Models\Worker;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashMovementReportController extends Controller
{
    /**
     * Mostra la pagina di selezione per il report
     */
    public function index()
    {
        // Verifica che l'utente non sia un dipendente
        if (Auth::user()->role === 'dipendente') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }
        
        // Ottieni tutti i worker per la selezione
        $workers = Worker::orderBy('name_worker')->get();
        
        return view('admin.reports.cashflow.index', compact('workers'));
    }
    
    /**
     * Genera il report in base ai parametri selezionati
     */
    public function generate(Request $request)
    {
        // Verifica che l'utente non sia un dipendente
        if (Auth::user()->role === 'dipendente') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }
        
        $request->validate([
            'worker_id'  => 'required|exists:workers,id',
            'data_inizio' => 'required|date',
            'data_fine'   => 'required|date|after_or_equal:data_inizio',
        ], [
            'worker_id.required'        => 'Seleziona un dipendente',
            'worker_id.exists'          => 'Dipendente non valido',
            'data_inizio.required'      => 'Seleziona la data di inizio',
            'data_inizio.date'          => 'Data di inizio non valida',
            'data_fine.required'        => 'Seleziona la data di fine',
            'data_fine.date'            => 'Data di fine non valida',
            'data_fine.after_or_equal'  => 'La data di fine deve essere uguale o successiva alla data di inizio',
        ]);

        // Recupera il worker
        $worker = Worker::findOrFail($request->worker_id);

        $dataInizio = $request->data_inizio;
        $dataFine   = $request->data_fine;

        // Recupera i movimenti nel range selezionato per il worker
        $movimenti = CashMovement::where('worker_id', $worker->id)
                                ->whereBetween('data_movimento', [$dataInizio, $dataFine])
                                ->orderBy('data_movimento', 'asc')
                                ->orderBy('created_at', 'asc')
                                ->get();

        // Calcola il totale entrate, uscite e saldo
        $totaleEntrate = $movimenti->where('tipo_movimento', 'entrata')->sum('importo');
        $totaleUscite  = $movimenti->where('tipo_movimento', 'uscita')->sum('importo');
        $saldo         = $totaleEntrate - $totaleUscite;

        return view('admin.reports.cashflow.report', compact('movimenti', 'worker', 'dataInizio', 'dataFine', 'totaleEntrate', 'totaleUscite', 'saldo'));
    }
} 
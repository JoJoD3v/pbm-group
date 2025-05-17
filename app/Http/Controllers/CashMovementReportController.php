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
            'worker_id' => 'required|exists:workers,id',
            'data' => 'required|date',
        ], [
            'worker_id.required' => 'Seleziona un dipendente',
            'worker_id.exists' => 'Dipendente non valido',
            'data.required' => 'Seleziona una data',
            'data.date' => 'Data non valida',
        ]);
        
        // Recupera il worker
        $worker = Worker::findOrFail($request->worker_id);
        
        // Data selezionata
        $data = $request->data;
        
        // Recupera i movimenti del giorno specificato per il worker selezionato
        $movimenti = CashMovement::where('worker_id', $worker->id)
                                ->whereDate('data_movimento', $data)
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        // Calcola il totale entrate, uscite e saldo
        $totaleEntrate = $movimenti->where('tipo_movimento', 'entrata')->sum('importo');
        $totaleUscite = $movimenti->where('tipo_movimento', 'uscita')->sum('importo');
        $saldo = $totaleEntrate - $totaleUscite;
        
        return view('admin.reports.cashflow.report', compact('movimenti', 'worker', 'data', 'totaleEntrate', 'totaleUscite', 'saldo'));
    }
} 
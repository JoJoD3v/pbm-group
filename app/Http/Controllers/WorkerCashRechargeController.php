<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\CashMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkerCashRechargeController extends Controller
{
    /**
     * Mostra il form per la ricarica del fondo cassa di un dipendente
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
        
        return view('admin.cashflow.recharge', compact('workers'));
    }
    
    /**
     * Esegue la ricarica del fondo cassa
     */
    public function store(Request $request)
    {
        // Verifica che l'utente non sia un dipendente
        if (Auth::user()->role === 'dipendente') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }
        
        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'importo' => 'required|numeric|min:0.01',
            'motivo' => 'required|string|max:255',
        ], [
            'worker_id.required' => 'Seleziona un dipendente',
            'worker_id.exists' => 'Dipendente non valido',
            'importo.required' => 'L\'importo è obbligatorio',
            'importo.numeric' => 'L\'importo deve essere un numero',
            'importo.min' => 'L\'importo deve essere maggiore di zero',
            'motivo.required' => 'La motivazione è obbligatoria',
        ]);
        
        // Recupera il worker selezionato
        $worker = Worker::findOrFail($request->worker_id);
        
        // Inizia una transazione per garantire l'integrità dei dati
        DB::beginTransaction();
        
        try {
            // Aggiorna il fondo cassa del worker
            $worker->fondo_cassa += $request->importo;
            $worker->save();
            
            // Crea un movimento di cassa come ricarica (entrata)
            CashMovement::create([
                'worker_id' => $worker->id,
                'tipo_movimento' => 'entrata',
                'importo' => $request->importo,
                'motivo' => 'Ricarica fondo cassa: ' . $request->motivo,
                'metodo_pagamento' => 'contanti',
                'data_movimento' => Carbon::now()->toDateString(),
            ]);
            
            DB::commit();
            
            return redirect()->route('worker.cash.recharge')
                ->with('success', 'Fondo cassa ricaricato con successo per ' . $worker->name_worker . ' ' . $worker->cognome_worker);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Si è verificato un errore durante la ricarica: ' . $e->getMessage());
        }
    }
}

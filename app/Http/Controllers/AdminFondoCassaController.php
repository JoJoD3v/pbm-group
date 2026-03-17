<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\CashMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminFondoCassaController extends Controller
{
    /**
     * Mostra la lista dei dipendenti con il saldo del fondo cassa.
     */
    public function index()
    {
        if (Auth::user()->role === 'dipendente') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        $workers = Worker::orderBy('name_worker')->get();

        return view('admin.cashflow.fondo_cassa_list', compact('workers'));
    }

    /**
     * Mostra il form per modificare il fondo cassa di un dipendente.
     */
    public function edit(Worker $worker)
    {
        if (Auth::user()->role === 'dipendente') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        return view('admin.cashflow.fondo_cassa_edit', compact('worker'));
    }

    /**
     * Applica la modifica al fondo cassa e registra il movimento.
     */
    public function update(Request $request, Worker $worker)
    {
        if (Auth::user()->role === 'dipendente') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        $request->validate([
            'nuovo_valore' => 'required|numeric|min:0',
            'motivo' => 'nullable|string|max:255',
        ], [
            'nuovo_valore.required' => 'Inserisci il nuovo valore del fondo cassa',
            'nuovo_valore.numeric'  => 'Il valore deve essere un numero',
            'nuovo_valore.min'      => 'Il fondo cassa non può essere negativo',
        ]);

        $vecchioValore = (float) $worker->fondo_cassa;
        $nuovoValore   = (float) $request->nuovo_valore;
        $differenza    = round($nuovoValore - $vecchioValore, 2);

        // Nessuna modifica effettiva
        if ($differenza == 0) {
            return redirect()->route('admin.fondo-cassa.index')
                ->with('info', 'Nessuna modifica apportata: il valore è rimasto invariato.');
        }

        DB::beginTransaction();
        try {
            $worker->fondo_cassa = $nuovoValore;
            $worker->save();

            $motivoBase = $request->filled('motivo') ? $request->motivo : 'Modifica da Admin';

            CashMovement::create([
                'worker_id'        => $worker->id,
                'tipo_movimento'   => $differenza > 0 ? 'entrata' : 'uscita',
                'importo'          => abs($differenza),
                'motivo'           => 'Modifica da Admin' . ($request->filled('motivo') ? ': ' . $request->motivo : ''),
                'metodo_pagamento' => 'contanti',
                'data_movimento'   => Carbon::now()->toDateString(),
            ]);

            DB::commit();

            return redirect()->route('admin.fondo-cassa.index')
                ->with('success', 'Fondo cassa di ' . $worker->name_worker . ' ' . $worker->cognome_worker . ' modificato con successo.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Errore durante la modifica: ' . $e->getMessage());
        }
    }
}

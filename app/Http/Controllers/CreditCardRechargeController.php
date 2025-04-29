<?php

namespace App\Http\Controllers;

use App\Models\CreditCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreditCardRechargeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recharges = DB::table('credit_card_recharges')
            ->join('credit_cards', 'credit_card_recharges.credit_card_id', '=', 'credit_cards.id')
            ->leftJoin('users', 'credit_card_recharges.user_id', '=', 'users.id')
            ->select(
                'credit_card_recharges.*', 
                'credit_cards.numero_carta',
                'users.first_name as autore_nome',
                'users.last_name as autore_cognome'
            )
            ->orderBy('credit_card_recharges.data_ricarica', 'desc')
            ->get();

        return view('credit_card_recharges.index', compact('recharges'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $creditCards = CreditCard::all();
        return view('credit_card_recharges.create', compact('creditCards'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'credit_card_id' => 'required|exists:credit_cards,id',
            'importo' => 'required|numeric|min:0',
            'data_ricarica_data' => 'required|date',
            'data_ricarica_ora' => 'required',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            // Combina data e ora in un unico campo datetime
            $dataRicarica = $request->data_ricarica_data . ' ' . $request->data_ricarica_ora . ':00';
            
            // Inserisci la ricarica
            DB::table('credit_card_recharges')->insert([
                'credit_card_id' => $request->credit_card_id,
                'user_id' => Auth::id(), // Salva l'utente connesso come autore
                'importo' => $request->importo,
                'data_ricarica' => $dataRicarica,
                'note' => $request->note,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Aggiorna il fondo della carta
            DB::table('credit_cards')
                ->where('id', $request->credit_card_id)
                ->increment('fondo_carta', $request->importo);
        });

        return redirect()->route('credit-card-recharges.index')
            ->with('success', 'Ricarica effettuata con successo');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $recharge = DB::table('credit_card_recharges')
            ->join('credit_cards', 'credit_card_recharges.credit_card_id', '=', 'credit_cards.id')
            ->leftJoin('users', 'credit_card_recharges.user_id', '=', 'users.id')
            ->select(
                'credit_card_recharges.*', 
                'credit_cards.numero_carta',
                'users.first_name as autore_nome',
                'users.last_name as autore_cognome'
            )
            ->where('credit_card_recharges.id', $id)
            ->first();

        return view('credit_card_recharges.show', compact('recharge'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $recharge = DB::table('credit_card_recharges')
            ->join('credit_cards', 'credit_card_recharges.credit_card_id', '=', 'credit_cards.id')
            ->select(
                'credit_card_recharges.*', 
                'credit_cards.numero_carta'
            )
            ->where('credit_card_recharges.id', $id)
            ->first();
            
        if (!$recharge) {
            return redirect()->route('credit-card-recharges.index')
                ->with('error', 'Ricarica non trovata');
        }
        
        $creditCards = CreditCard::all();
        
        // Estrai data e ora
        $dataRicarica = \Carbon\Carbon::parse($recharge->data_ricarica);
        $dataRicaricaData = $dataRicarica->format('Y-m-d');
        $dataRicaricaOra = $dataRicarica->format('H:i');
        
        return view('credit_card_recharges.edit', compact('recharge', 'creditCards', 'dataRicaricaData', 'dataRicaricaOra'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'credit_card_id' => 'required|exists:credit_cards,id',
            'importo' => 'required|numeric|min:0',
            'data_ricarica_data' => 'required|date',
            'data_ricarica_ora' => 'required',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $id) {
            // Ottieni la ricarica corrente per avere l'importo originale
            $ricaricaOriginale = DB::table('credit_card_recharges')
                ->where('id', $id)
                ->first();
            
            // Combina data e ora in un unico campo datetime
            $dataRicarica = $request->data_ricarica_data . ' ' . $request->data_ricarica_ora . ':00';
            
            // Se l'importo è cambiato, dobbiamo aggiornare il fondo della carta
            $differenzaImporto = $request->importo - $ricaricaOriginale->importo;
            
            // Aggiorna la ricarica
            DB::table('credit_card_recharges')
                ->where('id', $id)
                ->update([
                    'credit_card_id' => $request->credit_card_id,
                    'importo' => $request->importo,
                    'data_ricarica' => $dataRicarica,
                    'note' => $request->note,
                    'updated_at' => now(),
                ]);
            
            // Se c'è stata una variazione dell'importo, aggiorna il fondo della carta
            if ($differenzaImporto != 0) {
                DB::table('credit_cards')
                    ->where('id', $request->credit_card_id)
                    ->increment('fondo_carta', $differenzaImporto);
            }
        });

        return redirect()->route('credit-card-recharges.index')
            ->with('success', 'Ricarica aggiornata con successo');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            // Ottieni la ricarica corrente per avere l'importo e la carta associata
            $ricarica = DB::table('credit_card_recharges')
                ->where('id', $id)
                ->first();
            
            if (!$ricarica) {
                return redirect()->route('credit-card-recharges.index')
                    ->with('error', 'Ricarica non trovata');
            }
            
            // Elimina la ricarica
            DB::table('credit_card_recharges')
                ->where('id', $id)
                ->delete();
            
            // Aggiorna il fondo della carta decrementandolo dell'importo della ricarica
            DB::table('credit_cards')
                ->where('id', $ricarica->credit_card_id)
                ->decrement('fondo_carta', $ricarica->importo);
        });

        return redirect()->route('credit-card-recharges.index')
            ->with('success', 'Ricarica eliminata con successo');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Worker;
use App\Models\CreditCard;

class WorkerCardController extends Controller
{
    /**
     * Mostra l'elenco delle carte assegnate al dipendente
     */
    public function index()
    {
        // Recupera l'email dell'utente autenticato
        $userEmail = Auth::user()->email;
        
        // Cerca il worker associato all'email dell'utente
        $worker = Worker::where('worker_email', $userEmail)->first();
        
        if (!$worker) {
            return redirect()->route('dashboard')
                ->with('error', 'Profilo dipendente non trovato. Contatta l\'amministratore.');
        }
        
        // Recupera le carte assegnate al dipendente con il saldo aggiornato
        $creditCards = $worker->assignedCreditCards()
                        ->select('credit_cards.*', 'credit_card_worker.created_at as data_assegnazione')
                        ->get();
        
        return view('worker.cards.index', compact('creditCards', 'worker'));
    }
    
    /**
     * Mostra i dettagli di una carta specifica
     */
    public function show($id)
    {
        // Recupera l'email dell'utente autenticato
        $userEmail = Auth::user()->email;
        
        // Cerca il worker associato all'email dell'utente
        $worker = Worker::where('worker_email', $userEmail)->first();
        
        if (!$worker) {
            return redirect()->route('dashboard')
                ->with('error', 'Profilo dipendente non trovato. Contatta l\'amministratore.');
        }
        
        // Recupera la carta con l'ID specificato e il saldo aggiornato
        $creditCard = DB::table('credit_cards')
                    ->join('credit_card_worker', 'credit_cards.id', '=', 'credit_card_worker.credit_card_id')
                    ->where('credit_cards.id', $id)
                    ->where('credit_card_worker.worker_id', $worker->id)
                    ->whereNull('credit_card_worker.data_restituzione')
                    ->select('credit_cards.*', 'credit_card_worker.created_at as data_assegnazione')
                    ->first();
                    
        if (!$creditCard) {
            return redirect()->route('worker.cards')
                ->with('error', 'Carta non trovata o non assegnata a questo dipendente.');
        }
        
        // Recupera le ricariche della carta
        $recharges = DB::table('credit_card_recharges')
                    ->where('credit_card_id', $id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
        
        // Trasformiamo l'oggetto stdClass in un oggetto più completo
        $creditCardObj = new \stdClass();
        $creditCardObj->id = $creditCard->id;
        $creditCardObj->numero_carta = $creditCard->numero_carta;
        $creditCardObj->scadenza_carta = $creditCard->scadenza_carta;
        $creditCardObj->fondo_carta = $creditCard->fondo_carta;
        $creditCardObj->created_at = $creditCard->created_at;
        $creditCardObj->updated_at = $creditCard->updated_at;
        $creditCardObj->pivot = new \stdClass();
        $creditCardObj->pivot->created_at = $creditCard->data_assegnazione;
        $creditCardObj->recharges = $recharges;
        
        return view('worker.cards.show', compact('creditCardObj', 'worker'));
    }
} 
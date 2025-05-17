<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashMovement;
use App\Models\CreditCard;
use App\Models\Work;
use App\Models\Worker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WorkerCashFlowController extends Controller
{
    /**
     * Mostra l'elenco dei movimenti di cassa del dipendente per la data odierna
     */
    public function index(Request $request)
    {
        // Recupera l'utente autenticato
        $user = Auth::user();
        
        // Cerca il worker associato all'utente usando la relazione
        $worker = $user->worker;
        
        if (!$worker) {
            return redirect()->route('dashboard')
                ->with('error', 'Profilo dipendente non trovato. Contatta l\'amministratore.');
        }
        
        // Ottiene la data selezionata o usa quella odierna
        $data = $request->input('data', Carbon::now()->toDateString());
        
        // Recupera i movimenti del giorno specificato
        $movimenti = CashMovement::where('worker_id', $worker->id)
                                ->whereDate('data_movimento', $data)
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        // Calcola il totale entrate, uscite e saldo giornaliero
        $totaleEntrate = $movimenti->where('tipo_movimento', 'entrata')->sum('importo');
        $totaleUscite = $movimenti->where('tipo_movimento', 'uscita')->sum('importo');
        $saldo = $totaleEntrate - $totaleUscite;
        
        // Saldo del fondo cassa
        $fondoCassa = $worker->fondo_cassa;
        
        return view('worker.cashflow.index', compact('movimenti', 'worker', 'data', 'totaleEntrate', 'totaleUscite', 'saldo', 'fondoCassa'));
    }
    
    /**
     * Mostra il form per registrare una nuova spesa
     */
    public function createSpesa()
    {
        // Recupera l'utente autenticato
        $user = Auth::user();
        
        // Cerca il worker associato all'utente usando la relazione
        $worker = $user->worker;
        
        if (!$worker) {
            return redirect()->route('dashboard')
                ->with('error', 'Profilo dipendente non trovato. Contatta l\'amministratore.');
        }
        
        // Recupera le carte assegnate al worker con i saldi aggiornati
        $carteAssegnate = $worker->assignedCreditCards()
                          ->select('credit_cards.*', 'credit_card_worker.created_at as data_assegnazione')
                          ->get();
        
        return view('worker.cashflow.spesa', compact('worker', 'carteAssegnate'));
    }
    
    /**
     * Salva una nuova spesa nel database
     */
    public function storeSpesa(Request $request)
    {
        // Recupera l'utente autenticato
        $user = Auth::user();
        $worker = $user->worker;
        
        if (!$worker) {
            return redirect()->route('dashboard')
                ->with('error', 'Profilo dipendente non trovato. Contatta l\'amministratore.');
        }
        
        // Recupera le carte assegnate al worker
        $carteAssegnate = $worker->assignedCreditCards()->get();
        
        // Regole di validazione base
        $rules = [
            'importo' => 'required|numeric|min:0.01',
            'motivo' => 'required|string|max:255',
            'metodo_pagamento' => 'required|in:contanti,dkv,carta',
        ];
        
        // Messaggi di errore
        $messages = [
            'importo.required' => 'L\'importo è obbligatorio',
            'importo.numeric' => 'L\'importo deve essere un numero',
            'importo.min' => 'L\'importo deve essere maggiore di zero',
            'motivo.required' => 'La motivazione è obbligatoria',
            'metodo_pagamento.required' => 'Il metodo di pagamento è obbligatorio',
        ];
        
        // Aggiungi regole specifiche solo se il metodo di pagamento è carta e ci sono più di una carta
        if ($request->metodo_pagamento === 'carta' && $carteAssegnate->count() > 1) {
            $rules['credit_card_id'] = 'required|exists:credit_cards,id';
            $messages['credit_card_id.required'] = 'La carta è obbligatoria per i pagamenti con carta';
            $messages['credit_card_id.exists'] = 'La carta selezionata non è valida';
        }
        
        // Esegui la validazione
        $request->validate($rules, $messages);
        
        // Inizia una transazione per garantire l'integrità dei dati
        DB::beginTransaction();
        
        try {
            // Gestisce il caso in cui si usa una carta
            $creditCardId = null;
            
            if ($request->metodo_pagamento === 'carta') {
                // Se c'è una sola carta assegnata, usa automaticamente quella carta
                if ($carteAssegnate->count() == 1 && empty($request->credit_card_id)) {
                    $creditCardId = $carteAssegnate->first()->id;
                } else {
                    $creditCardId = $request->credit_card_id;
                }
                
                // Verifica che sia stata selezionata una carta
                if (!$creditCardId) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'La carta è obbligatoria per i pagamenti con carta.');
                }
                
                // Verifica che la carta appartenga al worker
                $carta = CreditCard::find($creditCardId);
                if (!$worker->assignedCreditCards->contains($carta)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'La carta selezionata non è assegnata a questo dipendente.');
                }
                
                // Verifica che la carta abbia un saldo sufficiente
                if ($carta->fondo_carta < $request->importo) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'La carta selezionata non ha saldo sufficiente.');
                }
                
                // Scala l'importo dal saldo della carta
                $carta->fondo_carta -= $request->importo;
                $carta->save();
                
                // Registra la spesa anche nella tabella credit_card_recharges (come importo negativo)
                DB::table('credit_card_recharges')->insert([
                    'credit_card_id' => $creditCardId,
                    'user_id' => Auth::id(),
                    'importo' => -$request->importo, // Importo negativo per indicare una spesa
                    'data_ricarica' => Carbon::now(),
                    'note' => 'Spesa: ' . $request->motivo,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
            } elseif ($request->metodo_pagamento === 'contanti') {
                // Verifica che il fondo cassa sia sufficiente
                if ($worker->fondo_cassa < $request->importo) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Il fondo cassa non è sufficiente per questa spesa.');
                }
                
                // Scala l'importo dal fondo cassa
                $worker->fondo_cassa -= $request->importo;
                $worker->save();
            }
            
            // Crea il movimento di cassa
            CashMovement::create([
                'worker_id' => $worker->id,
                'tipo_movimento' => 'uscita',
                'importo' => $request->importo,
                'motivo' => $request->motivo,
                'metodo_pagamento' => $request->metodo_pagamento,
                'credit_card_id' => $creditCardId,
                'data_movimento' => Carbon::now()->toDateString(),
            ]);
            
            DB::commit();
            
            return redirect()->route('worker.cashflow')
                ->with('success', 'Spesa registrata con successo.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Errore durante il salvataggio della spesa: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Si è verificato un errore durante il salvataggio della spesa: ' . $e->getMessage());
        }
    }
    
    /**
     * Mostra il form per registrare un nuovo incasso
     */
    public function createIncasso()
    {
        // Recupera l'utente autenticato
        $user = Auth::user();
        
        // Cerca il worker associato all'utente usando la relazione
        $worker = $user->worker;
        
        if (!$worker) {
            return redirect()->route('dashboard')
                ->with('error', 'Profilo dipendente non trovato. Contatta l\'amministratore.');
        }
        
        // Recupera i lavori assegnati al worker
        $lavoriAssegnati = $worker->works;
        
        return view('worker.cashflow.incasso', compact('worker', 'lavoriAssegnati'));
    }
    
    /**
     * Salva un nuovo incasso nel database
     */
    public function storeIncasso(Request $request)
    {
        $request->validate([
            'importo' => 'required|numeric|min:0.01',
            'metodo_pagamento' => 'required|in:contanti,dkv,carta',
            'work_id' => 'required|exists:works,id',
        ], [
            'importo.required' => 'L\'importo è obbligatorio',
            'importo.numeric' => 'L\'importo deve essere un numero',
            'importo.min' => 'L\'importo deve essere maggiore di zero',
            'metodo_pagamento.required' => 'Il metodo di pagamento è obbligatorio',
            'work_id.required' => 'Il lavoro è obbligatorio',
        ]);
        
        // Recupera l'utente autenticato
        $user = Auth::user();
        $worker = $user->worker;
        
        if (!$worker) {
            return redirect()->route('dashboard')
                ->with('error', 'Profilo dipendente non trovato. Contatta l\'amministratore.');
        }
        
        // Verifica che il lavoro sia assegnato al worker
        $work = Work::find($request->work_id);
        if (!$worker->works->contains($work)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Il lavoro selezionato non è assegnato a questo dipendente.');
        }
        
        // Inizia una transazione per garantire l'integrità dei dati
        DB::beginTransaction();
        
        try {
            // Se il pagamento è in contanti, aggiorna il fondo cassa
            if ($request->metodo_pagamento === 'contanti') {
                $worker->fondo_cassa += $request->importo;
                $worker->save();
            }
            
            // Salva il movimento
            CashMovement::create([
                'worker_id' => $worker->id,
                'work_id' => $request->work_id,
                'tipo_movimento' => 'entrata',
                'importo' => $request->importo,
                'motivo' => 'Incasso per il lavoro #' . $request->work_id,
                'metodo_pagamento' => $request->metodo_pagamento,
                'data_movimento' => Carbon::now()->toDateString(),
            ]);
            
            DB::commit();
            
            return redirect()->route('worker.cashflow')
                ->with('success', 'Incasso registrato con successo.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Errore durante il salvataggio dell\'incasso: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Si è verificato un errore durante il salvataggio dell\'incasso: ' . $e->getMessage());
        }
    }
} 
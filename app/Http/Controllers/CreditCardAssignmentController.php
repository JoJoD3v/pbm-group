<?php

namespace App\Http\Controllers;

use App\Models\CreditCard;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditCardAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = DB::table('credit_card_worker')
            ->join('credit_cards', 'credit_card_worker.credit_card_id', '=', 'credit_cards.id')
            ->join('workers', 'credit_card_worker.worker_id', '=', 'workers.id')
            ->select('credit_card_worker.*', 'credit_cards.numero_carta', 'workers.name_worker', 'workers.cognome_worker')
            ->get();

        return view('credit_card_assignments.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $creditCards = CreditCard::all();
        $workers = Worker::all();
        return view('credit_card_assignments.create', compact('creditCards', 'workers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'credit_card_id' => 'required|exists:credit_cards,id',
            'worker_id' => 'required|exists:workers,id',
            'data_assegnazione' => 'required|date',
        ]);

        DB::table('credit_card_worker')->insert([
            'credit_card_id' => $request->credit_card_id,
            'worker_id' => $request->worker_id,
            'data_assegnazione' => $request->data_assegnazione,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('credit-card-assignments.index')
            ->with('success', 'Carta assegnata con successo');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $assignment = DB::table('credit_card_worker')
            ->join('credit_cards', 'credit_card_worker.credit_card_id', '=', 'credit_cards.id')
            ->join('workers', 'credit_card_worker.worker_id', '=', 'workers.id')
            ->select('credit_card_worker.*', 'credit_cards.numero_carta', 'workers.name_worker', 'workers.cognome_worker')
            ->where('credit_card_worker.id', $id)
            ->first();

        $creditCards = CreditCard::all();
        $workers = Worker::all();

        return view('credit_card_assignments.edit', compact('assignment', 'creditCards', 'workers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'credit_card_id' => 'required|exists:credit_cards,id',
            'worker_id' => 'required|exists:workers,id',
            'data_assegnazione' => 'required|date',
            'data_restituzione' => 'nullable|date|after:data_assegnazione',
        ]);

        DB::table('credit_card_worker')
            ->where('id', $id)
            ->update([
                'credit_card_id' => $request->credit_card_id,
                'worker_id' => $request->worker_id,
                'data_assegnazione' => $request->data_assegnazione,
                'data_restituzione' => $request->data_restituzione,
                'updated_at' => now(),
            ]);

        return redirect()->route('credit-card-assignments.index')
            ->with('success', 'Assegnazione aggiornata con successo');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::table('credit_card_worker')->where('id', $id)->delete();

        return redirect()->route('credit-card-assignments.index')
            ->with('success', 'Assegnazione eliminata con successo');
    }
}

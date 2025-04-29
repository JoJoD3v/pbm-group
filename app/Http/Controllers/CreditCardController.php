<?php

namespace App\Http\Controllers;

use App\Models\CreditCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditCardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $creditCards = CreditCard::with('assignedWorker')->get();
        return view('credit_cards.index', compact('creditCards'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('credit_cards.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero_carta' => 'required',
            'scadenza_carta' => 'required|date',
            'fondo_carta' => 'required|numeric|min:0',
        ]);

        CreditCard::create($request->all());
        return redirect()->route('credit-cards.index')->with('success', 'Carta prepagata creata con successo');
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
    public function edit(CreditCard $creditCard)
    {
        return view('credit_cards.edit', compact('creditCard'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CreditCard $creditCard)
    {
        $request->validate([
            'numero_carta' => 'required',
            'scadenza_carta' => 'required|date',
            'fondo_carta' => 'required|numeric|min:0',
        ]);

        $creditCard->update($request->all());
        return redirect()->route('credit-cards.index')->with('success', 'Carta prepagata aggiornata con successo');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CreditCard $creditCard)
    {
        $creditCard->delete();
        return redirect()->route('credit-cards.index')->with('success', 'Carta prepagata eliminata con successo');
    }
}

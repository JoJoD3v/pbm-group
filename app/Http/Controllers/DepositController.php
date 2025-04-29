<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Material;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    // Visualizza la lista dei depositi
    public function index()
    {
        $deposits = Deposit::with('materials')->get();
        return view('deposits.index', compact('deposits'));
    }

    // Mostra il form per creare un nuovo deposito
    public function create()
    {
        // Recupera tutti i Materials per le checkbox
        $materials = Material::all();
        return view('deposits.create', compact('materials'));
    }

    // Salva il nuovo deposito
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',            
        ]);

        $deposit = Deposit::create($request->only('name', 'address', 'latitude', 'longitude'));

        // Associa i materiali selezionati (se presenti)
        if ($request->has('materials')) {
            $deposit->materials()->sync($request->materials);
        }

        return redirect()->route('deposits.index')
                         ->with('success', 'Deposito creato con successo.');
    }

    // Mostra il form per modificare un deposito
    public function edit(Deposit $deposit)
    {
        $materials = Material::all();
        // Recupera gli id dei materiali associati
        $selectedMaterials = $deposit->materials->pluck('id')->toArray();
        return view('deposits.edit', compact('deposit', 'materials', 'selectedMaterials'));
    }

    // Aggiorna il deposito
    public function update(Request $request, Deposit $deposit)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $deposit->update($request->only('name', 'address','latitude','longitude'));

        // Aggiorna la relazione many-to-many
        if ($request->has('materials')) {
            $deposit->materials()->sync($request->materials);
        } else {
            // Se nessun materiale è selezionato, rimuovi tutte le associazioni
            $deposit->materials()->detach();
        }

        return redirect()->route('deposits.index')
                         ->with('success', 'Deposito aggiornato con successo.');
    }

    // Elimina il deposito
    public function destroy(Deposit $deposit)
    {
        $deposit->delete();
        return redirect()->route('deposits.index')
                         ->with('success', 'Deposito eliminato con successo.');
    }
}

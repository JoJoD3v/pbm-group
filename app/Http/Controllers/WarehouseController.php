<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    // Visualizza l'elenco dei cantieri
    public function index()
    {
        $warehouses = Warehouse::all();
        return view('warehouses.index', compact('warehouses'));
    }

    // Mostra il form per creare un nuovo cantiere
    public function create()
    {
        return view('warehouses.create');
    }

    // Salva un nuovo cantiere nel database
    public function store(Request $request)
    {
        $request->validate([
            'nome_sede'   => 'required|string|max:255',
            'indirizzo'   => 'required|string|max:255',
            'latitude_warehouse'  => 'nullable|numeric',
            'longitude_warehouse' => 'nullable|numeric',
        ]);

        Warehouse::create($request->all());
        return redirect()->route('warehouses.index')
                         ->with('success', 'Cantiere creato con successo.');
    }

    // Mostra il form per modificare un cantiere esistente
    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    // Aggiorna i dati del cantiere
    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'nome_sede'   => 'required|string|max:255',
            'indirizzo'   => 'required|string|max:255',
            'latitude_warehouse'  => 'nullable|numeric',
            'longitude_warehouse' => 'nullable|numeric',
        ]);

        $warehouse->update($request->all());
        return redirect()->route('warehouses.index')
                         ->with('success', 'Cantiere aggiornato con successo.');
    }

    // Elimina un cantiere
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->route('warehouses.index')
                         ->with('success', 'Cantiere eliminato con successo.');
    }
}

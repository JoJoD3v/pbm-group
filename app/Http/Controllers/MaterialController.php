<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;

class MaterialController extends Controller
{
    // Visualizza la lista dei materiali
    public function index()
    {
        $materials = Material::all();
        return view('materials.index', compact('materials'));
    }

    // Mostra il form per creare un nuovo materiale
    public function create()
    {
        return view('materials.create');
    }

    // Salva il nuovo materiale nel database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        Material::create($request->all());
        return redirect()->route('materials.index')
                         ->with('success', 'Materiale creato con successo.');
    }

    // Mostra il form per modificare un materiale esistente
    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    // Aggiorna il materiale nel database
    public function update(Request $request, Material $material)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $material->update($request->all());
        return redirect()->route('materials.index')
                         ->with('success', 'Materiale aggiornato con successo.');
    }

    // Elimina il materiale
    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('materials.index')
                         ->with('success', 'Materiale eliminato con successo.');
    }
}

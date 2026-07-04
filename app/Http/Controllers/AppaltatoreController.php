<?php

namespace App\Http\Controllers;

use App\Models\Appaltatore;
use Illuminate\Http\Request;

class AppaltatoreController extends Controller
{
    // Elenco degli appaltatori
    public function index()
    {
        $appaltatori = Appaltatore::all();

        return view('appaltatori.index', compact('appaltatori'));
    }

    // Mostra il form per creare un nuovo Appaltatore
    public function create()
    {
        return view('appaltatori.create');
    }

    // Mostra la scheda di un Appaltatore
    public function show(Appaltatore $appaltatore)
    {
        return view('appaltatori.show', compact('appaltatore'));
    }

    // Salva un nuovo Appaltatore
    public function store(Request $request)
    {
        if ($request->tipo_soggetto == 'fisica') {
            $request->validate([
                'tipo_soggetto' => 'required|in:fisica,giuridica',
                'full_name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
                'codice_fiscale' => 'required|string|max:50',
                'latitude_appaltatore' => 'nullable|numeric',
                'longitude_appaltatore' => 'nullable|numeric',
            ]);
        } else { // giuridica
            $request->validate([
                'tipo_soggetto' => 'required|in:fisica,giuridica',
                'ragione_sociale' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
                'partita_iva' => 'required|string|max:50',
                'codice_fiscale' => 'nullable|string|max:50',
                'latitude_appaltatore' => 'nullable|numeric',
                'longitude_appaltatore' => 'nullable|numeric',
            ]);
        }

        Appaltatore::create($request->all());

        return redirect()->route('appaltatori.index')
            ->with('success', 'Appaltatore creato con successo.');
    }

    // Mostra il form per modificare un Appaltatore
    public function edit(Appaltatore $appaltatore)
    {
        return view('appaltatori.edit', compact('appaltatore'));
    }

    // Aggiorna un Appaltatore
    public function update(Request $request, Appaltatore $appaltatore)
    {
        if ($request->tipo_soggetto == 'fisica') {
            $request->validate([
                'tipo_soggetto' => 'required|in:fisica,giuridica',
                'full_name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
                'codice_fiscale' => 'required|string|max:50',
                'latitude_appaltatore' => 'nullable|numeric',
                'longitude_appaltatore' => 'nullable|numeric',
            ]);
        } else { // giuridica
            $request->validate([
                'tipo_soggetto' => 'required|in:fisica,giuridica',
                'ragione_sociale' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
                'partita_iva' => 'required|string|max:50',
                'codice_fiscale' => 'nullable|string|max:50',
                'latitude_appaltatore' => 'nullable|numeric',
                'longitude_appaltatore' => 'nullable|numeric',
            ]);
        }

        $appaltatore->update($request->all());

        return redirect()->route('appaltatori.index')
            ->with('success', 'Appaltatore aggiornato con successo.');
    }

    // Elimina un Appaltatore
    public function destroy(Appaltatore $appaltatore)
    {
        $appaltatore->delete();

        return redirect()->route('appaltatori.index')
            ->with('success', 'Appaltatore eliminato con successo.');
    }
}

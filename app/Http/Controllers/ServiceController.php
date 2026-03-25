<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('nome_servizio')->get();
        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_servizio'   => 'required|string|max:255',
            'prezzo_servizio' => 'nullable|numeric|min:0',
        ]);

        Service::create($request->only('nome_servizio', 'prezzo_servizio'));

        return redirect()->route('services.index')
                         ->with('success', 'Servizio aggiunto con successo.');
    }

    public function show(Service $service)
    {
        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'nome_servizio'   => 'required|string|max:255',
            'prezzo_servizio' => 'nullable|numeric|min:0',
        ]);

        $service->update($request->only('nome_servizio', 'prezzo_servizio'));

        return redirect()->route('services.index')
                         ->with('success', 'Servizio aggiornato con successo.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')
                         ->with('success', 'Servizio eliminato con successo.');
    }
}

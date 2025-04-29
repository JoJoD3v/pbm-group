<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Vehicle::all();
        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'targa' => 'required|string|max:20|unique:vehicles',
            'scadenza_assicurazione' => 'nullable|date',
        ]);

        Vehicle::create($request->all());

        return redirect()->route('vehicles.index')
                         ->with('success', 'Automezzo creato con successo.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        return view('vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'targa' => 'required|string|max:20|unique:vehicles,targa,' . $vehicle->id,
            'scadenza_assicurazione' => 'nullable|date',
        ]);

        $vehicle->update($request->all());

        return redirect()->route('vehicles.index')
                         ->with('success', 'Automezzo aggiornato con successo.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')
                         ->with('success', 'Automezzo eliminato con successo.');
    }
}

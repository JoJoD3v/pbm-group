<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Customer;
use App\Models\Material;
use App\Models\Warehouse;
use App\Models\Deposit;

class WorkController extends Controller
{
    public function index()
    {
        $works = Work::with('customer')->get();
        return view('works.index', compact('works'));
    }

    public function create()
    {
        // Recupera dati utili per i select:
        $customers = Customer::all();
        $materials = Material::all();
        $warehouses = Warehouse::all();
        return view('works.create', compact('customers', 'materials', 'warehouses'));
    }
    
    public function createDisposal()
    {
        // Recupera dati utili per i select:
        $customers = Customer::all();
        $materials = Material::with('deposits')->get();
        $warehouses = Warehouse::all();
        return view('works.create_disposal', compact('customers', 'materials', 'warehouses'));
    }
    
    /**
     * Ottieni i depositi associati a un materiale specifico
     */
    public function getDepositsByMaterial($materialId)
    {
        $material = Material::with('deposits')->findOrFail($materialId);
        return response()->json($material->deposits);
    }

    public function store(Request $request)
    {
        // La validazione può essere complessa in base alle opzioni scelte; qui un esempio base:
        $request->validate([
            'tipo_lavoro'           => 'required|string|max:255',
            'customer_id'           => 'required|exists:customers,id',
            'nome_partenza'         => 'nullable|string|max:255',
            'indirizzo_partenza'    => 'nullable|string|max:255',
            'nome_destinazione'     => 'required|string|max:255',
            'indirizzo_destinazione'=> 'required|string|max:255',
            'data_esecuzione'       => 'nullable|date',
            'costo_lavoro'          => 'nullable|numeric',
            'modalita_pagamento'    => 'nullable|string|max:255',
            'phone'                 => 'nullable', // se necessario
            // Altri campi vanno validati secondo la logica della app
        ]);

        // Se è stato usato il materiale libero, usa quel campo; altrimenti, se si è scelto un materiale registrato,
        // si potrebbe recuperare il nome e il codice eer dal materiale selezionato.
        if($request->materiale_option == 'registrato'){
            // Imposta "materiale" e "codice_eer" in base al materiale selezionato
            $material = Material::find($request->material_id);
            $dataMateriale = [
                'materiale'   => $material ? $material->name : null,
                'codice_eer'  => $material ? $material->eer_code : null,
            ];
        } else {
            $dataMateriale = [
                'materiale'  => $request->input('materiale_libero'),
                'codice_eer' => null,
            ];
        }

        // Per il "nome destinazione" la logica viene gestita dal form (la select indica quale opzione è stata scelta)
        // e, in alcuni casi, i campi indirizzo, latitude e longitude vengono auto-riempiti.

        $work = Work::create(array_merge($request->all(), $dataMateriale));

        return redirect()->route('works.index')
                         ->with('success', 'Work creato con successo.');
    }

    public function show(Work $work)
    {
        return view('works.show', compact('work'));
    }
    
    public function edit(Work $work)
    {
        $customers = Customer::all();
        $materials = Material::all();
        $warehouses = Warehouse::all();
        return view('works.edit', compact('work', 'customers', 'materials', 'warehouses'));
    }

    public function update(Request $request, Work $work)
    {
        // Validazione simile a store (da adattare)
        $request->validate([
            'tipo_lavoro'           => 'required|string|max:255',
            'customer_id'           => 'required|exists:customers,id',
            'nome_partenza'         => 'nullable|string|max:255',
            'indirizzo_partenza'    => 'nullable|string|max:255',
            'nome_destinazione'     => 'required|string|max:255',
            'indirizzo_destinazione'=> 'required|string|max:255',
            'data_esecuzione'       => 'nullable|date',
            'costo_lavoro'          => 'nullable|numeric',
            'modalita_pagamento'    => 'nullable|string|max:255',
        ]);

        if($request->materiale_option == 'registrato'){
            $material = Material::find($request->material_id);
            $dataMateriale = [
                'materiale'   => $material ? $material->name : null,
                'codice_eer'  => $material ? $material->eer_code : null,
            ];
        } else {
            $dataMateriale = [
                'materiale'  => $request->input('materiale_libero'),
                'codice_eer' => null,
            ];
        }

        $work->update(array_merge($request->all(), $dataMateriale));

        return redirect()->route('works.index')
                         ->with('success', 'Work aggiornato con successo.');
    }

    public function destroy(Work $work)
    {
        $work->delete();
        return redirect()->route('works.index')
                         ->with('success', 'Work eliminato con successo.');
    }
}

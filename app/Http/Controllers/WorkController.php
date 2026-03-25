<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Customer;
use App\Models\Material;
use App\Models\Warehouse;
use App\Models\Deposit;
use Carbon\Carbon;

class WorkController extends Controller
{
    private function buildFilteredQuery(Request $request)
    {
        $query = Work::with(['customer', 'workers']);

        // Filtro per data inizio
        if ($request->filled('data_inizio')) {
            $query->whereDate('data_esecuzione', '>=', $request->data_inizio);
        }
        
        // Filtro per data fine
        if ($request->filled('data_fine')) {
            $query->whereDate('data_esecuzione', '<=', $request->data_fine);
        }

        // Filtro per tipo di lavoro
        if ($request->filled('tipo_lavoro')) {
            $query->where('tipo_lavoro', $request->tipo_lavoro);
        }

        return $query->orderBy('data_esecuzione', 'desc')
                     ->orderBy('created_at', 'desc');
    }

    public function index(Request $request)
    {
        $works = $this->buildFilteredQuery($request)->get();
        return view('works.index', [
            'works' => $works,
            'pageTitle' => 'Elenco Lavori',
            'indexRoute' => 'works.index',
        ]);
    }

    public function assigned(Request $request)
    {
        $works = $this->buildFilteredQuery($request)
            ->whereHas('workers')
            ->get();

        return view('works.index', [
            'works' => $works,
            'pageTitle' => 'Elenco Lavori Assegnati',
            'indexRoute' => 'works.assigned',
            'showAssignedWorkerColumn' => true,
        ]);
    }

    public function unassigned(Request $request)
    {
        $works = $this->buildFilteredQuery($request)
            ->whereDoesntHave('workers')
            ->get();

        return view('works.index', [
            'works' => $works,
            'pageTitle' => 'Elenco Lavori Non Assegnati',
            'indexRoute' => 'works.unassigned',
        ]);
    }

    public function statuses(Request $request)
    {
        $ids = $request->input('ids', []);
        $ids = is_array($ids) ? $ids : [];
        
        if (empty($ids)) {
            return response()->json([
                'server_time' => now()->toIso8601String(),
                'statuses' => [],
            ]);
        }

        $statuses = Work::whereIn('id', $ids)
            ->get(['id', 'status_lavoro'])
            ->mapWithKeys(function ($work) {
                return [$work->id => $work->status_lavoro];
            });

        return response()->json([
            'server_time' => now()->toIso8601String(),
            'statuses' => $statuses,
        ]);
    }

    public function create()
    {
        // Recupera dati utili per i select:
        $customers = Customer::all()->sortBy(fn($c) => $c->customer_type === 'fisica' ? $c->full_name : $c->ragione_sociale)->values();
        $materials = Material::all();
        $warehouses = Warehouse::all();
        return view('works.create', compact('customers', 'materials', 'warehouses'));
    }
    
    public function createDisposal()
    {
        // Recupera dati utili per i select:
        $customers = Customer::all()->sortBy(fn($c) => $c->customer_type === 'fisica' ? $c->full_name : $c->ragione_sociale)->values();
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

        $dataEsecuzione = $request->filled('data_esecuzione')
            ? Carbon::parse($request->data_esecuzione)->format('Y-m-d H:i:s')
            : null;

        $work = Work::create(array_merge(
            $request->all(),
            $dataMateriale,
            ['data_esecuzione' => $dataEsecuzione]
        ));

        return redirect()->route('works.index')
                         ->with('success', 'Work creato con successo.');
    }

    public function show(Work $work)
    {
        // Carica le ricevute associate al lavoro
        $work->load('ricevute');
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

        $dataEsecuzione = $request->filled('data_esecuzione')
            ? Carbon::parse($request->data_esecuzione)->format('Y-m-d H:i:s')
            : null;

        $work->update(array_merge(
            $request->all(),
            $dataMateriale,
            ['data_esecuzione' => $dataEsecuzione]
        ));

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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Worker;
use App\Models\VehicleAssignmentLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VehicleAssignmentController extends Controller
{
    /**
     * Mostra l'elenco delle assegnazioni di automezzi.
     */
    public function index()
    {
        $assignments = DB::table('vehicle_worker')
            ->join('vehicles', 'vehicle_worker.vehicle_id', '=', 'vehicles.id')
            ->join('workers', 'vehicle_worker.worker_id', '=', 'workers.id')
            ->whereNull('vehicle_worker.data_restituzione')
            ->select(
                'vehicle_worker.*',
                'vehicles.nome as vehicle_nome',
                'vehicles.targa',
                'workers.name_worker',
                'workers.cognome_worker'
            )
            ->get();

        return view('vehicles.assignments.index', compact('assignments'));
    }

    /**
     * Mostra il form per creare una nuova assegnazione.
     */
    public function create()
    {
        $vehicles = Vehicle::all();
        $workers = Worker::all();
        
        return view('vehicles.assignments.create', compact('vehicles', 'workers'));
    }

    /**
     * Salva una nuova assegnazione nel database.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'vehicle_id' => 'required|exists:vehicles,id',
                'worker_id' => 'required|exists:workers,id',
                'data_assegnazione' => 'required|date',
                'ora_assegnazione' => 'required',
                'note' => 'nullable|string'
            ]);

            // Combina data e ora
            $dataOraAssegnazione = Carbon::parse($request->data_assegnazione . ' ' . $request->ora_assegnazione);

            Log::info('Inizio creazione assegnazione', [
                'vehicle_id' => $request->vehicle_id,
                'worker_id' => $request->worker_id,
                'data_assegnazione' => $dataOraAssegnazione
            ]);

            DB::beginTransaction();

            // Inserisci l'assegnazione in vehicle_worker
            $vehicleWorkerId = DB::table('vehicle_worker')->insertGetId([
                'vehicle_id' => $request->vehicle_id,
                'worker_id' => $request->worker_id,
                'data_assegnazione' => $dataOraAssegnazione,
                'note' => $request->note,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Assegnazione creata in vehicle_worker', ['id' => $vehicleWorkerId]);

            // Salva il log in vehicle_assignment_logs
            $logId = DB::table('vehicle_assignment_logs')->insertGetId([
                'vehicle_id' => $request->vehicle_id,
                'worker_id' => $request->worker_id,
                'data_assegnazione' => $dataOraAssegnazione,
                'note' => $request->note,
                'operazione' => 'assegnazione',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Log creato in vehicle_assignment_logs', ['id' => $logId]);

            DB::commit();
            Log::info('Transazione completata con successo');

            return redirect()->route('vehicle.assignments.index')
                ->with('success', 'Assegnazione creata con successo');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Errore di validazione', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Errore durante la creazione dell\'assegnazione', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Errore durante la creazione dell\'assegnazione: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostra il form per modificare un'assegnazione.
     */
    public function edit(Vehicle $vehicle, Worker $worker)
    {
        $assignment = DB::table('vehicle_worker')
            ->where('vehicle_id', $vehicle->id)
            ->where('worker_id', $worker->id)
            ->whereNull('data_restituzione')
            ->first();

        if (!$assignment) {
            return back()->with('error', 'Assegnazione non trovata');
        }

        $vehicles = Vehicle::all();
        $workers = Worker::all();

        return view('vehicles.assignments.edit', compact('vehicle', 'worker', 'assignment', 'vehicles', 'workers'));
    }

    /**
     * Aggiorna un'assegnazione nel database.
     */
    public function update(Request $request, Vehicle $vehicle, Worker $worker)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'worker_id' => 'required|exists:workers,id',
            'data_assegnazione' => 'required|date',
            'note' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Aggiorna l'assegnazione in vehicle_worker
            DB::table('vehicle_worker')
                ->where('vehicle_id', $vehicle->id)
                ->where('worker_id', $worker->id)
                ->whereNull('data_restituzione')
                ->update([
                    'vehicle_id' => $request->vehicle_id,
                    'worker_id' => $request->worker_id,
                    'data_assegnazione' => $request->data_assegnazione,
                    'note' => $request->note,
                    'updated_at' => now()
                ]);

            // Salva il log di modifica in vehicle_assignment_logs
            DB::table('vehicle_assignment_logs')->insert([
                'vehicle_id' => $request->vehicle_id,
                'worker_id' => $request->worker_id,
                'data_assegnazione' => $request->data_assegnazione,
                'note' => $request->note,
                'operazione' => 'modifica',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();
            return redirect()->route('vehicle.assignments.index')->with('success', 'Assegnazione aggiornata con successo');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Errore durante l\'aggiornamento dell\'assegnazione');
        }
    }

    /**
     * Rimuove un'assegnazione dal database.
     */
    public function destroy(Vehicle $vehicle, Worker $worker)
    {
        DB::beginTransaction();
        try {
            // Recupera i dati dell'assegnazione prima di eliminarla
            $assignment = DB::table('vehicle_worker')
                ->where('vehicle_id', $vehicle->id)
                ->where('worker_id', $worker->id)
                ->whereNull('data_restituzione')
                ->first();

            if ($assignment) {
                // Aggiorna la data di restituzione in vehicle_worker
                DB::table('vehicle_worker')
                    ->where('vehicle_id', $vehicle->id)
                    ->where('worker_id', $worker->id)
                    ->whereNull('data_restituzione')
                    ->update(['data_restituzione' => now()]);

                // Salva il log di restituzione in vehicle_assignment_logs
                DB::table('vehicle_assignment_logs')->insert([
                    'vehicle_id' => $vehicle->id,
                    'worker_id' => $worker->id,
                    'data_assegnazione' => $assignment->data_assegnazione,
                    'data_restituzione' => now(),
                    'note' => $assignment->note,
                    'operazione' => 'restituzione',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
            return redirect()->route('vehicle.assignments.index')->with('success', 'Assegnazione terminata con successo');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Errore durante la terminazione dell\'assegnazione');
        }
    }
}

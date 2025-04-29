<?php

namespace App\Http\Controllers;

use App\Models\VehicleAssignmentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VehicleAssignmentReportController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('vehicle_assignment_logs')
            ->join('vehicles', 'vehicle_assignment_logs.vehicle_id', '=', 'vehicles.id')
            ->join('workers', 'vehicle_assignment_logs.worker_id', '=', 'workers.id')
            ->select(
                'vehicle_assignment_logs.*',
                'vehicles.nome as vehicle_nome',
                'vehicles.targa',
                'workers.name_worker',
                'workers.cognome_worker'
            );

        // Applica i filtri per data se presenti
        if ($request->filled('data_inizio')) {
            $dataInizio = Carbon::parse($request->data_inizio)->startOfDay();
            $query->where('vehicle_assignment_logs.data_assegnazione', '>=', $dataInizio);
        }

        if ($request->filled('data_fine')) {
            $dataFine = Carbon::parse($request->data_fine)->endOfDay();
            $query->where('vehicle_assignment_logs.data_assegnazione', '<=', $dataFine);
        }

        $assignments = $query->orderBy('vehicle_assignment_logs.data_assegnazione', 'desc')->get();

        return view('vehicles.assignments.report', compact('assignments'));
    }

    public function destroy($id)
    {
        try {
            // Verifica se il log esiste
            $log = DB::table('vehicle_assignment_logs')->where('id', $id)->first();
            
            if (!$log) {
                return redirect()->route('vehicle.assignments.report')
                    ->with('error', 'Log non trovato');
            }

            // Elimina il log
            DB::table('vehicle_assignment_logs')->where('id', $id)->delete();

            return redirect()->route('vehicle.assignments.report')
                ->with('success', 'Log eliminato con successo');
        } catch (\Exception $e) {
            \Log::error('Errore durante l\'eliminazione del log: ' . $e->getMessage());
            return redirect()->route('vehicle.assignments.report')
                ->with('error', 'Errore durante l\'eliminazione del log');
        }
    }
} 
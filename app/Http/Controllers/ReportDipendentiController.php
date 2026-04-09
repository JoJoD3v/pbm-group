<?php

namespace App\Http\Controllers;

use App\Models\CashMovement;
use App\Models\Worker;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportDipendentiController extends Controller
{
    /**
     * Mostra il form di selezione per il report dipendente.
     */
    public function index()
    {
        if (Auth::user()->role === 'dipendente') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        $workers = Worker::orderBy('name_worker')->get();

        return view('reports.dipendenti.index', compact('workers'));
    }

    /**
     * Genera il report per il dipendente selezionato.
     */
    public function generate(Request $request)
    {
        if (Auth::user()->role === 'dipendente') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'data_inizio' => 'required|date',
            'data_fine' => 'required|date|after_or_equal:data_inizio',
        ], [
            'worker_id.required' => 'Seleziona un dipendente.',
            'worker_id.exists' => 'Dipendente non valido.',
            'data_inizio.required' => 'Seleziona la data di inizio.',
            'data_inizio.date' => 'Data di inizio non valida.',
            'data_fine.required' => 'Seleziona la data di fine.',
            'data_fine.date' => 'Data di fine non valida.',
            'data_fine.after_or_equal' => 'La data di fine deve essere uguale o successiva alla data di inizio.',
        ]);

        $worker = Worker::findOrFail($request->worker_id);
        $dataInizio = $request->data_inizio;
        $dataFine = $request->data_fine;

        $data = $this->buildReportData($worker, $dataInizio, $dataFine);

        return view('reports.dipendenti.report', array_merge($data, [
            'worker' => $worker,
            'dataInizio' => $dataInizio,
            'dataFine' => $dataFine,
            'workers' => Worker::orderBy('name_worker')->get(),
        ]));
    }

    /**
     * Esporta il report dipendente in PDF.
     */
    public function pdf(Request $request)
    {
        if (Auth::user()->role === 'dipendente') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        $request->validate([
            'worker_id' => 'required|exists:workers,id',
            'data_inizio' => 'required|date',
            'data_fine' => 'required|date|after_or_equal:data_inizio',
        ]);

        $worker = Worker::findOrFail($request->worker_id);
        $dataInizio = $request->data_inizio;
        $dataFine = $request->data_fine;

        $data = $this->buildReportData($worker, $dataInizio, $dataFine);

        $html = view('pdf.report_dipendente', array_merge($data, [
            'worker' => $worker,
            'dataInizio' => $dataInizio,
            'dataFine' => $dataFine,
        ]))->render();

        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'report_dipendente_'.$worker->id.'_'.$dataInizio.'_'.$dataFine.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Raccoglie tutti i dati del report per un worker nel periodo dato.
     *
     * @return array{lavori: Collection, totaleLavori: int, totaleCosto: float, movimentiCassa: Collection, totaleEntrateCassa: float, totaleUsciteCassa: float, saldoCassa: float, ricaricheCarta: Collection, movimentiCarta: Collection, totalRicaricheCarta: float, totaleUsoCarta: float, cartaAssegnata: Model|null}
     */
    private function buildReportData(Worker $worker, string $dataInizio, string $dataFine): array
    {
        // Lavori assegnati al dipendente nel periodo
        $lavori = $worker->works()
            ->with('customer')
            ->whereBetween('data_esecuzione', [
                Carbon::parse($dataInizio)->startOfDay(),
                Carbon::parse($dataFine)->endOfDay(),
            ])
            ->orderBy('data_esecuzione')
            ->get();

        $totaleLavori = $lavori->count();
        $totaleCosto = $lavori->sum('costo_lavoro');

        // Movimenti fondo cassa
        $movimentiCassa = CashMovement::where('worker_id', $worker->id)
            ->whereBetween('data_movimento', [$dataInizio, $dataFine])
            ->orderBy('data_movimento')
            ->orderBy('created_at')
            ->get();

        $totaleEntrateCassa = $movimentiCassa->where('tipo_movimento', 'entrata')->sum('importo');
        $totaleUsciteCassa = $movimentiCassa->where('tipo_movimento', 'uscita')->sum('importo');
        $saldoCassa = $totaleEntrateCassa - $totaleUsciteCassa;

        // Carta di credito assegnata
        $cartaAssegnata = DB::table('credit_card_worker')
            ->where('worker_id', $worker->id)
            ->whereNull('data_restituzione')
            ->join('credit_cards', 'credit_card_worker.credit_card_id', '=', 'credit_cards.id')
            ->select('credit_cards.*')
            ->first();

        // Ricariche carta nel periodo
        $ricaricheCarta = collect([]);
        $totalRicaricheCarta = 0;

        if ($cartaAssegnata) {
            $ricaricheCarta = DB::table('credit_card_recharges')
                ->where('credit_card_id', $cartaAssegnata->id)
                ->whereBetween('data_ricarica', [$dataInizio, $dataFine])
                ->orderBy('data_ricarica')
                ->get();

            $totalRicaricheCarta = $ricaricheCarta->sum('importo');
        }

        // Movimenti eseguiti con la carta (pagamenti dei lavori con carta)
        $movimentiCarta = CashMovement::where('worker_id', $worker->id)
            ->whereNotNull('credit_card_id')
            ->whereBetween('data_movimento', [$dataInizio, $dataFine])
            ->orderBy('data_movimento')
            ->get();

        $totaleUsoCarta = $movimentiCarta->sum('importo');

        return [
            'lavori' => $lavori,
            'totaleLavori' => $totaleLavori,
            'totaleCosto' => $totaleCosto,
            'movimentiCassa' => $movimentiCassa,
            'totaleEntrateCassa' => $totaleEntrateCassa,
            'totaleUsciteCassa' => $totaleUsciteCassa,
            'saldoCassa' => $saldoCassa,
            'cartaAssegnata' => $cartaAssegnata,
            'ricaricheCarta' => $ricaricheCarta,
            'movimentiCarta' => $movimentiCarta,
            'totalRicaricheCarta' => $totalRicaricheCarta,
            'totaleUsoCarta' => $totaleUsoCarta,
        ];
    }
}

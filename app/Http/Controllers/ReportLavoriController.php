<?php

namespace App\Http\Controllers;

use App\Models\Work;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ReportLavoriController extends Controller
{
    /**
     * Mostra il form di filtro per il report lavori.
     */
    public function index()
    {
        if (Auth::user()->role === 'dipendente') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        return view('reports.lavori.index');
    }

    /**
     * Genera il report lavori nel periodo selezionato.
     */
    public function generate(Request $request)
    {
        if (Auth::user()->role === 'dipendente') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        $request->validate([
            'data_inizio' => 'required|date',
            'data_fine' => 'required|date|after_or_equal:data_inizio',
        ], [
            'data_inizio.required' => 'Seleziona la data di inizio.',
            'data_inizio.date' => 'Data di inizio non valida.',
            'data_fine.required' => 'Seleziona la data di fine.',
            'data_fine.date' => 'Data di fine non valida.',
            'data_fine.after_or_equal' => 'La data di fine deve essere uguale o successiva alla data di inizio.',
        ]);

        $dataInizio = $request->data_inizio;
        $dataFine = $request->data_fine;

        $data = $this->buildReportData($dataInizio, $dataFine);

        return view('reports.lavori.report', array_merge($data, [
            'dataInizio' => $dataInizio,
            'dataFine' => $dataFine,
        ]));
    }

    /**
     * Esporta il report lavori in PDF.
     */
    public function pdf(Request $request)
    {
        if (Auth::user()->role === 'dipendente') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        $request->validate([
            'data_inizio' => 'required|date',
            'data_fine' => 'required|date|after_or_equal:data_inizio',
        ]);

        $dataInizio = $request->data_inizio;
        $dataFine = $request->data_fine;

        $data = $this->buildReportData($dataInizio, $dataFine);

        $html = view('pdf.report_lavori', array_merge($data, [
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

        $filename = 'report_lavori_'.$dataInizio.'_'.$dataFine.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Raccoglie tutti i dati del report lavori nel periodo dato.
     *
     * @return array{lavori: Collection, totaleLavori: int, totaleCompensoPagato: float, totaleCompensoTotale: float, lavoriPerStatus: Collection}
     */
    private function buildReportData(string $dataInizio, string $dataFine): array
    {
        $lavori = Work::with(['customer', 'workers'])
            ->whereBetween('data_esecuzione', [
                Carbon::parse($dataInizio)->startOfDay(),
                Carbon::parse($dataFine)->endOfDay(),
            ])
            ->orderBy('data_esecuzione')
            ->get();

        $totaleLavori = $lavori->count();
        $totaleCompensoTotale = $lavori->sum('costo_lavoro');

        // Compensi incassati (lavori completati o conclusi)
        $totaleCompensoPagato = $lavori
            ->whereIn('status_lavoro', ['Lavoro Completato', 'Concluso'])
            ->sum('costo_lavoro');

        // Raggruppamento per status
        $lavoriPerStatus = $lavori->groupBy('status_lavoro');

        return [
            'lavori' => $lavori,
            'totaleLavori' => $totaleLavori,
            'totaleCompensoTotale' => $totaleCompensoTotale,
            'totaleCompensoPagato' => $totaleCompensoPagato,
            'lavoriPerStatus' => $lavoriPerStatus,
        ];
    }
}

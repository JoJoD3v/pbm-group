<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Work;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ReportClientiController extends Controller
{
    /**
     * Mostra il form di filtro per il report clienti.
     */
    public function index()
    {
        if (Auth::user()->role === 'dipendente') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        $customers = Customer::all()->sortBy(fn ($c) => $c->customer_type === 'fisica' ? $c->full_name : $c->ragione_sociale)->values();

        return view('reports.clienti.index', ['customers' => $customers]);
    }

    /**
     * Genera il report lavori per il cliente selezionato.
     */
    public function generate(Request $request)
    {
        if (Auth::user()->role === 'dipendente') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'data_inizio' => 'nullable|date',
            'data_fine' => 'nullable|date|after_or_equal:data_inizio',
        ], [
            'customer_id.required' => 'Seleziona un cliente.',
            'customer_id.exists' => 'Cliente non valido.',
            'data_inizio.date' => 'La data di inizio non è valida.',
            'data_fine.date' => 'La data di fine non è valida.',
            'data_fine.after_or_equal' => 'La data di fine deve essere successiva o uguale alla data di inizio.',
        ]);

        $customerId = $request->customer_id;
        $dataInizio = $request->data_inizio;
        $dataFine = $request->data_fine;

        $data = $this->buildReportData($customerId, $dataInizio, $dataFine);

        return view('reports.clienti.report', array_merge($data, [
            'customerId' => $customerId,
            'dataInizio' => $dataInizio,
            'dataFine' => $dataFine,
        ]));
    }

    /**
     * Esporta il report clienti in PDF.
     */
    public function pdf(Request $request)
    {
        if (Auth::user()->role === 'dipendente') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'data_inizio' => 'nullable|date',
            'data_fine' => 'nullable|date|after_or_equal:data_inizio',
        ]);

        $customerId = $request->customer_id;
        $dataInizio = $request->data_inizio;
        $dataFine = $request->data_fine;

        $data = $this->buildReportData($customerId, $dataInizio, $dataFine);

        $html = view('pdf.report_clienti', array_merge($data, [
            'customerId' => $customerId,
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

        $filename = 'report_cliente_'.$customerId.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Raccoglie tutti i dati del report per il cliente dato.
     *
     * @return array{customer: Customer, lavori: Collection, totaleLavori: int, totaleCompensoTotale: float, lavoriPerStatus: Collection}
     */
    private function buildReportData(int $customerId, ?string $dataInizio = null, ?string $dataFine = null): array
    {
        $customer = Customer::findOrFail($customerId);

        $lavori = Work::with(['customer', 'workers'])
            ->where('customer_id', $customerId)
            ->when($dataInizio, fn ($q) => $q->where('data_esecuzione', '>=', Carbon::parse($dataInizio)->startOfDay()))
            ->when($dataFine, fn ($q) => $q->where('data_esecuzione', '<=', Carbon::parse($dataFine)->endOfDay()))
            ->orderByDesc('data_esecuzione')
            ->get();

        $totaleLavori = $lavori->count();
        $totaleCompensoTotale = $lavori->sum('costo_lavoro');

        $lavoriPerStatus = $lavori->groupBy('status_lavoro');

        return [
            'customer' => $customer,
            'lavori' => $lavori,
            'totaleLavori' => $totaleLavori,
            'totaleCompensoTotale' => $totaleCompensoTotale,
            'lavoriPerStatus' => $lavoriPerStatus,
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Work;
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
        ], [
            'customer_id.required' => 'Seleziona un cliente.',
            'customer_id.exists' => 'Cliente non valido.',
        ]);

        $customerId = $request->customer_id;

        $data = $this->buildReportData($customerId);

        return view('reports.clienti.report', array_merge($data, [
            'customerId' => $customerId,
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
        ]);

        $customerId = $request->customer_id;

        $data = $this->buildReportData($customerId);

        $html = view('pdf.report_clienti', array_merge($data, [
            'customerId' => $customerId,
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
    private function buildReportData(int $customerId): array
    {
        $customer = Customer::findOrFail($customerId);

        $lavori = Work::with(['customer', 'workers'])
            ->where('customer_id', $customerId)
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

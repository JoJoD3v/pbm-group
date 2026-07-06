<?php

namespace App\Http\Controllers;

use App\Models\CashMovement;
use App\Models\ChiusuraGiorno;
use App\Models\Worker;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChiusuraGiornoController extends Controller
{
    private function guardAdmin()
    {
        $role = strtolower(Auth::user()->role ?? '');
        if ($role !== 'sviluppatore') {
            return redirect()->route('dashboard')
                ->with('error', 'Non hai i permessi per accedere a questa risorsa.');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->guardAdmin()) {
            return $redirect;
        }

        $chiusure = ChiusuraGiorno::withCount('righe')
            ->with('creator')
            ->orderByDesc('data_chiusura')
            ->get();

        return view('chiusure.index', compact('chiusure'));
    }

    public function create()
    {
        if ($redirect = $this->guardAdmin()) {
            return $redirect;
        }

        return view('chiusure.create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->guardAdmin()) {
            return $redirect;
        }

        $request->validate([
            'data_chiusura' => 'required|date',
        ], [
            'data_chiusura.required' => 'Seleziona la data della chiusura.',
            'data_chiusura.date' => 'La data non è valida.',
        ]);

        $data = Carbon::parse($request->data_chiusura)->startOfDay();

        if (ChiusuraGiorno::whereDate('data_chiusura', $data)->exists()) {
            return back()->with('error', 'Esiste già una chiusura per questa data.');
        }

        $chiusura = DB::transaction(function () use ($data) {
            $chiusura = ChiusuraGiorno::create([
                'data_chiusura' => $data,
                'created_by' => Auth::id(),
            ]);

            foreach ($this->workersConAttivita($data) as $worker) {
                $aperturaCassa = $this->aperturaFondoCassa($worker, $data);
                $aperturaCarta = $this->aperturaCarta($worker, $data);

                [$deltaCassa, $deltaCarta] = $this->deltaDelGiorno($worker, $data);

                $chiusura->righe()->create([
                    'worker_id' => $worker->id,
                    'apertura_fondo_cassa' => $aperturaCassa,
                    'apertura_carta' => $aperturaCarta,
                    'chiusura_fondo_cassa' => $aperturaCassa + $deltaCassa,
                    'chiusura_carta' => $aperturaCarta + $deltaCarta,
                ]);
            }

            return $chiusura;
        });

        return redirect()->route('chiusure.show', $chiusura->id)
            ->with('success', 'Chiusura del giorno generata con successo.');
    }

    public function show(ChiusuraGiorno $chiusura)
    {
        if ($redirect = $this->guardAdmin()) {
            return $redirect;
        }

        $chiusura->load('righe.worker.mansioni', 'creator');
        $datiRighe = $this->buildDatiRighe($chiusura);

        return view('chiusure.show', compact('chiusura', 'datiRighe'));
    }

    public function pdf(ChiusuraGiorno $chiusura)
    {
        if ($redirect = $this->guardAdmin()) {
            return $redirect;
        }

        $chiusura->load('righe.worker.mansioni');
        $datiRighe = $this->buildDatiRighe($chiusura);

        $html = view('pdf.chiusura_giorno', compact('chiusura', 'datiRighe'))->render();

        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'chiusura_giorno_'.$chiusura->data_chiusura->format('Y-m-d').'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    /**
     * Costruisce movimenti + lavori del giorno per ogni riga (usato da show e pdf).
     */
    private function buildDatiRighe(ChiusuraGiorno $chiusura): array
    {
        $data = $chiusura->data_chiusura->copy()->startOfDay();
        $datiRighe = [];

        foreach ($chiusura->righe as $riga) {
            if (! $riga->worker) {
                continue;
            }

            $datiRighe[$riga->worker_id] = [
                'movimenti' => $this->movimentiDelGiorno($riga->worker, $data),
                'lavori' => $this->lavoriDelGiorno($riga->worker, $data),
            ];
        }

        return $datiRighe;
    }

    /**
     * Worker con almeno un movimento o un lavoro fino a $data inclusa.
     *
     * @return \Illuminate\Support\Collection<Worker>
     */
    private function workersConAttivita(Carbon $data)
    {
        $fine = $data->copy()->endOfDay();

        $workerIdsMov = CashMovement::where('data_movimento', '<=', $fine)
            ->distinct()
            ->pluck('worker_id');

        $workerIdsLavori = Worker::whereHas('works', function ($q) use ($fine) {
            $q->where('data_esecuzione', '<=', $fine);
        })->pluck('id');

        $ids = $workerIdsMov->merge($workerIdsLavori)->unique();

        return Worker::whereIn('id', $ids)->orderBy('name_worker')->get();
    }

    /**
     * Saldo fondo cassa (contanti) accumulato PRIMA di $data.
     */
    private function aperturaFondoCassa(Worker $worker, Carbon $data): float
    {
        $entrate = CashMovement::where('worker_id', $worker->id)
            ->where('metodo_pagamento', 'contanti')
            ->where('tipo_movimento', 'entrata')
            ->where('data_movimento', '<', $data)
            ->sum('importo');

        $uscite = CashMovement::where('worker_id', $worker->id)
            ->where('metodo_pagamento', 'contanti')
            ->where('tipo_movimento', 'uscita')
            ->where('data_movimento', '<', $data)
            ->sum('importo');

        return (float) ($entrate - $uscite);
    }

    /**
     * Saldo carta prepagata accumulato PRIMA di $data:
     * ricariche (credit_card_recharges positive) - spese carta (cash_movements uscita/carta).
     */
    private function aperturaCarta(Worker $worker, Carbon $data): float
    {
        $cardIds = $worker->assignedCreditCards()->pluck('credit_cards.id');

        if ($cardIds->isEmpty()) {
            return 0.0;
        }

        $ricariche = DB::table('credit_card_recharges')
            ->whereIn('credit_card_id', $cardIds)
            ->where('importo', '>', 0)
            ->where('data_ricarica', '<', $data)
            ->sum('importo');

        $spese = CashMovement::where('worker_id', $worker->id)
            ->where('metodo_pagamento', 'carta')
            ->where('tipo_movimento', 'uscita')
            ->where('data_movimento', '<', $data)
            ->sum('importo');

        return (float) ($ricariche - $spese);
    }

    /**
     * Variazione del giorno per fondo cassa e carta.
     *
     * @return array{0: float, 1: float} [deltaCassa, deltaCarta]
     */
    private function deltaDelGiorno(Worker $worker, Carbon $data): array
    {
        $movimenti = $this->movimentiDelGiorno($worker, $data);

        $deltaCassa = 0.0;
        $deltaCarta = 0.0;

        foreach ($movimenti as $mov) {
            $segno = $mov->tipo_movimento === 'entrata' ? 1 : -1;
            $importo = $segno * (float) $mov->importo;

            if ($mov->metodo_pagamento === 'contanti') {
                $deltaCassa += $importo;
            } elseif ($mov->metodo_pagamento === 'carta') {
                $deltaCarta += $importo;
            }
        }

        // Ricariche carta del giorno (aumentano il saldo carta)
        $cardIds = $worker->assignedCreditCards()->pluck('credit_cards.id');
        if ($cardIds->isNotEmpty()) {
            $deltaCarta += (float) DB::table('credit_card_recharges')
                ->whereIn('credit_card_id', $cardIds)
                ->where('importo', '>', 0)
                ->whereDate('data_ricarica', $data)
                ->sum('importo');
        }

        return [$deltaCassa, $deltaCarta];
    }

    private function movimentiDelGiorno(Worker $worker, Carbon $data)
    {
        return CashMovement::where('worker_id', $worker->id)
            ->whereDate('data_movimento', $data)
            ->with('work.customer', 'work.appaltatore', 'creditCard')
            ->orderBy('data_movimento')
            ->orderBy('created_at')
            ->get();
    }

    private function lavoriDelGiorno(Worker $worker, Carbon $data)
    {
        return $worker->works()
            ->whereDate('data_esecuzione', $data)
            ->with('customer', 'appaltatore')
            ->orderBy('data_esecuzione')
            ->get();
    }
}

@extends('layouts.dashboard')

@php
    $labelDestinatario = function ($lavoro) {
        if ($lavoro->customer) {
            return $lavoro->customer->customer_type === 'fisica'
                ? $lavoro->customer->full_name
                : $lavoro->customer->ragione_sociale;
        }
        if ($lavoro->appaltatore) {
            return $lavoro->appaltatore->ragione_sociale ?: $lavoro->appaltatore->full_name;
        }
        return 'N/D';
    };
@endphp

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chiusura del giorno — {{ $chiusura->data_chiusura->format('d/m/Y') }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('chiusure.pdf', $chiusura->id) }}" target="_blank" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Esporta PDF
            </a>
            <a href="{{ route('chiusure.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Indietro
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @forelse($chiusura->righe as $riga)
        @php
            $dati = $datiRighe[$riga->worker_id] ?? ['movimenti' => collect(), 'lavori' => collect()];
            $mansioni = $riga->worker ? $riga->worker->mansioni->pluck('mansione')->map(fn ($m) => ucfirst($m))->implode(', ') : '';
        @endphp
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h6 class="m-0 font-weight-bold text-primary">
                    {{ $riga->worker->full_name ?? 'Lavoratore #'.$riga->worker_id }}
                    @if($mansioni)<span class="text-muted"> — {{ $mansioni }}</span>@endif
                </h6>
                <div>
                    <span class="badge bg-info text-dark me-2">Carta apertura: € {{ number_format($riga->apertura_carta, 2, ',', '.') }}</span>
                    <span class="badge bg-secondary">Fondo cassa apertura: € {{ number_format($riga->apertura_fondo_cassa, 2, ',', '.') }}</span>
                </div>
            </div>
            <div class="card-body">
                <h6 class="font-weight-bold">Lavori svolti</h6>
                @if($dati['lavori']->isEmpty())
                    <p class="text-muted">Nessun lavoro svolto.</p>
                @else
                    <ul class="mb-4">
                        @foreach($dati['lavori'] as $lavoro)
                            <li>
                                #{{ $lavoro->id }} — {{ $lavoro->tipo_lavoro }} — {{ $labelDestinatario($lavoro) }}
                                @if($lavoro->costo_lavoro) — € {{ number_format($lavoro->costo_lavoro, 2, ',', '.') }}@endif
                            </li>
                        @endforeach
                    </ul>
                @endif

                <h6 class="font-weight-bold">Movimenti del giorno</h6>
                @if($dati['movimenti']->isEmpty())
                    <p class="text-muted">Nessun movimento.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>Data</th>
                                    <th>Metodo</th>
                                    <th>Tipo</th>
                                    <th class="text-end">Importo</th>
                                    <th>Causale</th>
                                    <th>Lavoro</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dati['movimenti'] as $mov)
                                    <tr>
                                        <td>{{ $mov->data_movimento->format('d/m/Y') }}</td>
                                        <td>{{ ucfirst($mov->metodo_pagamento) }}</td>
                                        <td>
                                            @if($mov->tipo_movimento === 'entrata')
                                                <span class="text-success">Entrata</span>
                                            @else
                                                <span class="text-danger">Uscita</span>
                                            @endif
                                        </td>
                                        <td class="text-end {{ $mov->tipo_movimento === 'entrata' ? 'text-success' : 'text-danger' }}">
                                            {{ $mov->tipo_movimento === 'entrata' ? '+' : '−' }} € {{ number_format($mov->importo, 2, ',', '.') }}
                                        </td>
                                        <td>{{ $mov->motivo ?: '—' }}</td>
                                        <td>{{ $mov->work ? '#'.$mov->work->id : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="d-flex justify-content-end gap-3 mt-3">
                    <div class="fw-bold">Saldo finale Carta prepagata: € {{ number_format($riga->chiusura_carta, 2, ',', '.') }}</div>
                    <div class="fw-bold">Saldo finale Fondo cassa: € {{ number_format($riga->chiusura_fondo_cassa, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">Nessun lavoratore con attività per questa data.</div>
    @endforelse
</div>
@endsection

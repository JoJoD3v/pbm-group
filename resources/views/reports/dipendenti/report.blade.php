@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Report Dipendente</h1>
        <a href="{{ route('reports.dipendenti.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Nuovo Report
        </a>
    </div>

    {{-- Riepilogo testata --}}
    <div class="alert alert-info mb-4">
        <div class="row">
            <div class="col-md-6">
                <strong>Dipendente:</strong> {{ $worker->name_worker }} {{ $worker->cognome_worker }}
                @if($worker->worker_email)
                    &nbsp;({{ $worker->worker_email }})
                @endif
            </div>
            <div class="col-md-6">
                <strong>Periodo:</strong>
                {{ \Carbon\Carbon::parse($dataInizio)->format('d/m/Y') }} —
                {{ \Carbon\Carbon::parse($dataFine)->format('d/m/Y') }}
            </div>
        </div>
    </div>

    {{-- Cards riepilogo lavori --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Lavori Totali</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totaleLavori }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Valore Totale Lavori</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">€ {{ number_format($totaleCosto, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Fondo Cassa Attuale</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">€ {{ number_format($worker->fondo_cassa, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sezione Lavori --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-wrench"></i> Lavori nel Periodo
            </h6>
        </div>
        <div class="card-body">
            @if($lavori->isEmpty())
                <p class="text-muted mb-0">Nessun lavoro trovato nel periodo selezionato.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%">
                        <thead class="table-dark">
                            <tr>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th>Cliente</th>
                                <th>Status</th>
                                <th class="text-end">Costo (€)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lavori as $lavoro)
                                @php
                                    $statusClass = match($lavoro->status_lavoro) {
                                        'Lavoro Completato', 'Concluso' => 'table-success',
                                        'Lavoro Iniziato' => 'table-info',
                                        'Lavoro Annullato' => 'table-danger',
                                        default => '',
                                    };
                                @endphp
                                <tr class="{{ $statusClass }}">
                                    <td>{{ \Carbon\Carbon::parse($lavoro->data_esecuzione)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $lavoro->tipo_lavoro }}</td>
                                    <td>
                                        {{ $lavoro->customer->full_name ?? $lavoro->customer->ragione_sociale ?? 'N/D' }}
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if(in_array($lavoro->status_lavoro, ['Lavoro Completato','Concluso'])) bg-success
                                            @elseif($lavoro->status_lavoro === 'Lavoro Iniziato') bg-info
                                            @elseif($lavoro->status_lavoro === 'Lavoro Annullato') bg-danger
                                            @else bg-secondary @endif">
                                            {{ $lavoro->status_lavoro }}
                                        </span>
                                    </td>
                                    <td class="text-end">{{ $lavoro->costo_lavoro ? number_format($lavoro->costo_lavoro, 2, ',', '.') : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary fw-bold">
                                <td colspan="4" class="text-end">Totale</td>
                                <td class="text-end">€ {{ number_format($totaleCosto, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Sezione Fondo Cassa --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-cash-coin"></i> Movimenti Fondo Cassa
            </h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card border-left-success shadow-sm py-2">
                        <div class="card-body py-2">
                            <div class="text-xs text-success text-uppercase mb-1">Totale Entrate</div>
                            <div class="h6 mb-0 fw-bold">€ {{ number_format($totaleEntrateCassa, 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-left-danger shadow-sm py-2">
                        <div class="card-body py-2">
                            <div class="text-xs text-danger text-uppercase mb-1">Totale Uscite</div>
                            <div class="h6 mb-0 fw-bold">€ {{ number_format($totaleUsciteCassa, 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-left-primary shadow-sm py-2">
                        <div class="card-body py-2">
                            <div class="text-xs text-primary text-uppercase mb-1">Saldo Periodo</div>
                            <div class="h6 mb-0 fw-bold">€ {{ number_format($saldoCassa, 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($movimentiCassa->isEmpty())
                <p class="text-muted mb-0">Nessun movimento nel periodo selezionato.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th>Metodo</th>
                                <th>Motivo</th>
                                <th class="text-end">Importo (€)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movimentiCassa as $movimento)
                                <tr class="{{ $movimento->tipo_movimento === 'entrata' ? 'table-success' : 'table-danger' }}">
                                    <td>{{ \Carbon\Carbon::parse($movimento->data_movimento)->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge {{ $movimento->tipo_movimento === 'entrata' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($movimento->tipo_movimento) }}
                                        </span>
                                    </td>
                                    <td>{{ ucfirst($movimento->metodo_pagamento ?? '—') }}</td>
                                    <td>{{ $movimento->motivo ?? '—' }}</td>
                                    <td class="text-end">{{ number_format($movimento->importo, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Sezione Carta Prepagata --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-credit-card"></i> Carta Prepagata Assegnata
            </h6>
        </div>
        <div class="card-body">
            @if($cartaAssegnata)
                <div class="alert alert-secondary mb-3">
                    <strong>Carta:</strong> {{ $cartaAssegnata->numero_carta }} &nbsp;|&nbsp;
                    <strong>Scadenza:</strong> {{ \Carbon\Carbon::parse($cartaAssegnata->scadenza_carta)->format('d/m/Y') }} &nbsp;|&nbsp;
                    <strong>Saldo Corrente:</strong> € {{ number_format($cartaAssegnata->fondo_carta, 2, ',', '.') }}
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card border-left-success shadow-sm py-2">
                            <div class="card-body py-2">
                                <div class="text-xs text-success text-uppercase mb-1">Ricariche nel Periodo</div>
                                <div class="h6 mb-0 fw-bold">€ {{ number_format($totalRicaricheCarta, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-danger shadow-sm py-2">
                            <div class="card-body py-2">
                                <div class="text-xs text-danger text-uppercase mb-1">Utilizzo Carta nel Periodo</div>
                                <div class="h6 mb-0 fw-bold">€ {{ number_format($totaleUsoCarta, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ricariche carta --}}
                @if($ricaricheCarta->isNotEmpty())
                    <h6 class="font-weight-bold mb-2">Ricariche</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Note</th>
                                    <th class="text-end">Importo (€)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ricaricheCarta as $ricarica)
                                    <tr class="table-success">
                                        <td>{{ \Carbon\Carbon::parse($ricarica->data_ricarica)->format('d/m/Y') }}</td>
                                        <td>{{ $ricarica->note ?? '—' }}</td>
                                        <td class="text-end">{{ number_format($ricarica->importo, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Utilizzi carta --}}
                @if($movimentiCarta->isNotEmpty())
                    <h6 class="font-weight-bold mb-2">Pagamenti con Carta</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th>Motivo</th>
                                    <th class="text-end">Importo (€)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movimentiCarta as $movimento)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($movimento->data_movimento)->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge {{ $movimento->tipo_movimento === 'entrata' ? 'bg-success' : 'bg-danger' }}">
                                                {{ ucfirst($movimento->tipo_movimento) }}
                                            </span>
                                        </td>
                                        <td>{{ $movimento->motivo ?? '—' }}</td>
                                        <td class="text-end">{{ number_format($movimento->importo, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if($ricaricheCarta->isEmpty() && $movimentiCarta->isEmpty())
                    <p class="text-muted mb-0">Nessun movimento carta nel periodo selezionato.</p>
                @endif
            @else
                <p class="text-muted mb-0">Nessuna carta prepagata assegnata a questo dipendente.</p>
            @endif
        </div>
    </div>

    {{-- Bottone esporta PDF --}}
    <div class="mb-4">
        <form action="{{ route('reports.dipendenti.pdf') }}" method="POST" target="_blank">
            @csrf
            <input type="hidden" name="worker_id" value="{{ $worker->id }}">
            <input type="hidden" name="data_inizio" value="{{ $dataInizio }}">
            <input type="hidden" name="data_fine" value="{{ $dataFine }}">
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Esporta in PDF
            </button>
        </form>
    </div>
</div>
@endsection

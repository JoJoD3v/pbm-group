@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Report Clienti</h1>
        <a href="{{ route('reports.clienti.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Nuovo Report
        </a>
    </div>

    {{-- Testata cliente --}}
    <div class="alert alert-info mb-4">
        <strong>Cliente:</strong>
        {{ $customer->customer_type == 'fisica' ? $customer->full_name : $customer->ragione_sociale }}
        @if($dataInizio || $dataFine)
            <br>
            <strong>Periodo:</strong>
            {{ $dataInizio ? \Carbon\Carbon::parse($dataInizio)->format('d/m/Y') : '...' }}
            &mdash;
            {{ $dataFine ? \Carbon\Carbon::parse($dataFine)->format('d/m/Y') : '...' }}
        @endif
    </div>

    {{-- Cards riepilogo --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Totale Lavori</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totaleLavori }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Somma Compensi</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">€ {{ number_format($totaleCompensoTotale, 2, ',', '.') }}</div>
                    <div class="text-xs text-muted">(tutti gli status)</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Riepilogo per status --}}
    @if($lavoriPerStatus->isNotEmpty())
        <div class="row mb-4">
            @foreach($lavoriPerStatus as $status => $gruppo)
                @php
                    $colorClass = match($status) {
                        'Lavoro Completato', 'Concluso' => 'border-left-success',
                        'Lavoro Iniziato' => 'border-left-info',
                        'Lavoro Annullato' => 'border-left-danger',
                        default => 'border-left-secondary',
                    };
                @endphp
                <div class="col-md-3 mb-3">
                    <div class="card {{ $colorClass }} shadow-sm py-2">
                        <div class="card-body py-2">
                            <div class="text-xs text-uppercase mb-1 font-weight-bold">{{ $status }}</div>
                            <div class="h6 mb-0 fw-bold">{{ $gruppo->count() }} lavori</div>
                            <div class="text-xs">€ {{ number_format($gruppo->sum('costo_lavoro'), 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Elenco dettagliato lavori --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="bi bi-list-ul"></i> Elenco Lavori del Cliente
            </h6>
        </div>
        <div class="card-body">
            @if($lavori->isEmpty())
                <p class="text-muted mb-0">Nessun lavoro trovato per questo cliente.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%">
                        <thead class="table-dark">
                            <tr>
                                <th>Lavoro</th>
                                <th>Data Lavoro</th>
                                <th>Tipo Lavoro</th>
                                <th>Status</th>
                                <th class="text-end">Compenso (€)</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lavori as $lavoro)
                                @php
                                    $rowClass = match($lavoro->status_lavoro) {
                                        'Lavoro Completato', 'Concluso' => 'table-success',
                                        'Lavoro Iniziato' => 'table-info',
                                        'Lavoro Annullato' => 'table-danger',
                                        default => '',
                                    };
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td>#{{ $lavoro->id }}</td>
                                    <td>{{ $lavoro->data_esecuzione ? \Carbon\Carbon::parse($lavoro->data_esecuzione)->format('d/m/Y H:i') : '—' }}</td>
                                    <td>{{ $lavoro->tipo_lavoro }}</td>
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
                                    <td>
                                        <a href="{{ route('works.show', $lavoro->id) }}" class="btn btn-sm btn-success">
                                            <i class="bi bi-eye"></i> Vedi
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary fw-bold">
                                <td colspan="4" class="text-end">Totale</td>
                                <td class="text-end">€ {{ number_format($totaleCompensoTotale, 2, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Bottone esporta PDF --}}
    <div class="mb-4">
        <form action="{{ route('reports.clienti.pdf') }}" method="POST" target="_blank">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customerId }}">
            <input type="hidden" name="data_inizio" value="{{ $dataInizio }}">
            <input type="hidden" name="data_fine" value="{{ $dataFine }}">
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Esporta in PDF
            </button>
        </form>
    </div>
</div>
@endsection

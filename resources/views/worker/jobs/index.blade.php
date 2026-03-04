@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Lavori Non Assegnati di Oggi ({{ \Carbon\Carbon::today()->format('d/m/Y') }})</h1>
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lavori Non Assegnati di Oggi</h6>
            </div>
            <div class="card-body">
                @if($works->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Data/Ora</th>
                                    <th>Tipo Lavoro</th>
                                    <th>Cliente</th>
                                    <th>Materiale</th>
                                    <th>Indirizzo Partenza</th>
                                    <th>Indirizzo Destinazione</th>
                                    <th>Stato</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($works as $work)
                                    <tr class="table-info">
                                        <td>{{ $work->id }}</td>
                                        <td>@formatDateTime($work->data_esecuzione ?? $work->created_at)</td>
                                        <td>{{ $work->tipo_lavoro }}</td>
                                        <td>
                                            @if($work->customer)
                                                {{ $work->customer->ragione_sociale ?? $work->customer->full_name }}
                                            @else
                                                N/D
                                            @endif
                                        </td>
                                        <td>{{ $work->materiale ?? 'N/D' }}</td>
                                        <td>
                                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($work->indirizzo_partenza) }}" target="_blank">
                                                {{ $work->indirizzo_partenza }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($work->indirizzo_destinazione) }}" target="_blank">
                                                {{ $work->indirizzo_destinazione }}
                                            </a>
                                        </td>
                                        <td>
                                            @php
                                                $status = $work->status_lavoro;
                                                $statusBadge = 'secondary';
                                                if ($status === 'Preso in Carico') $statusBadge = 'info';
                                                if ($status === 'Lavoro Iniziato') $statusBadge = 'warning';
                                                if ($status === 'Lavoro Completato' || $status === 'Concluso') $statusBadge = 'success';
                                                if ($status === 'Lavoro Annullato') $statusBadge = 'danger';
                                            @endphp
                                            <span class="badge bg-{{ $statusBadge }}">{{ $status ?? 'In Sospeso' }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('worker.jobs.show', $work->id) }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye"></i> Dettagli
                                            </a>
                                            <form action="{{ route('worker.jobs.assumi', $work->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-person-check"></i> Assumi Lavoro
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        Non ci sono lavori non assegnati per oggi.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 

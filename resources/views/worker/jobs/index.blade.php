@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">I Miei Lavori</h1>
        
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
                <h6 class="m-0 font-weight-bold text-primary">Lavori di Oggi ({{ \Carbon\Carbon::today()->format('d/m/Y') }})</h6>
            </div>
            <div class="card-body">
                @if($works->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo Lavoro</th>
                                    <th>Cliente</th>
                                    <th>Materiale</th>
                                    <th>Indirizzo Partenza</th>
                                    <th>Indirizzo Destinazione</th>
                                    <th>Stato</th>
                                    <th>Assegnato</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($works as $work)
                                    @php
                                        $isAssigned = $work->workers->count() > 0;
                                        $isAssignedToMe = $worker->works->contains($work->id);
                                    @endphp
                                    <tr class="{{ !$isAssigned ? 'table-info' : '' }}">
                                        <td>{{ $work->id }}</td>
                                        <td>{{ $work->tipo_lavoro }}</td>
                                        <td>{{ $work->customer->ragione_sociale ?? $work->customer->full_name }}</td>
                                        <td>{{ $work->materiale }}</td>
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
                                            @if($work->status_lavoro)
                                                <span class="badge bg-{{ $work->status_lavoro == 'Completato' ? 'success' : ($work->status_lavoro == 'In corso' ? 'warning' : 'info') }}">
                                                    {{ $work->status_lavoro }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Non assegnato</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isAssigned)
                                                @if($isAssignedToMe)
                                                    <span class="badge bg-success">A Me</span>
                                                @else
                                                    <span class="badge bg-warning">Ad Altri</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Non Assegnato</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isAssignedToMe || !$isAssigned)
                                                <a href="{{ route('worker.jobs.show', $work->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-eye"></i> Dettagli
                                                </a>
                                                
                                                @if($isAssignedToMe)
                                                    <a href="{{ route('worker.ricevute.create', $work->id) }}" class="btn btn-success btn-sm">
                                                        <i class="bi bi-receipt"></i> Esegui Ricevuta
                                                    </a>
                                                @else
                                                    <form action="{{ route('worker.jobs.assumi', $work->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning btn-sm">
                                                            <i class="bi bi-person-check"></i> Assumi Lavoro
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        Non ci sono lavori disponibili per oggi.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 
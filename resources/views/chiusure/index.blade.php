@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chiusura del giorno</h1>
        <a href="{{ route('chiusure.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nuova Chiusura del giorno
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            @if($chiusure->isEmpty())
                <p class="text-muted mb-0">Nessuna chiusura registrata.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%">
                        <thead class="table-dark">
                            <tr>
                                <th>Data chiusura</th>
                                <th style="width: 140px;">N° lavoratori</th>
                                <th>Creato da</th>
                                <th style="width: 180px;">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($chiusure as $chiusura)
                                <tr>
                                    <td>{{ $chiusura->data_chiusura->format('d/m/Y') }}</td>
                                    <td>{{ $chiusura->righe_count }}</td>
                                    <td>{{ $chiusura->creator->name ?? 'N/D' }}</td>
                                    <td>
                                        <a href="{{ route('chiusure.show', $chiusura->id) }}" class="btn btn-sm btn-success">
                                            <i class="bi bi-eye"></i> Vedi
                                        </a>
                                        <a href="{{ route('chiusure.pdf', $chiusura->id) }}" target="_blank" class="btn btn-sm btn-danger">
                                            <i class="bi bi-file-earmark-pdf"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

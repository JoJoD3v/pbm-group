@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Report Assegnazioni Automezzi</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filtri -->
            <form method="GET" class="mb-4">
                <div class="row">                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="data_inizio">Data Inizio</label>
                            <div class="italian-date-input">
                                <input type="date" class="form-control" id="data_inizio" name="data_inizio" value="{{ request('data_inizio') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="data_fine">Data Fine</label>
                            <div class="italian-date-input">
                                <input type="date" class="form-control" id="data_fine" name="data_fine" value="{{ request('data_fine') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">Filtra</button>
                                <a href="{{ route('vehicle.assignments.report') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>            <!-- Tabella -->
            <div class="table-responsive">
                <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>                            <th>Automezzo</th>
                            <th>Targa</th>
                            <th>Lavoratore</th>
                            <th class="datetime-column">Data Assegnazione</th>
                            <th class="datetime-column">Data Restituzione</th>
                            <th>Note</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $assignment)
                            <tr>
                                <td>{{ $assignment->vehicle_nome }}</td>
                                <td>{{ $assignment->targa }}</td>
                                <td>{{ $assignment->name_worker }} {{ $assignment->cognome_worker }}</td>                                <td class="datetime-column">@formatDateTime($assignment->data_assegnazione)</td>
                                <td class="datetime-column">
                                    @if($assignment->data_restituzione)
                                        @formatDateTime($assignment->data_restituzione)
                                    @else
                                        <span class="badge bg-success">Attualmente Assegnato</span>
                                    @endif
                                </td>
                                <td>{{ $assignment->note ?? 'N/D' }}</td>
                                <td>
                                    <form action="{{ route('vehicle.assignments.report.destroy', $assignment->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Sei sicuro di voler eliminare questo log?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // La configurazione globale si applica automaticamente
        $('#dataTable').DataTable({
            order: [[3, 'desc']], // Ordina per data di assegnazione decrescente
            columnDefs: [
                {
                    targets: [3, 4], // Colonne datetime (0-based index)
                    type: 'date-eu-time' // Usa il tipo date-eu-time per ordinamento corretto con ora e minuti
                }
            ]
        });
    });
</script>
@endsection 
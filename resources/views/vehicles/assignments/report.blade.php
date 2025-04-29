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
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="data_inizio">Data Inizio</label>
                            <input type="date" class="form-control" id="data_inizio" name="data_inizio" value="{{ request('data_inizio') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="data_fine">Data Fine</label>
                            <input type="date" class="form-control" id="data_fine" name="data_fine" value="{{ request('data_fine') }}">
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
            </form>

            <!-- Tabella -->
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Automezzo</th>
                            <th>Targa</th>
                            <th>Lavoratore</th>
                            <th>Data Assegnazione</th>
                            <th>Data Restituzione</th>
                            <th>Note</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $assignment)
                            <tr>
                                <td>{{ $assignment->vehicle_nome }}</td>
                                <td>{{ $assignment->targa }}</td>
                                <td>{{ $assignment->name_worker }} {{ $assignment->cognome_worker }}</td>
                                <td>{{ \Carbon\Carbon::parse($assignment->data_assegnazione)->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($assignment->data_restituzione)
                                        {{ \Carbon\Carbon::parse($assignment->data_restituzione)->format('d/m/Y H:i') }}
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
        $('#dataTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json'
            },
            order: [[3, 'desc']] // Ordina per data di assegnazione decrescente
        });
    });
</script>
@endsection 
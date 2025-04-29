@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">Assegnazioni Automezzi</h6>
      <a href="{{ route('vehicle.assignments.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Nuova Assegnazione
      </a>
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

      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Automezzo</th>
              <th>Targa</th>
              <th>Lavoratore</th>
              <th>Data Assegnazione</th>
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
                <td>
                  @if($assignment->data_assegnazione)
                    {{ \Carbon\Carbon::parse($assignment->data_assegnazione)->format('d/m/Y H:i') }}
                  @else
                    N/D
                  @endif
                </td>
                <td>{{ $assignment->note ?? 'N/D' }}</td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{ route('vehicle.assignments.edit', ['vehicle' => $assignment->vehicle_id, 'worker' => $assignment->worker_id]) }}" class="btn btn-warning btn-sm">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('vehicle.assignments.destroy', ['vehicle' => $assignment->vehicle_id, 'worker' => $assignment->worker_id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Sei sicuro di voler eliminare questa assegnazione?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
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
      }
    });
  });
</script>
@endsection

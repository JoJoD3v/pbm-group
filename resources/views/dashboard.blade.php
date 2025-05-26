@extends('layouts.dashboard')

@section('content')
  <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
  <div class="row mb-4">
    <div class="col-lg-12">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Informazioni Personali</h6>
        </div>        <div class="card-body">
          <p>Benvenuto, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}.</p>
          <p>Il tuo ruolo: 
            @if(Auth::user()->role == 'Amministratore')
              <span class="badge bg-primary">{{ Auth::user()->role }}</span>
            @elseif(Auth::user()->role == 'Sviluppatore')
              <span class="badge bg-info">{{ Auth::user()->role }}</span>
            @else
              <span class="badge bg-secondary">{{ Auth::user()->role }}</span>
            @endif
          </p>
        </div>
      </div>
    </div>
  </div>
  @if(in_array(Auth::user()->role, ['Amministratore', 'Sviluppatore']))
    <div class="row">
      <div class="col-lg-12">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Lavori Programmati per Oggi ({{ date('d/m/Y') }})</h6>
          </div>
          <div class="card-body">
            @if($todayWorks->count() > 0)
              <div class="table-responsive">
                <table class="table table-bordered dataTable" id="todayWorksTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Tipo</th>
                      <th>Cliente</th>
                      <th>Stato</th>
                      <th>Partenza</th>
                      <th>Destinazione</th>
                      <th>Materiale</th>
                      <th>Azioni</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($todayWorks as $work)
                      <tr>
                        <td>{{ $work->id }}</td>
                        <td>{{ $work->tipo_lavoro }}</td>
                        <td>
                          @if($work->customer)
                            {{ $work->customer->customer_type == 'fisica' ? $work->customer->full_name : $work->customer->ragione_sociale }}
                          @else
                            N/D
                          @endif
                        </td>
                        <td>
                          @if($work->status_lavoro == 'programmato')
                            <span class="badge bg-warning text-dark">Programmato</span>
                          @elseif($work->status_lavoro == 'in_corso')
                            <span class="badge bg-info">In Corso</span>
                          @elseif($work->status_lavoro == 'completato')
                            <span class="badge bg-success">Completato</span>
                          @elseif($work->status_lavoro == 'annullato')
                            <span class="badge bg-danger">Annullato</span>
                          @else
                            <span class="badge bg-secondary">{{ $work->status_lavoro }}</span>
                          @endif
                        </td>
                        <td>{{ $work->indirizzo_partenza }}</td>
                        <td>{{ $work->indirizzo_destinazione }}</td>
                        <td>{{ $work->materiale }}</td>
                        <td>
                          <a href="{{ route('works.show', $work->id) }}" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i>
                          </a>
                          <a href="{{ route('works.edit', $work->id) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i>
                          </a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="alert alert-info">
                Nessun lavoro programmato per oggi.
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  @endif
@endsection

@section('scripts')
  @if(in_array(Auth::user()->role, ['Amministratore', 'Sviluppatore']))
    <script>
      $(document).ready(function() {
        $('#todayWorksTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json"
          },
          "pageLength": 10,
          "order": [[ 0, "desc" ]]
        });
      });
    </script>
  @endif
@endsection

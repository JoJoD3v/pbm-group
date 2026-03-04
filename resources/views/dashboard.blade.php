@extends('layouts.dashboard')

@section('content')
  @php
    $role = strtolower(Auth::user()->role ?? '');
  @endphp

  <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
  @if($role !== 'dipendente')
    <div class="row mb-4">
      <div class="col-lg-12">
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informazioni Personali</h6>
          </div>        <div class="card-body">
            <p>Benvenuto, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}.</p>
            <p>Il tuo ruolo: 
              @if($role === 'amministratore')
                <span class="badge bg-primary">{{ ucfirst($role) }}</span>
              @elseif($role === 'sviluppatore')
                <span class="badge bg-info">{{ ucfirst($role) }}</span>
              @else
                <span class="badge bg-secondary">{{ ucfirst($role) }}</span>
              @endif
            </p>
          </div>
        </div>
      </div>
    </div>
  @endif
  @if(in_array($role, ['amministratore', 'sviluppatore']))
    <div class="row">
      <div class="col-lg-12">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Lavori Programmati per Oggi ({{ date('d/m/Y') }})</h6>
          </div>
          <div class="card-body">
            @if($todayWorks->count() > 0)
              <div class="table-responsive">
                <table class="table table-bordered" id="todayWorksTable" width="100%" cellspacing="0">
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
                          @php
                            $status = $work->status_lavoro;
                            $statusBadge = 'secondary';
                            if ($status === 'Preso in Carico') $statusBadge = 'info';
                            if ($status === 'Lavoro Iniziato') $statusBadge = 'primary';
                            if ($status === 'Lavoro Completato' || $status === 'Concluso') $statusBadge = 'success';
                            if ($status === 'Lavoro Annullato') $statusBadge = 'danger';
                          @endphp
                          <span class="badge bg-{{ $statusBadge }}">{{ $status ?? 'In Sospeso' }}</span>
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

  @if($role === 'dipendente')
    <div class="row">
      <div class="col-lg-12">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Lavori Assegnati Oggi ({{ date('d/m/Y') }})</h6>
          </div>
          <div class="card-body">
            @if($workerTodayWorks->count() > 0)
              <div class="table-responsive">
                <table class="table table-bordered" id="workerTodayWorksTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Ora</th>
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
                    @foreach($workerTodayWorks as $work)
                      <tr>
                        <td>@formatDateTime($work->data_esecuzione ?? $work->created_at)</td>
                        <td>{{ $work->tipo_lavoro }}</td>
                        <td>
                          @if($work->customer)
                            {{ $work->customer->customer_type == 'fisica' ? $work->customer->full_name : $work->customer->ragione_sociale }}
                          @else
                            N/D
                          @endif
                        </td>
                        <td>{{ $work->status_lavoro }}</td>
                        <td>{{ $work->indirizzo_partenza }}</td>
                        <td>{{ $work->indirizzo_destinazione }}</td>
                        <td>{{ $work->materiale }}</td>
                        <td>
                          <a href="{{ route('worker.jobs.show', $work->id) }}" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i>
                          </a>
                          <a href="{{ route('worker.ricevute.create', $work->id) }}" class="btn btn-success btn-sm">
                            <i class="bi bi-receipt"></i>
                          </a>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="alert alert-info">
                Nessun lavoro assegnato per oggi.
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Primo Lavoro di Domani ({{ date('d/m/Y', strtotime('+1 day')) }})</h6>
          </div>
          <div class="card-body">
            @if($tomorrowFirstWork)
              <div class="row">
                <div class="col-md-6 mb-2"><strong>Ora:</strong> @formatDateTime($tomorrowFirstWork->data_esecuzione ?? $tomorrowFirstWork->created_at)</div>
                <div class="col-md-6 mb-2"><strong>Tipo:</strong> {{ $tomorrowFirstWork->tipo_lavoro }}</div>
                <div class="col-md-6 mb-2">
                  <strong>Cliente:</strong>
                  @if($tomorrowFirstWork->customer)
                    {{ $tomorrowFirstWork->customer->customer_type == 'fisica' ? $tomorrowFirstWork->customer->full_name : $tomorrowFirstWork->customer->ragione_sociale }}
                  @else
                    N/D
                  @endif
                </div>
                <div class="col-md-6 mb-2"><strong>Stato:</strong> {{ $tomorrowFirstWork->status_lavoro }}</div>
                <div class="col-md-6 mb-2"><strong>Partenza:</strong> {{ $tomorrowFirstWork->indirizzo_partenza }}</div>
                <div class="col-md-6 mb-2"><strong>Destinazione:</strong> {{ $tomorrowFirstWork->indirizzo_destinazione }}</div>
                <div class="col-md-6 mb-2"><strong>Materiale:</strong> {{ $tomorrowFirstWork->materiale ?? 'N/D' }}</div>
                <div class="col-md-6 mb-2">
                  <a href="{{ route('worker.jobs.show', $tomorrowFirstWork->id) }}" class="btn btn-info btn-sm">
                    <i class="bi bi-eye"></i> Dettagli
                  </a>
                </div>
              </div>
            @else
              <div class="alert alert-info">
                Nessun lavoro assegnato per domani.
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  @endif
@endsection

@section('scripts')
  @if(in_array($role, ['amministratore', 'sviluppatore']))
    <script>
      $(document).ready(function() {
        if ($('#todayWorksTable').length && !$.fn.dataTable.isDataTable('#todayWorksTable')) {
          $('#todayWorksTable').DataTable({
            "language": {
              "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json"
            },
            "pageLength": 10,
            "order": [[ 0, "desc" ]]
          });
        }
      });
    </script>
  @endif
  @if($role === 'dipendente' && $workerTodayWorks->count() > 0)
    <script>
      $(document).ready(function() {
        if ($('#workerTodayWorksTable').length && !$.fn.dataTable.isDataTable('#workerTodayWorksTable')) {
          $('#workerTodayWorksTable').DataTable({
            "language": {
              "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json"
            },
            "pageLength": 10,
            "order": [[ 0, "asc" ]]
          });
        }
      });
    </script>
  @endif
@endsection

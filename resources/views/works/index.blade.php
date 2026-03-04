@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">{{ $pageTitle ?? 'Elenco Lavori' }}</h6>
    </div>
    <div class="card-body">
      @if(session('success'))
         <div class="alert alert-success">
           {{ session('success') }}
         </div>
      @endif

      <!-- Filtri di ricerca -->
      <div class="row mb-4">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h6 class="m-0 font-weight-bold text-primary">Filtri</h6>
            </div>
            <div class="card-body">
              <form action="{{ route($indexRoute ?? 'works.index') }}" method="GET" class="row">                <div class="col-md-4 mb-3">
                  <label for="data_inizio">Data inizio</label>
                  <div class="italian-date-input">
                    <input type="date" class="form-control" id="data_inizio" name="data_inizio" value="{{ request('data_inizio') }}">
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="data_fine">Data fine</label>
                  <div class="italian-date-input">
                    <input type="date" class="form-control" id="data_fine" name="data_fine" value="{{ request('data_fine') }}">
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="tipo_lavoro">Tipo Lavoro</label>
                  <select class="form-control" id="tipo_lavoro" name="tipo_lavoro">
                    <option value="">Tutti</option>
                    <option value="smaltimento" {{ request('tipo_lavoro') == 'smaltimento' ? 'selected' : '' }}>Smaltimento</option>
                    <option value="trasporto" {{ request('tipo_lavoro') == 'trasporto' ? 'selected' : '' }}>Trasporto</option>
                  </select>
                </div>
                <div class="col-md-12">
                  <button type="submit" class="btn btn-primary">Filtra</button>
                  <a href="{{ route($indexRoute ?? 'works.index') }}" class="btn btn-secondary">Reset</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>      <div class="table-responsive">
         <table id="worksTable" class="table table-bordered dataTable" width="100%" cellspacing="0">
            <thead class="thead-light">
                <tr>
                    <th class="datetime-column">Data</th>
                    <th>Tipo Lavoro</th>
                    <th>Cliente</th>
                    @if(!empty($showAssignedWorkerColumn))
                    <th>Lavoratore</th>
                    @endif
                    <th>Status</th>
                    <th>Materiali</th>
                    <th>Indirizzo Partenza</th>
                    <th>Indirizzo Destinazione</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
              @foreach($works as $work)
                <tr>
                    <td class="datetime-column">@formatDateTime($work->data_esecuzione ?? $work->created_at)</td>
                    <td>{{ $work->tipo_lavoro }}</td>
                  <td>
                    @if($work->customer)
                      {{ $work->customer->customer_type == 'fisica' ? $work->customer->full_name : $work->customer->ragione_sociale }}
                    @else
                      N/D
                    @endif
                  </td>
                  @if(!empty($showAssignedWorkerColumn))
                  <td>
                    @if($work->workers && $work->workers->count())
                      {{ $work->workers->pluck('full_name')->join(', ') }}
                    @else
                      N/D
                    @endif
                  </td>
                  @endif
                  @php
                    $status = $work->status_lavoro;
                    $statusBadge = 'secondary';
                    if ($status === 'Preso in Carico') $statusBadge = 'info';
                    if ($status === 'Lavoro Iniziato') $statusBadge = 'warning';
                    if ($status === 'Lavoro Completato' || $status === 'Concluso') $statusBadge = 'success';
                    if ($status === 'Lavoro Annullato') $statusBadge = 'danger';
                  @endphp
                  <td class="status-cell" data-work-id="{{ $work->id }}">
                    <span class="badge bg-{{ $statusBadge }}">{{ $status ?? 'In Sospeso' }}</span>
                  </td>
                  <td>
                    @if($work->materiale)
                      {{ $work->materiale }}
                      @if($work->codice_eer)
                        ({{ $work->codice_eer }})
                      @endif
                    @else
                      N/D
                    @endif
                  </td>
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
                    <a href="{{ route('works.show', $work->id) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-eye"></i>
                      </a>                    
                    <form action="{{ route('works.destroy', $work->id) }}" method="POST" style="display:inline-block;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Sei sicuro?')">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach            </tbody>
         </table>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('#worksTable').DataTable({
      order: [[0, 'desc']], // Ordina per data decrescente (colonna 0)
      columnDefs: [
        {
          targets: 0, // Colonna data e ora (0-based index)
          type: 'date-eu-time' // Usa il tipo date-eu-time per ordinamento corretto
        }
      ]
    });

    @if(!empty($showAssignedWorkerColumn))
      const updateStatusBadge = (cell, status) => {
        let badgeClass = 'secondary';
        if (status === 'Preso in Carico') badgeClass = 'info';
        if (status === 'Lavoro Iniziato') badgeClass = 'warning';
        if (status === 'Lavoro Completato' || status === 'Concluso') badgeClass = 'success';
        if (status === 'Lavoro Annullato') badgeClass = 'danger';
        const label = status || 'In Sospeso';
        cell.html('<span class="badge bg-' + badgeClass + '">' + label + '</span>');
      };

      let polling = null;
      let inFlight = false;

      const fetchStatuses = () => {
        if (document.hidden || inFlight) return;
        const ids = $('.status-cell').map(function() {
          return $(this).data('work-id');
        }).get();

        if (!ids.length) return;

        inFlight = true;
        $.ajax({
          url: '{{ route('works.statuses') }}',
          method: 'POST',
          data: {
            _token: '{{ csrf_token() }}',
            ids: ids
          },
          success: function(data) {
            if (data && data.statuses) {
              $('.status-cell').each(function() {
                const id = $(this).data('work-id');
                if (data.statuses[id] !== undefined) {
                  const current = $(this).find('.badge').text().trim();
                  if (current !== (data.statuses[id] || 'In Sospeso')) {
                    updateStatusBadge($(this), data.statuses[id]);
                  }
                }
              });
            }
          }
        }).always(function() {
          inFlight = false;
        });
      };

      fetchStatuses();
      polling = setInterval(fetchStatuses, 20000);

      document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
          fetchStatuses();
        }
      });
    @endif
  });
</script>
@endsection

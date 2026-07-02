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

      <!-- Navigatore giorno -->
      @php
        $prevDay = \Carbon\Carbon::parse($currentDate)->subDay()->format('Y-m-d');
        $nextDay = \Carbon\Carbon::parse($currentDate)->addDay()->format('Y-m-d');
        $isToday = \Carbon\Carbon::parse($currentDate)->isToday();
      @endphp
      <div class="d-flex align-items-center justify-content-center mb-4 gap-3">
        <a href="{{ route($indexRoute ?? 'works.index', ['data' => $prevDay]) }}" class="btn btn-outline-primary">
          <i class="bi bi-chevron-left"></i>
        </a>
        <span class="h5 m-0 fw-bold">
          {{ \Carbon\Carbon::parse($currentDate)->locale('it')->isoFormat('dddd D MMMM YYYY') }}
          @if($isToday)
            <span class="badge bg-primary ms-2" style="font-size:0.65rem;vertical-align:middle;">Oggi</span>
          @endif
        </span>
        <a href="{{ route($indexRoute ?? 'works.index', ['data' => $nextDay]) }}" class="btn btn-outline-primary">
          <i class="bi bi-chevron-right"></i>
        </a>
        @if(!$isToday)
          <a href="{{ route($indexRoute ?? 'works.index') }}" class="btn btn-secondary btn-sm ms-2">Torna a oggi</a>
        @endif
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
                      @foreach($work->workers as $w)
                        @if($w->colore_bg)
                          <span class="badge" style="background-color: {{ $w->colore_bg }}; color: {{ $w->colore_font ?? '#000' }};">{{ $w->full_name }}</span>{{ !$loop->last ? ', ' : '' }}
                        @else
                          {{ $w->full_name }}{{ !$loop->last ? ', ' : '' }}
                        @endif
                      @endforeach
                    @else
                      N/D
                    @endif
                  </td>
                  @endif
                  @php
                    $status = $work->status_lavoro;
                    $statusBadge = 'secondary';
                    if ($status === 'Preso in Carico') $statusBadge = 'info';
                    if ($status === 'Lavoro Iniziato') $statusBadge = 'primary';
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
                    <a href="{{ route('works.show', $work->id) }}" class="btn btn-success">
                        <i class="bi bi-eye"></i>
                      </a>
                    @if(strtolower(Auth::user()->role ?? '') === 'sviluppatore')
                    <a href="{{ route('works.edit', $work->id) }}" class="btn btn-warning">
                      <i class="bi bi-pencil"></i>
                    </a>
                    @endif
                    <form action="{{ route('works.destroy', $work->id) }}" method="POST" style="display:inline-block;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger" onclick="return confirm('Sei sicuro?')">
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
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    // Verifica se DataTable è già inizializzata, altrimenti inizializzala
    let table;
    if ($.fn.DataTable.isDataTable('#worksTable')) {
      table = $('#worksTable').DataTable();
    } else {
      table = $('#worksTable').DataTable({
        order: [[0, 'desc']], // Ordina per data decrescente (colonna 0)
        columnDefs: [
          {
            targets: 0, // Colonna data e ora (0-based index)
            type: 'date-eu-time' // Usa il tipo date-eu-time per ordinamento corretto
          }
        ]
      });
    }

    // Sistema di polling per aggiornamento automatico degli stati
    const updateStatusBadge = (cell, status) => {
      let badgeClass = 'secondary';
      if (status === 'Preso in Carico') badgeClass = 'info';
      if (status === 'Lavoro Iniziato') badgeClass = 'primary';
      if (status === 'Lavoro Completato' || status === 'Concluso') badgeClass = 'success';
      if (status === 'Lavoro Annullato') badgeClass = 'danger';
      const label = status || 'In Sospeso';
      cell.html('<span class="badge bg-' + badgeClass + '">' + label + '</span>');
    };

    let polling = null;
    let inFlight = false;

    const fetchStatuses = () => {
      if (document.hidden || inFlight) return;

      // Usa il DOM diretto poiché DataTables potrebbe aver riorganizzato le righe
      const ids = [];
      $('#worksTable tbody .status-cell').each(function() {
        const id = $(this).data('work-id');
        if (id) {
          ids.push(id);
        }
      });

      if (!ids.length) {
        return;
      }

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
            let updates = 0;
            $('#worksTable tbody .status-cell').each(function() {
              const id = $(this).data('work-id');
              if (data.statuses[id] !== undefined) {
                const current = $(this).find('.badge').text().trim();
                const newStatus = data.statuses[id] || 'In Sospeso';
                if (current !== newStatus) {
                  updateStatusBadge($(this), data.statuses[id]);
                  updates++;
                }
              }
            });
          }
        }
      }).always(function() {
        inFlight = false;
      });
    };

    // Avvia il polling
    fetchStatuses();
    polling = setInterval(fetchStatuses, 20000);

    // Riprendi il polling quando la pagina torna visibile
    document.addEventListener('visibilitychange', function() {
      if (!document.hidden) {
        fetchStatuses();
      }
    });

    // Pulisci l'intervallo quando la pagina viene abbandonata
    window.addEventListener('beforeunload', function() {
      if (polling) {
        clearInterval(polling);
      }
    });
  });
</script>
@endsection

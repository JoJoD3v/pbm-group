@extends('layouts.dashboard')

@section('content')
  @php
    $role = strtolower(Auth::user()->role ?? '');
    $prevDate = $currentDate->copy()->subDay()->format('Y-m-d');
    $nextDate = $currentDate->copy()->addDay()->format('Y-m-d');
    $isToday = $currentDate->isToday();
  @endphp

  <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
  @if(in_array($role, ['amministratore', 'sviluppatore']))
    <div class="row">
      <div class="col-lg-12">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <a href="{{ route('dashboard', ['date' => $prevDate]) }}" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-chevron-left"></i>
            </a>
            <h6 class="m-0 font-weight-bold text-primary">
              Lavori Programmati{{ $isToday ? ' per Oggi' : '' }} ({{ $currentDate->format('d/m/Y') }})
            </h6>
            <a href="{{ route('dashboard', ['date' => $nextDate]) }}" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-chevron-right"></i>
            </a>
          </div>
          <div class="card-body">
            @if($todayWorks->count() > 0)
              <div class="table-responsive">
                <table class="table table-bordered" id="todayWorksTable" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Ora</th>
                      <th>Tipo</th>
                      <th>Cliente</th>
                      <th>Stato</th>
                      <th>Lavoratore</th>
                      <th>Partenza</th>
                      <th>Destinazione</th>
                      <th>Materiale</th>
                      <th>Azioni</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($todayWorks as $work)
                      <tr>
                        <td>{{ $work->data_esecuzione ? $work->data_esecuzione->format('H:i') : '—' }}</td>
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
                        <td>
                          @if($work->workers->isNotEmpty())
                            {{ $work->workers->map->full_name->join(', ') }}
                          @else
                            <span class="text-muted">—</span>
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

  @if($role === 'dipendente')
    <div class="row">
      <div class="col-lg-12">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <a href="{{ route('dashboard', ['date' => $prevDate]) }}" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-chevron-left"></i>
            </a>
            <h6 class="m-0 font-weight-bold text-primary">
              Lavori Assegnati{{ $isToday ? ' Oggi' : '' }} ({{ $currentDate->format('d/m/Y') }})
            </h6>
            <a href="{{ route('dashboard', ['date' => $nextDate]) }}" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-chevron-right"></i>
            </a>
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
                        <td class="d-flex flex-wrap gap-1">
                          <a href="{{ route('worker.jobs.show', $work->id) }}" class="btn btn-info px-3 py-2">
                            <i class="bi bi-eye"></i>
                          </a>
                          <a href="{{ route('worker.ricevute.create', $work->id) }}" class="btn btn-success px-3 py-2">
                            <i class="bi bi-receipt"></i>
                          </a>
                          <button type="button" class="btn btn-danger px-3 py-2 btn-spesa-lavoro"
                                  data-work-id="{{ $work->id }}"
                                  data-work-label="Lavoro #{{ $work->id }} ({{ $work->customer ? ($work->customer->ragione_sociale ?? $work->customer->full_name) : 'N/D' }})">
                            <i class="bi bi-currency-euro"></i> Spesa
                          </button>
                          <button type="button" class="btn btn-success px-3 py-2 btn-incasso-lavoro"
                                  data-work-id="{{ $work->id }}"
                                  data-work-label="Lavoro #{{ $work->id }} ({{ $work->customer ? ($work->customer->ragione_sociale ?? $work->customer->full_name) : 'N/D' }})">
                            <i class="bi bi-currency-euro"></i> Incasso
                          </button>
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
            <h6 class="m-0 font-weight-bold text-primary">Primo Lavoro di {{ $isToday ? 'Domani' : 'Dopodomani' }} ({{ $currentDate->copy()->addDay()->format('d/m/Y') }})</h6>
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

  @if($worker)
  <!-- Modal Spesa Lavoro (Dashboard) -->
  <div class="modal fade" id="spesaLavoroModalDashboard" tabindex="-1" aria-labelledby="spesaLavoroModalDashboardLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="spesaLavoroModalDashboardLabel">
            <i class="bi bi-currency-euro"></i> Spesa Lavoro
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <form action="" method="POST" id="spesaLavoroFormDashboard">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="metodo_pagamento_spesa_dashboard" class="form-label">Fonte <span class="text-danger">*</span></label>
              <select class="form-select" id="metodo_pagamento_spesa_dashboard" name="metodo_pagamento" required>
                <option value="">Seleziona fonte...</option>
                <option value="contanti">Fondo Cassa (€ {{ number_format($worker->fondo_cassa, 2, ',', '.') }})</option>
                @if($carteAssegnate->isNotEmpty())
                  @if($carteAssegnate->count() === 1)
                    @php $carta = $carteAssegnate->first(); @endphp
                    <option value="carta" data-card-id="{{ $carta->id }}">
                      Carta Prepagata {{ substr($carta->numero_carta, 0, 4) }} **** {{ substr($carta->numero_carta, -4) }}
                      (€ {{ number_format($carta->fondo_carta, 2, ',', '.') }})
                    </option>
                  @else
                    <option value="carta">Carta Prepagata (seleziona sotto)</option>
                  @endif
                @endif
              </select>
            </div>

            @if($carteAssegnate->count() > 1)
            <div class="mb-3" id="cartaSelectContainerDashboard" style="display:none;">
              <label for="credit_card_id_spesa_dashboard" class="form-label">Seleziona Carta <span class="text-danger">*</span></label>
              <select class="form-select" id="credit_card_id_spesa_dashboard">
                <option value="">Seleziona carta...</option>
                @foreach($carteAssegnate as $carta)
                  <option value="{{ $carta->id }}" data-saldo="{{ $carta->fondo_carta }}">
                    Carta #{{ $carta->id }} –
                    {{ substr($carta->numero_carta, 0, 4) }} **** {{ substr($carta->numero_carta, -4) }}
                    (€ {{ number_format($carta->fondo_carta, 2, ',', '.') }})
                  </option>
                @endforeach
              </select>
            </div>
            @endif

            <input type="hidden" id="credit_card_id_hidden_dashboard" name="credit_card_id" value="">

            <div class="mb-3">
              <label for="importo_spesa_dashboard" class="form-label">Somma (€) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">€</span>
                <input type="number" class="form-control" id="importo_spesa_dashboard" name="importo" step="0.01" min="0.01" required>
              </div>
            </div>

            <div class="mb-3">
              <label for="causale_spesa_dashboard" class="form-label">Causale <span class="text-danger">*</span></label>
              <textarea class="form-control" id="causale_spesa_dashboard" name="causale" rows="3" required></textarea>
              <small class="text-muted" id="spesaWorkLabelDashboard">Seleziona un lavoro per vedere i dettagli.</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
            <button type="submit" class="btn btn-danger">
              <i class="bi bi-save"></i> Registra Spesa
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif

  <!-- Modal Incasso Lavoro (Dashboard) -->
  <div class="modal fade" id="incassoLavoroModalDashboard" tabindex="-1" aria-labelledby="incassoLavoroModalDashboardLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="incassoLavoroModalDashboardLabel">
            <i class="bi bi-currency-euro"></i> Incasso Lavoro
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <form action="" method="POST" id="incassoLavoroFormDashboard">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="metodo_pagamento" value="contanti">

            <div class="mb-3">
              <label for="importo_incasso_dashboard" class="form-label">Somma (€) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">€</span>
                <input type="number" class="form-control" id="importo_incasso_dashboard" name="importo" step="0.01" min="0.01" required>
              </div>
            </div>

            <div class="mb-3">
              <label for="causale_incasso_dashboard" class="form-label">Causale <span class="text-danger">*</span></label>
              <textarea class="form-control" id="causale_incasso_dashboard" name="causale" rows="3" required></textarea>
              <small class="text-muted" id="incassoWorkLabelDashboard">Seleziona un lavoro per vedere i dettagli.</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
            <button type="submit" class="btn btn-success">
              <i class="bi bi-save"></i> Registra Incasso
            </button>
          </div>
        </form>
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
            "order": [[ 0, "asc" ]]
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
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        // --- Spesa Lavoro modal ---
        const spesaButtons = document.querySelectorAll('.btn-spesa-lavoro');
        const spesaForm = document.getElementById('spesaLavoroFormDashboard');
        const spesaWorkLabel = document.getElementById('spesaWorkLabelDashboard');

        spesaButtons.forEach(function (btn) {
          btn.addEventListener('click', function () {
            const workId = this.getAttribute('data-work-id');
            const workLabel = this.getAttribute('data-work-label');
            const actionUrl = '{{ url("/worker/jobs") }}/' + workId + '/spesa';
            spesaForm.setAttribute('action', actionUrl);
            spesaWorkLabel.textContent = 'Verrà aggiunto automaticamente: ' + workLabel;
          });
        });

        // Gestione fonte pagamento (carta/contanti)
        const metodoPagamentoDashboard = document.getElementById('metodo_pagamento_spesa_dashboard');
        const hiddenCardIdDashboard = document.getElementById('credit_card_id_hidden_dashboard');
        @if($worker && $carteAssegnate->count() > 1)
        const cartaSelectContainerDashboard = document.getElementById('cartaSelectContainerDashboard');
        const cartaSelectDashboard = document.getElementById('credit_card_id_spesa_dashboard');
        @endif

        if (metodoPagamentoDashboard) {
          metodoPagamentoDashboard.addEventListener('change', function () {
            const val = this.value;
            if (val === 'carta') {
              @if($worker && $carteAssegnate->count() === 1)
                hiddenCardIdDashboard.value = this.options[this.selectedIndex].dataset.cardId || '';
              @elseif($worker && $carteAssegnate->count() > 1)
                cartaSelectContainerDashboard.style.display = 'block';
                hiddenCardIdDashboard.value = '';
              @else
                hiddenCardIdDashboard.value = '';
              @endif
            } else {
              @if($worker && $carteAssegnate->count() > 1)
                cartaSelectContainerDashboard.style.display = 'none';
              @endif
              hiddenCardIdDashboard.value = '';
            }
          });
        }

        @if($worker && $carteAssegnate->count() > 1)
        if (cartaSelectDashboard) {
          cartaSelectDashboard.addEventListener('change', function () {
            hiddenCardIdDashboard.value = this.value;
          });
        }
        @endif

        // Reset form on modal close
        const spesaModalEl = document.getElementById('spesaLavoroModalDashboard');
        if (spesaModalEl) {
          spesaModalEl.addEventListener('hidden.bs.modal', function () {
            spesaForm.reset();
            spesaWorkLabel.textContent = 'Seleziona un lavoro per vedere i dettagli.';
          });
        }

        // --- Incasso Lavoro modal ---
        const incassoButtons = document.querySelectorAll('.btn-incasso-lavoro');
        const incassoForm = document.getElementById('incassoLavoroFormDashboard');
        const incassoWorkLabel = document.getElementById('incassoWorkLabelDashboard');

        incassoButtons.forEach(function (btn) {
          btn.addEventListener('click', function () {
            const workId = this.getAttribute('data-work-id');
            const workLabel = this.getAttribute('data-work-label');
            const actionUrl = '{{ url("/worker/jobs") }}/' + workId + '/incasso';
            incassoForm.setAttribute('action', actionUrl);
            incassoWorkLabel.textContent = 'Verrà aggiunto automaticamente: ' + workLabel;
          });
        });

        // Reset form on modal close
        const incassoModalEl = document.getElementById('incassoLavoroModalDashboard');
        if (incassoModalEl) {
          incassoModalEl.addEventListener('hidden.bs.modal', function () {
            incassoForm.reset();
            incassoWorkLabel.textContent = 'Seleziona un lavoro per vedere i dettagli.';
          });
        }
      });
    </script>
  @endif
@endsection

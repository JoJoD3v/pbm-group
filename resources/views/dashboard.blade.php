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
                            @foreach($work->workers as $w)
                              @if($w->colore_bg)
                                <span class="badge" style="background-color: {{ $w->colore_bg }}; color: {{ $w->colore_font ?? '#000' }}; font-size: inherit; padding: 0.35em 0.65em;">{{ $w->full_name }}</span>{{ !$loop->last ? ', ' : '' }}
                              @else
                                {{ $w->full_name }}{{ !$loop->last ? ', ' : '' }}
                              @endif
                            @endforeach
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
            <a href="{{ route('dashboard', ['date' => $prevDate, 'tab' => $tab]) }}" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-chevron-left"></i>
            </a>
            <h6 class="m-0 font-weight-bold text-primary">
              Lavori Assegnati{{ $isToday ? ' Oggi' : '' }} ({{ $currentDate->format('d/m/Y') }})
            </h6>
            <a href="{{ route('dashboard', ['date' => $nextDate, 'tab' => $tab]) }}" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-chevron-right"></i>
            </a>
          </div>
          <div class="card-body">
            @if(count($tabs) > 1)
              <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                  <a class="nav-link {{ $tab === 'tutti' ? 'active' : '' }}" href="{{ route('dashboard', ['date' => $currentDate->format('Y-m-d'), 'tab' => 'tutti']) }}">Tutti</a>
                </li>
                @foreach($tabs as $tabKey => $tabInfo)
                  <li class="nav-item">
                    <a class="nav-link {{ $tab === $tabKey ? 'active' : '' }}" href="{{ route('dashboard', ['date' => $currentDate->format('Y-m-d'), 'tab' => $tabKey]) }}">{{ $tabInfo['label'] }}</a>
                  </li>
                @endforeach
              </ul>
            @endif
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
                          <button type="button" class="btn btn-primary px-3 py-2 btn-movimento-lavoro"
                                  data-bs-toggle="modal" data-bs-target="#movimentoLavoroModalDashboard"
                                  data-work-id="{{ $work->id }}"
                                  data-work-label="Lavoro #{{ $work->id }} ({{ $work->customer ? ($work->customer->ragione_sociale ?? $work->customer->full_name) : 'N/D' }})">
                            <i class="bi bi-currency-euro"></i>
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
  <!-- Modal Movimento Lavoro Unificato (Dashboard) -->
  <div class="modal fade" id="movimentoLavoroModalDashboard" tabindex="-1" aria-labelledby="movimentoLavoroModalDashboardLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" id="movimentoModalHeader">
          <h5 class="modal-title" id="movimentoLavoroModalDashboardLabel">
            <i class="bi bi-currency-euro"></i> Movimento Lavoro
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Chiudi"></button>
        </div>
        <form action="" method="POST" id="movimentoLavoroFormDashboard">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="tipo_movimento_dashboard" class="form-label">Tipo <span class="text-danger">*</span></label>
              <select class="form-select" id="tipo_movimento_dashboard" name="tipo" required>
                <option value="">Seleziona tipo...</option>
                <option value="spesa">Spesa</option>
                <option value="incasso">Incasso</option>
              </select>
            </div>

            <div id="fonteSectionDashboard" style="display:none;">
              <div class="mb-3">
                <label for="metodo_pagamento_dashboard" class="form-label">Fonte <span class="text-danger">*</span></label>
                <select class="form-select" id="metodo_pagamento_dashboard" name="metodo_pagamento">
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
                <label for="credit_card_id_dashboard" class="form-label">Seleziona Carta <span class="text-danger">*</span></label>
                <select class="form-select" id="credit_card_id_dashboard">
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
            </div>

            <input type="hidden" id="metodo_pagamento_hidden_dashboard" name="metodo_pagamento" value="">

            <div class="mb-3">
              <label for="importo_dashboard" class="form-label">Somma (€) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">€</span>
                <input type="number" class="form-control" id="importo_dashboard" name="importo" step="0.01" min="0.01" required>
              </div>
            </div>

            <div class="mb-3">
              <label for="causale_dashboard" class="form-label">Causale <span class="text-danger">*</span></label>
              <textarea class="form-control" id="causale_dashboard" name="causale" rows="3" required></textarea>
              <small class="text-muted" id="movimentoWorkLabelDashboard">Seleziona un lavoro per vedere i dettagli.</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
            <button type="submit" class="btn" id="movimentoSubmitBtn">
              <i class="bi bi-save"></i> Registra
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif
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
        const movimentoButtons = document.querySelectorAll('.btn-movimento-lavoro');
        const movimentoForm = document.getElementById('movimentoLavoroFormDashboard');
        const movimentoWorkLabel = document.getElementById('movimentoWorkLabelDashboard');

        // Aggiorna form action al click del bottone riga
        movimentoButtons.forEach(function (btn) {
          btn.addEventListener('click', function () {
            const workId = this.getAttribute('data-work-id');
            const workLabel = this.getAttribute('data-work-label');
            const actionUrl = '{{ url("/worker/jobs") }}/' + workId + '/movimento';
            movimentoForm.setAttribute('action', actionUrl);
            movimentoWorkLabel.textContent = 'Verrà aggiunto automaticamente: ' + workLabel;
          });
        });

        // Elementi dinamici
        const tipoSelect = document.getElementById('tipo_movimento_dashboard');
        const fonteSection = document.getElementById('fonteSectionDashboard');
        const metodoPagamentoSelect = document.getElementById('metodo_pagamento_dashboard');
        const hiddenCardId = document.getElementById('credit_card_id_hidden_dashboard');
        const hiddenMetodoPagamento = document.getElementById('metodo_pagamento_hidden_dashboard');
        const modalHeader = document.getElementById('movimentoModalHeader');
        const submitBtn = document.getElementById('movimentoSubmitBtn');
        @if($worker && $carteAssegnate->count() > 1)
        const cartaSelectContainer = document.getElementById('cartaSelectContainerDashboard');
        const cartaSelect = document.getElementById('credit_card_id_dashboard');
        @endif

        // Toggle Fonte section e colori in base al tipo
        tipoSelect.addEventListener('change', function () {
          const tipo = this.value;

          if (tipo === 'spesa') {
            fonteSection.style.display = 'block';
            metodoPagamentoSelect.setAttribute('required', 'required');
            hiddenMetodoPagamento.value = '';
            modalHeader.className = 'modal-header bg-danger text-white';
            submitBtn.className = 'btn btn-danger';
            submitBtn.innerHTML = '<i class="bi bi-save"></i> Registra Spesa';
          } else if (tipo === 'incasso') {
            fonteSection.style.display = 'none';
            metodoPagamentoSelect.removeAttribute('required');
            hiddenMetodoPagamento.value = 'contanti';
            hiddenCardId.value = '';
            modalHeader.className = 'modal-header bg-success text-white';
            submitBtn.className = 'btn btn-success';
            submitBtn.innerHTML = '<i class="bi bi-save"></i> Registra Incasso';
          } else {
            fonteSection.style.display = 'none';
            metodoPagamentoSelect.removeAttribute('required');
            hiddenMetodoPagamento.value = '';
            modalHeader.className = 'modal-header';
            submitBtn.className = 'btn';
            submitBtn.innerHTML = '<i class="bi bi-save"></i> Registra';
          }
        });

        // Gestione fonte pagamento (carta/contanti)
        if (metodoPagamentoSelect) {
          metodoPagamentoSelect.addEventListener('change', function () {
            const val = this.value;
            if (val === 'carta') {
              @if($worker && $carteAssegnate->count() === 1)
                hiddenCardId.value = this.options[this.selectedIndex].dataset.cardId || '';
                hiddenMetodoPagamento.value = 'carta';
              @elseif($worker && $carteAssegnate->count() > 1)
                cartaSelectContainer.style.display = 'block';
                hiddenCardId.value = '';
                hiddenMetodoPagamento.value = '';
              @else
                hiddenCardId.value = '';
                hiddenMetodoPagamento.value = '';
              @endif
            } else if (val === 'contanti') {
              @if($worker && $carteAssegnate->count() > 1)
                cartaSelectContainer.style.display = 'none';
              @endif
              hiddenCardId.value = '';
              hiddenMetodoPagamento.value = 'contanti';
            } else {
              @if($worker && $carteAssegnate->count() > 1)
                cartaSelectContainer.style.display = 'none';
              @endif
              hiddenCardId.value = '';
              hiddenMetodoPagamento.value = '';
            }
          });
        }

        @if($worker && $carteAssegnate->count() > 1)
        if (cartaSelect) {
          cartaSelect.addEventListener('change', function () {
            hiddenCardId.value = this.value;
            hiddenMetodoPagamento.value = 'carta';
          });
        }
        @endif

        // Reset form on modal close
        const movimentoModalEl = document.getElementById('movimentoLavoroModalDashboard');
        if (movimentoModalEl) {
          movimentoModalEl.addEventListener('hidden.bs.modal', function () {
            movimentoForm.reset();
            movimentoWorkLabel.textContent = 'Seleziona un lavoro per vedere i dettagli.';
            fonteSection.style.display = 'none';
            modalHeader.className = 'modal-header';
            submitBtn.className = 'btn';
            submitBtn.innerHTML = '<i class="bi bi-save"></i> Registra';
            hiddenMetodoPagamento.value = '';
            hiddenCardId.value = '';
            @if($worker && $carteAssegnate->count() > 1)
            if (cartaSelectContainer) cartaSelectContainer.style.display = 'none';
            @endif
          });
        }
      });
    </script>
  @endif
@endsection

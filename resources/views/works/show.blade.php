@extends('layouts.dashboard')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Lavoro                     
        @if($work->customer)
          {{ $work->customer->customer_type == 'fisica' ? $work->customer->full_name : $work->customer->ragione_sociale }}
        @else          
        @endif
      </h6>
    </div>
    <div class="card-body">
      <div class="row">
        <!-- Colonna sinistra con i dettagli del lavoro -->
        <div class="col-lg-6">
          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title">
                Dettagli Lavoro                    
                <hr>
              </h5>
              <div class="row mb-3">
                <div class="col-md-6">
                  <strong>Tipo Lavoro:</strong>
                  <p>{{ $work->tipo_lavoro }}</p>
                </div>
                <div class="col-md-6">
                  <strong>Cliente:</strong>
                  <p>
                    @if($work->customer)
                      {{ $work->customer->customer_type == 'fisica' ? $work->customer->full_name : $work->customer->ragione_sociale }}
                    @else
                      N/D
                    @endif
                  </p>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">                  <strong>Data Esecuzione:</strong>
                  <p>
                    @if($work->data_esecuzione)
                      @formatDate($work->data_esecuzione)
                    @else
                      N/D
                    @endif
                  </p>
                </div>
                <div class="col-md-6">
                  <strong>Costo Lavoro:</strong>
                  <p>
                    @if($work->costo_lavoro)
                      {{ number_format($work->costo_lavoro, 2, ',', '.') }} €
                    @else
                      N/D
                    @endif
                  </p>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <strong>Modalità Pagamento:</strong>
                  <p>{{ $work->modalita_pagamento ?? 'N/D' }}</p>
                </div>
                <div class="col-md-6">
                  <strong>Materiale:</strong>
                  <p>
                    @if($work->materiale)
                      {{ $work->materiale }}
                      @if($work->codice_eer)
                        ({{ $work->codice_eer }})
                      @endif
                    @else
                      N/D
                    @endif
                  </p>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <strong>Nome Destinazione:</strong>
                  <p>{{ $work->nome_destinazione }}</p>
                </div>
                <div class="col-md-6">
                  <strong>Status Lavoro:</strong>
                  <p>{{ $work->status_lavoro }}</p>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <strong>Indirizzo Destinazione:</strong>
                  <p>{{ $work->indirizzo_destinazione }}</p>
                </div>
                <div class="col-md-6">
                  <strong>Coordinate:</strong>
                  <p>
                    Latitudine: {{ $work->latitude_destinazione }}<br>
                    Longitudine: {{ $work->longitude_destinazione }}
                  </p>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">                  <strong>Data Creazione:</strong>
                  <p>@formatDateTime($work->created_at)</p>
                </div>
                <div class="col-md-6">
                  <strong>Data Aggiornamento:</strong>
                  <p>@formatDateTime($work->updated_at)</p>
                </div>
              </div>

              <div class="mt-3">
                <a href="{{ route('works.edit', $work->id) }}" class="btn btn-warning">
                  <i class="bi bi-pencil"></i> Modifica
                </a>
                <a href="{{ route('works.index') }}" class="btn btn-secondary">
                  <i class="bi bi-arrow-left"></i> Indietro
                </a>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Colonna destra con la mappa -->
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Visualizzazione Mappa</h5>
              <div class="map-container" style="height: 400px; width: 100%;">
                <iframe 
                  width="100%" 
                  height="100%" 
                  frameborder="0" 
                  style="border:0" 
                  src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google_maps.api_key') }}&q={{ number_format($work->latitude_destinazione, 6, '.', '') }},{{ number_format($work->longitude_destinazione, 6, '.', '') }}&zoom=15" 
                  allowfullscreen>
                </iframe>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sezione Ricevute -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card shadow">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Ricevute</h6>
            </div>
            <div class="card-body">
              @if($work->ricevute && $work->ricevute->count() > 0)
                <div class="table-responsive">
                  <table class="table table-bordered table-hover dataTable">
                    <thead class="thead-light">
                      <tr>
                        <th>Numero Ricevuta</th>
                        <th>Nome Ricevente</th>
                        <th>Fattura</th>
                        <th>Pagamento</th>
                        <th>Somma Pagata</th>
                        <th class="datetime-column">Data Creazione</th>
                        <th>Firma</th>
                        <th>Bolla</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($work->ricevute as $ricevuta)
                        <tr>
                          <td>{{ $ricevuta->numero_ricevuta }}</td>
                          <td>{{ $ricevuta->nome_ricevente }}</td>
                          <td>
                            @if($ricevuta->fattura)
                              <span class="badge badge-success">Sì</span>
                            @else
                              <span class="badge badge-secondary">No</span>
                            @endif
                          </td>
                          <td>
                            @if($ricevuta->pagamento_effettuato)
                              <span class="badge badge-success">Effettuato</span>
                            @else
                              <span class="badge badge-warning">Non effettuato</span>
                            @endif
                          </td>
                          <td>
                            @if($ricevuta->somma_pagamento)
                              {{ number_format($ricevuta->somma_pagamento, 2, ',', '.') }} €
                            @else
                              -
                            @endif
                          </td>
                          <td class="datetime-column">@formatDateTime($ricevuta->created_at)</td>
                          <td>
                            @if(Str::startsWith($ricevuta->firma_base64, 'data:image'))
                              <!-- Immagine piccola visualizzabile direttamente -->
                              <div style="max-width: 150px; margin: 0 auto;">
                                <img src="{{ $ricevuta->firma_base64 }}" alt="Firma" class="img-fluid img-thumbnail">
                              </div>

                            @else
                              {{ Str::limit($ricevuta->firma_base64, 30) }}
                            @endif
                          </td>
                          <td>
                            @if($ricevuta->foto_bolla)
                              <a href="{{ asset('storage/' . $ricevuta->foto_bolla) }}" target="_blank" class="btn btn-sm btn-info">
                                Visualizza Bolla
                              </a>
                            @else
                              <span class="text-muted">Nessuna bolla</span>
                            @endif
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="alert alert-info">
                  Nessuna ricevuta disponibile per questo lavoro.
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>  // Script per visualizzare la firma nel modal e inizializzare DataTable
  $(document).ready(function() {
    $('.view-signature').on('click', function() {
      var signatureData = $(this).data('signature');
      $('#signatureImage').attr('src', signatureData);
    });
    
    // Inizializza DataTable con ordinamento per data
    $('.dataTable').DataTable({
      order: [[5, 'desc']], // Ordina per data creazione decrescente
      columnDefs: [
        {
          targets: 5, // Colonna datetime (0-based index)
          type: 'date-eu-time' // Usa il tipo date-eu-time per l'ordinamento con ora e minuti
        }
      ]
    });
  });
</script>
@endsection

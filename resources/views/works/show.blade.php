@extends('layouts.dashboard')

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
                <div class="col-md-6">
                  <strong>Data Esecuzione:</strong>
                  <p>
                    @if($work->data_esecuzione)
                      {{ \Carbon\Carbon::parse($work->data_esecuzione)->format('d/m/Y') }}
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
                <div class="col-md-6">
                  <strong>Data Creazione:</strong>
                  <p>{{ \Carbon\Carbon::parse($work->created_at)->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6">
                  <strong>Data Aggiornamento:</strong>
                  <p>{{ \Carbon\Carbon::parse($work->updated_at)->format('d/m/Y H:i') }}</p>
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
    </div>
  </div>
</div>
@endsection

@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dettagli Lavoro #{{ $work->id }}</h1>
            <a href="{{ route('worker.jobs') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Torna all'elenco
            </a>
        </div>
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informazioni Lavoro</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th style="width: 30%">ID Lavoro:</th>
                                    <td>{{ $work->id }}</td>
                                </tr>
                                <tr>
                                    <th>Tipo Lavoro:</th>
                                    <td>{{ $work->tipo_lavoro }}</td>
                                </tr>
                                <tr>
                                    <th>Stato:</th>
                                    <td>
                                        @if($work->status_lavoro)
                                            <span class="badge bg-{{ $work->status_lavoro == 'Completato' ? 'success' : ($work->status_lavoro == 'In corso' ? 'warning' : 'info') }}">
                                                {{ $work->status_lavoro }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Non assegnato</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Data Esecuzione:</th>
                                    <td>{{ $work->data_esecuzione ?? 'Non pianificato' }}</td>
                                </tr>
                                <tr>
                                    <th>Materiale:</th>
                                    <td>{{ $work->materiale }}</td>
                                </tr>
                                <tr>
                                    <th>Codice EER:</th>
                                    <td>{{ $work->codice_eer ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Dati Cliente</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th style="width: 30%">Cliente:</th>
                                    <td>{{ $work->customer->ragione_sociale ?? $work->customer->full_name }}</td>
                                </tr>
                                <tr>
                                    <th>Indirizzo:</th>
                                    <td>{{ $work->customer->address }}</td>
                                </tr>
                                <tr>
                                    <th>Telefono:</th>
                                    <td>{{ $work->customer->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $work->customer->email }}</td>
                                </tr>
                                <tr>
                                    <th>Costo Lavoro:</th>
                                    <td>{{ $work->costo_lavoro }}</td>
                                </tr>
                                <tr>
                                    <th>Modalità Pagamento:</th>
                                    <td>{{ $work->modalita_pagamento }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Indirizzo di Partenza</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Nome:</strong> {{ $work->nome_partenza ?? 'N/A' }}</p>
                        <p><strong>Indirizzo:</strong> {{ $work->indirizzo_partenza ?? 'N/A' }}</p>
                        
                        @if($work->latitude_partenza && $work->longitude_partenza)
                            <div id="map-partenza" style="height: 250px; width: 100%;"></div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Indirizzo di Destinazione</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Nome:</strong> {{ $work->nome_destinazione }}</p>
                        <p><strong>Indirizzo:</strong> {{ $work->indirizzo_destinazione }}</p>
                        
                        @if($work->latitude_destinazione && $work->longitude_destinazione)
                            <div id="map-destinazione" style="height: 250px; width: 100%;"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(($work->latitude_partenza && $work->longitude_partenza) || ($work->latitude_destinazione && $work->longitude_destinazione))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMaps" async defer></script>
        <script>
            function initMaps() {
                @if($work->latitude_partenza && $work->longitude_partenza)
                    var partenzaLatLng = {lat: {{ $work->latitude_partenza }}, lng: {{ $work->longitude_partenza }}};
                    var mapPartenza = new google.maps.Map(document.getElementById('map-partenza'), {
                        zoom: 15,
                        center: partenzaLatLng
                    });
                    
                    var markerPartenza = new google.maps.Marker({
                        position: partenzaLatLng,
                        map: mapPartenza,
                        title: '{{ $work->nome_partenza }}'
                    });
                @endif
                
                @if($work->latitude_destinazione && $work->longitude_destinazione)
                    var destinazioneLatLng = {lat: {{ $work->latitude_destinazione }}, lng: {{ $work->longitude_destinazione }}};
                    var mapDestinazione = new google.maps.Map(document.getElementById('map-destinazione'), {
                        zoom: 15,
                        center: destinazioneLatLng
                    });
                    
                    var markerDestinazione = new google.maps.Marker({
                        position: destinazioneLatLng,
                        map: mapDestinazione,
                        title: '{{ $work->nome_destinazione }}'
                    });
                @endif
            }
        </script>
    @endif
@endsection 
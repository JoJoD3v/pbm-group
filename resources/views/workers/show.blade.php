@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Dettagli Lavoratore</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <tr>
            <th style="width: 200px;">ID</th>
            <td>{{ $worker->id }}</td>
          </tr>
          <tr>
            <th>ID Lavoratore</th>
            <td>{{ $worker->id_worker }}</td>
          </tr>
          <tr>
            <th>Nome</th>
            <td>{{ $worker->name_worker }}</td>
          </tr>
          <tr>
            <th>Cognome</th>
            <td>{{ $worker->cognome_worker }}</td>
          </tr>
          <tr>
            <th>Licenza</th>
            <td>{{ $worker->license_worker }}</td>
          </tr>
          <tr>
            <th>Email</th>
            <td>{{ $worker->worker_email }}</td>
          </tr>
          <tr>
            <th>Fondo Cassa</th>
            <td>€ {{ number_format($worker->fondo_cassa, 2, ',', '.') }}</td>
          </tr>            <tr>
            <th>Data Creazione</th>
            <td>@formatDateTime($worker->created_at)</td>
          </tr>
          <tr>
            <th>Ultimo Aggiornamento</th>
            <td>@formatDateTime($worker->updated_at)</td>
          </tr>
        </table>
      </div>
      
      <!-- Sezione Carte Prepagate Assegnate -->
      <div class="mt-4">
        <h5 class="font-weight-bold text-primary">Carte Prepagate Assegnate</h5>
        @if($worker->assignedCreditCards && $worker->assignedCreditCards->count() > 0)
          <div class="table-responsive">            <table class="table table-bordered dataTable">
              <thead>
                <tr>
                  <th>ID Carta</th>
                  <th>Numero Carta</th>
                  <th class="date-column">Scadenza</th>
                  <th>Saldo Disponibile</th>
                  <th class="date-column">Data Assegnazione</th>
                </tr>
              </thead>
              <tbody>
                @foreach($worker->assignedCreditCards as $carta)
                <tr>
                  <td>{{ $carta->id }}</td>
                  <td>{{ substr($carta->numero_carta, 0, 4) . ' **** **** ' . substr($carta->numero_carta, -4) }}</td>
                  <td class="date-column">@formatDate($carta->scadenza_carta)</td>
                  <td class="font-weight-bold">€ {{ number_format($carta->fondo_carta, 2, ',', '.') }}</td>
                  <td class="date-column">{{ $carta->pivot ? @formatDate($carta->pivot->created_at) : 'N/D' }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="alert alert-info">
            Nessuna carta prepagata assegnata a questo lavoratore.
          </div>
        @endif
      </div>
      
      <!-- Sezione Autoveicoli Assegnati -->
      <div class="mt-4">
        <h5 class="font-weight-bold text-primary">Autoveicoli Assegnati</h5>
        @if($worker->vehicles && $worker->vehicles->count() > 0)          <div class="table-responsive">
            <table class="table table-bordered dataTable">
              <thead>
                <tr>
                  <th>ID Veicolo</th>
                  <th>Targa</th>
                  <th>Marca</th>
                  <th>Modello</th>
                  <th class="date-column">Data Assegnazione</th>
                  <th>Note</th>
                </tr>
              </thead>
              <tbody>
                @foreach($worker->vehicles as $veicolo)
                <tr>
                  <td>{{ $veicolo->id }}</td>
                  <td>{{ $veicolo->plate_number }}</td>
                  <td>{{ $veicolo->brand }}</td>                  <td>{{ $veicolo->model }}</td>
                  <td class="date-column">{{ $veicolo->pivot ? @formatDate($veicolo->pivot->data_assegnazione) : 'N/D' }}</td>
                  <td>{{ $veicolo->pivot ? $veicolo->pivot->note : '' }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="alert alert-info">
            Nessun autoveicolo assegnato a questo lavoratore.
          </div>
        @endif
      </div>
      
      <div class="mt-3">
        <a href="{{ route('workers.edit', $worker->id) }}" class="btn btn-warning">
          <i class="bi bi-pencil"></i> Modifica
        </a>
        <a href="{{ route('workers.index') }}" class="btn btn-secondary">
          <i class="bi bi-arrow-left"></i> Indietro
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

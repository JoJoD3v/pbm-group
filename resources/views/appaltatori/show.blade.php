@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">
        Scheda Appaltatore —
        @if($appaltatore->tipo_soggetto == 'fisica')
          {{ $appaltatore->full_name }}
        @else
          {{ $appaltatore->ragione_sociale }}
        @endif
      </h6>
    </div>
    <div class="card-body">

      <div class="row mb-3">
        <div class="col-md-6">
          <strong>Tipo Appaltatore:</strong>
          <p>{{ ucfirst($appaltatore->tipo_soggetto) }}</p>
        </div>

        @if($appaltatore->tipo_soggetto == 'fisica')
        <div class="col-md-6">
          <strong>Nome e Cognome:</strong>
          <p>{{ $appaltatore->full_name }}</p>
        </div>
        @else
        <div class="col-md-6">
          <strong>Ragione Sociale:</strong>
          <p>{{ $appaltatore->ragione_sociale }}</p>
        </div>
        @endif
      </div>

      <div class="row mb-3">
        @if($appaltatore->tipo_soggetto == 'fisica')
        <div class="col-md-6">
          <strong>Codice Fiscale:</strong>
          <p>{{ $appaltatore->codice_fiscale ?? 'N/D' }}</p>
        </div>
        @else
        <div class="col-md-6">
          <strong>Partita IVA:</strong>
          <p>{{ $appaltatore->partita_iva ?? 'N/D' }}</p>
        </div>
        @endif
        <div class="col-md-6">
          <strong>Indirizzo:</strong>
          <p>{{ $appaltatore->address }}</p>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <strong>Telefono:</strong>
          <p>{{ $appaltatore->phone ?? 'N/D' }}</p>
        </div>
        <div class="col-md-6">
          <strong>Email:</strong>
          <p>{{ $appaltatore->email ?? 'N/D' }}</p>
        </div>
      </div>

      @if($appaltatore->latitude_appaltatore && $appaltatore->longitude_appaltatore)
      <div class="row mb-3">
        <div class="col-md-6">
          <strong>Coordinate:</strong>
          <p>{{ $appaltatore->latitude_appaltatore }}, {{ $appaltatore->longitude_appaltatore }}</p>
        </div>
      </div>
      @endif

      @if($appaltatore->note)
      <div class="row mb-3">
        <div class="col-12">
          <strong>Note:</strong>
          <p class="mb-0" style="white-space: pre-wrap;">{{ $appaltatore->note }}</p>
        </div>
      </div>
      @endif

      <div class="mt-4">
        <a href="{{ route('appaltatori.edit', $appaltatore->id) }}" class="btn btn-warning">
          <i class="bi bi-pencil"></i> Modifica
        </a>
        <a href="{{ route('appaltatori.index') }}" class="btn btn-secondary">
          <i class="bi bi-arrow-left"></i> Indietro
        </a>
      </div>

    </div>
  </div>
</div>
@endsection

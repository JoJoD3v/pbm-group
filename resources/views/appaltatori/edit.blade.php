@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Modifica Appaltatore</h6>
    </div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('appaltatori.update', $appaltatore->id) }}" method="POST" id="appaltatoreForm">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label">Tipo di Appaltatore</label>
          <div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="tipo_soggetto" id="fisica" value="fisica" {{ $appaltatore->tipo_soggetto == 'fisica' ? 'checked' : '' }}>
              <label class="form-check-label" for="fisica">Persona Fisica</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="tipo_soggetto" id="giuridica" value="giuridica" {{ $appaltatore->tipo_soggetto == 'giuridica' ? 'checked' : '' }}>
              <label class="form-check-label" for="giuridica">Persona Giuridica</label>
            </div>
          </div>
        </div>

        <!-- Campi per Persona Fisica -->
        <div id="fisicaFields">
          <div class="mb-3">
            <label for="full_name" class="form-label">Nome e Cognome</label>
            <input type="text" name="full_name" id="full_name" class="form-control" value="{{ $appaltatore->full_name }}">
          </div>
          <div class="mb-3">
            <label for="codice_fiscale" class="form-label">Codice Fiscale</label>
            <input type="text" name="codice_fiscale" id="codice_fiscale" class="form-control" value="{{ $appaltatore->codice_fiscale }}">
          </div>
        </div>

        <!-- Campi per Persona Giuridica -->
        <div id="giuridicaFields" style="display:none;">
          <div class="mb-3">
            <label for="ragione_sociale" class="form-label">Ragione Sociale</label>
            <input type="text" name="ragione_sociale" id="ragione_sociale" class="form-control" value="{{ $appaltatore->ragione_sociale }}">
          </div>
          <div class="mb-3">
            <label for="partita_iva" class="form-label">Partita Iva</label>
            <input type="text" name="partita_iva" id="partita_iva" class="form-control" value="{{ $appaltatore->partita_iva }}">
          </div>
          <div class="mb-3">
            <label for="codice_fiscale_giuridica" class="form-label">Codice Fiscale</label>
            <input type="text" name="codice_fiscale" id="codice_fiscale_giuridica" class="form-control" value="{{ $appaltatore->codice_fiscale }}">
          </div>
        </div>

        <!-- Campi comuni -->
        <div class="mb-3">
          <label for="address" class="form-label">Indirizzo</label>
          <input type="text" name="address" id="address" class="form-control" value="{{ $appaltatore->address }}" required>
        </div>
        <div class="mb-3">
          <label for="phone" class="form-label">Numero di Telefono</label>
          <input type="text" name="phone" id="phone" class="form-control" value="{{ $appaltatore->phone }}">
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" id="email" class="form-control" value="{{ $appaltatore->email }}">
        </div>
        <div class="mb-3">
          <input type="hidden" name="longitude_appaltatore" id="longitude" value="{{ $appaltatore->longitude_appaltatore }}" class="form-control" placeholder="Longitudine" >
          <input type="hidden" name="latitude_appaltatore" id="latitude" value="{{ $appaltatore->latitude_appaltatore }}" class="form-control" placeholder="Latitudine" >
        </div>

        <div class="mb-3">
          <label for="note" class="form-label">Note</label>
          <textarea name="note" id="note" class="form-control" rows="3">{{ old('note', $appaltatore->note) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Aggiorna</button>
        <a href="{{ route('appaltatori.index') }}" class="btn btn-secondary">Indietro</a>
      </form>
    </div>
  </div>
</div>

<script src="{{ asset('js/customer-edit.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places"></script>
<script src="{{ asset('js/deposit-address.js') }}"></script>

@endsection

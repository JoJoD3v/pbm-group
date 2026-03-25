@extends('layouts.dashboard')

@section('content')
<div class="container">
  <h1 class="h3 mb-4 text-gray-800">Aggiungi Discarica</h1>

  @if($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif

  <form action="{{ route('deposits.store') }}" method="POST">
      @csrf
      <div class="mb-3">
          <label for="name" class="form-label">Nome Discarica</label>
          <input type="text" name="name" id="name" class="form-control" required>
      </div>
      <div class="mb-3">
          <label for="address" class="form-label">Indirizzo Discarica</label>
          <input type="text" name="address" id="address" class="form-control" required>
      </div>
      <div class="mb-3">
          <label for="n_aut_comunicazione" class="form-label">N. Aut. Comunicazione</label>
          <input type="text" name="n_aut_comunicazione" id="n_aut_comunicazione" class="form-control" value="{{ old('n_aut_comunicazione') }}">
      </div>
      <div class="mb-3">
          <label for="numero_iscrizione_albo" class="form-label">Numero iscrizione albo</label>
          <input type="text" name="numero_iscrizione_albo" id="numero_iscrizione_albo" class="form-control" value="{{ old('numero_iscrizione_albo') }}">
      </div>
      <div class="mb-3">
          <label for="tipo" class="form-label">Tipo</label>
          <input type="text" name="tipo" id="tipo" class="form-control" value="{{ old('tipo') }}">
      </div>
      <div class="mb-3">
          <label for="destinazione" class="form-label">Destinazione</label>
          <input type="text" name="destinazione" id="destinazione" class="form-control" value="{{ old('destinazione') }}">
      </div>
      <div class="mb-3">
          <label for="data_scadenza" class="form-label">Data Scadenza</label>
          <input type="date" name="data_scadenza" id="data_scadenza" class="form-control" value="{{ old('data_scadenza') }}">
      </div>
      <div class="mb-3">
          <label for="latitude" class="form-label">Latitudine</label>
          <input type="text" name="latitude" id="latitude" class="form-control" placeholder="Latitudine" >
      </div>
      <div class="mb-3">
          <label for="longitude" class="form-label">Longitudine</label>
          <input type="text" name="longitude" id="longitude" class="form-control" placeholder="Longitudine" >
      </div>
      <div class="mb-3">
          <label class="form-label">Materiali Accettati</label>
          <div class="form-check">
              @foreach($materials as $material)
                  <input class="form-check-input" type="checkbox" name="materials[]" id="material{{ $material->id }}" value="{{ $material->id }}">
                  <label class="form-check-label" for="material{{ $material->id }}">{{ $material->name }}</label><br>
              @endforeach
          </div>
      </div>

      <button type="submit" class="btn btn-primary">Salva</button>
      <a href="{{ route('deposits.index') }}" class="btn btn-secondary">Indietro</a>
  </form>
</div>

<!-- Includi lo script di Google Maps con Places Library -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places"></script>
<script src="{{ asset('js/deposit-address.js') }}"></script>

@endsection

@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Aggiungi Cliente</h6>
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

      <form action="{{ route('customers.store') }}" method="POST" id="customerForm">
        @csrf

        <!-- Selezione tipo di customer -->
        <div class="mb-3">
          <label class="form-label">Tipo di Cliente</label>
          <div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="customer_type" id="fisica" value="fisica" checked>
              <label class="form-check-label" for="fisica">Persona Fisica</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="customer_type" id="giuridica" value="giuridica">
              <label class="form-check-label" for="giuridica">Persona Giuridica</label>
            </div>
          </div>
        </div>

        <!-- Campi per Persona Fisica -->
        <div id="fisicaFields">
            <div class="mb-3">
              <label for="full_name" class="form-label">Nome e Cognome</label>
              <input type="text" name="full_name" id="full_name" class="form-control">
            </div>
            <div class="mb-3">
              <label for="codice_fiscale" class="form-label">Codice Fiscale</label>
              <input type="text" name="codice_fiscale" id="codice_fiscale" class="form-control">
            </div>
        </div>


        <!-- Campi per Persona Giuridica -->
        <div id="giuridicaFields" style="display:none;">
            <div class="mb-3">
              <label for="ragione_sociale" class="form-label">Ragione Sociale</label>
              <input type="text" name="ragione_sociale" id="ragione_sociale" class="form-control">
            </div>
            <div class="mb-3">
              <label for="partita_iva" class="form-label">Partita Iva</label>
              <input type="text" name="partita_iva" id="partita_iva" class="form-control">
            </div>
        </div>

        <!-- Campi comuni -->
        <div class="mb-3">
          <label for="address" class="form-label">Indirizzo</label>
          <input type="text" name="address" id="address" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="phone" class="form-label">Numero di Telefono</label>
          <input type="text" name="phone" id="phone" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <input type="hidden" name="latitude_customer" id="latitude" class="form-control" placeholder="Latitudine" >
          <input type="hidden" name="longitude_customer" id="longitude" class="form-control" placeholder="Longitudine" >
        </div>

        <button type="submit" class="btn btn-primary">Salva</button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Indietro</a>
      </form>
    </div>
  </div>
</div>

<!-- Script per gestire il toggle dei campi in base al tipo di customer -->
<script src="{{ asset('js/customer-create.js') }}"></script>
<!-- Includi lo script di Google Maps con Places Library -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places"></script>
<script src="{{ asset('js/deposit-address.js') }}"></script>

@endsection

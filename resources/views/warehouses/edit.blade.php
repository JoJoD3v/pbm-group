@extends('layouts.dashboard')

@section('content')
<div class="container">
  <h1 class="h3 mb-4 text-gray-800">Modifica Cantiere</h1>

  @if($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif

  <form action="{{ route('warehouses.update', $warehouse->id) }}" method="POST">
      @csrf
      @method('PUT')
      
      <div class="mb-3">
          <label for="name" class="form-label">Nome Cantiere</label>
          <input type="text" name="nome_sede" id="name" class="form-control" value="{{ $warehouse->nome_sede }}" >
      </div>
      
      <div class="mb-3">
          <label for="address" class="form-label">Indirizzo Cantiere</label>
          <input type="text" name="indirizzo" id="address" class="form-control" value="{{ $warehouse->indirizzo }}" >
      </div>
      
      <div class="mb-3">
          <label for="latitude" class="form-label">Latitudine Cantiere</label>
          <input type="text" name="latitude_warehouse" id="latitude" class="form-control" value="{{ $warehouse->latitude_warehouse }}" placeholder="Inserisci latitudine" readonly>
      </div>
      
      <div class="mb-3">
          <label for="longitude" class="form-label">Longitudine Cantiere</label>
          <input type="text" name="longitude_warehouse" id="longitude" class="form-control" value="{{ $warehouse->longitude_warehouse }}" placeholder="Inserisci longitudine" readonly>
      </div>

      
      <button type="submit" class="btn btn-primary">Aggiorna</button>
      <a href="{{ route('warehouses.index') }}" class="btn btn-secondary">Indietro</a>
  </form>
</div>

<!-- Includi la Google Maps API con la Places Library -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places"></script>
<!-- Includi il file JS esterno per l'autocompletamento dell'indirizzo -->
<script src="{{ asset('js/deposit-address.js') }}"></script>
@endsection

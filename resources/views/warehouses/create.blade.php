@extends('layouts.dashboard')

@section('content')
<div class="container">
  <h1 class="h3 mb-4 text-gray-800">Aggiungi Cantiere</h1>

  @if($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif

  <form action="{{ route('warehouses.store') }}" method="POST">
      @csrf
      <div class="mb-3">
          <label for="name" class="form-label">Nome Cantiere</label>
          <input type="text" name="nome_sede" id="name" class="form-control" required>
      </div>
      <div class="mb-3">
          <label for="address" class="form-label">Indirizzo Cantiere</label>
          <input type="text" name="indirizzo" id="address" class="form-control" required>
      </div>
      <div class="mb-3">
          <label for="latitude" class="form-label">Latitudine</label>
          <input type="text" name="latitude_warehouse" id="latitude" class="form-control" placeholder="Latitudine" >
      </div>
      <div class="mb-3">
          <label for="longitude" class="form-label">Longitudine</label>
          <input type="text" name="longitude_warehouse" id="longitude" class="form-control" placeholder="Longitudine" >
      </div>

      <button type="submit" class="btn btn-primary">Salva</button>
      <a href="{{ route('warehouses.index') }}" class="btn btn-secondary">Indietro</a>
  </form>
</div>

<!-- Includi lo script di Google Maps con Places Library -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places"></script>
<script src="{{ asset('js/deposit-address.js') }}"></script>

@endsection

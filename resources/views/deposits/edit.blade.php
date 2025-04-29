@extends('layouts.dashboard')

@section('content')
<div class="container">
  <h1 class="h3 mb-4 text-gray-800">Modifica Deposito</h1>

  @if($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif

  <form action="{{ route('deposits.update', $deposit->id) }}" method="POST">
      @csrf
      @method('PUT')
      
      <div class="mb-3">
          <label for="name" class="form-label">Nome Deposito</label>
          <input type="text" name="name" id="name" class="form-control" value="{{ $deposit->name }}" required>
      </div>
      
      <div class="mb-3">
          <label for="address" class="form-label">Indirizzo Deposito</label>
          <input type="text" name="address" id="address" class="form-control" value="{{ $deposit->address }}" required>
      </div>
      
      <div class="mb-3">
          <label for="latitude" class="form-label">Latitudine</label>
          <input type="text" name="latitude" id="latitude" class="form-control" value="{{ $deposit->latitude }}" placeholder="Inserisci latitudine" readonly>
      </div>
      
      <div class="mb-3">
          <label for="longitude" class="form-label">Longitudine</label>
          <input type="text" name="longitude" id="longitude" class="form-control" value="{{ $deposit->longitude }}" placeholder="Inserisci longitudine" readonly>
      </div>
      
      <div class="mb-3">
          <label class="form-label">Materiali Accettati</label>
          <div class="form-check">
              @foreach($materials as $material)
                  <input class="form-check-input" type="checkbox" name="materials[]" id="material{{ $material->id }}" value="{{ $material->id }}"
                  {{ in_array($material->id, $selectedMaterials) ? 'checked' : '' }}>
                  <label class="form-check-label" for="material{{ $material->id }}">{{ $material->name }}</label><br>
              @endforeach
          </div>
      </div>
      
      <button type="submit" class="btn btn-primary">Aggiorna</button>
      <a href="{{ route('deposits.index') }}" class="btn btn-secondary">Indietro</a>
  </form>
</div>

<!-- Includi la Google Maps API con la Places Library -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places"></script>
<!-- Includi il file JS esterno per l'autocompletamento dell'indirizzo -->
<script src="{{ asset('js/deposit-address.js') }}"></script>
@endsection

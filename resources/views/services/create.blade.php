@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Aggiungi Servizio</h6>
    </div>
    <div class="card-body">

      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('services.store') }}" method="POST">
        @csrf

        <div class="mb-3">
          <label for="nome_servizio" class="form-label">Nome Servizio <span class="text-danger">*</span></label>
          <input type="text" name="nome_servizio" id="nome_servizio" class="form-control @error('nome_servizio') is-invalid @enderror"
                 value="{{ old('nome_servizio') }}" required>
          @error('nome_servizio')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="prezzo_servizio" class="form-label">Prezzo Servizio (€)</label>
          <div class="input-group">
            <span class="input-group-text">€</span>
            <input type="number" name="prezzo_servizio" id="prezzo_servizio" class="form-control @error('prezzo_servizio') is-invalid @enderror"
                   step="0.01" min="0" value="{{ old('prezzo_servizio') }}">
          </div>
          @error('prezzo_servizio')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <button type="submit" class="btn btn-primary">Salva</button>
        <a href="{{ route('services.index') }}" class="btn btn-secondary">Indietro</a>
      </form>

    </div>
  </div>
</div>
@endsection

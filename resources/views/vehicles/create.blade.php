@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Aggiungi Nuovo Automezzo</h6>
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

      <form action="{{ route('vehicles.store') }}" method="POST">
        @csrf

        <!-- Nome Automezzo -->
        <div class="mb-3">
          <label for="nome" class="form-label">Nome Automezzo</label>
          <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome') }}" required>
        </div>

        <!-- Targa -->
        <div class="mb-3">
          <label for="targa" class="form-label">Numero di Targa</label>
          <input type="text" name="targa" id="targa" class="form-control" value="{{ old('targa') }}" required>
        </div>

        <!-- Scadenza Assicurazione -->
        <div class="mb-3">
          <label for="scadenza_assicurazione" class="form-label">Scadenza Assicurazione</label>
          <input type="date" name="scadenza_assicurazione" id="scadenza_assicurazione" class="form-control" value="{{ old('scadenza_assicurazione') }}">
          <small class="text-muted">Opzionale</small>
        </div>

        <button type="submit" class="btn btn-primary">Salva Automezzo</button>
        <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">Indietro</a>
      </form>
    </div>
  </div>
</div>
@endsection

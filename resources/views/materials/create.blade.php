@extends('layouts.dashboard')

@section('content')
<div class="container">
  <h1 class="h3 mb-4 text-gray-800">Aggiungi Materiale</h1>
  
  @if($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif
  
  <form action="{{ route('materials.store') }}" method="POST">
      @csrf
      <div class="mb-3">
          <label for="name" class="form-label">Nome</label>
          <input type="text" name="name" id="name" class="form-control" required>
      </div>
      <div class="mb-3">
          <label for="eer_code" class="form-label">EER Code</label>
          <input type="text" name="eer_code" id="eer_code" class="form-control">
      </div>
      <div class="mb-3">
          <label for="prezzo" class="form-label">Prezzo (€)</label>
          <input type="number" step="0.01" min="0" name="prezzo" id="prezzo" class="form-control" value="{{ old('prezzo') }}">
      </div>
      <div class="mb-3">
          <label for="note" class="form-label">Note</label>
          <textarea name="note" id="note" class="form-control" rows="3">{{ old('note') }}</textarea>
      </div>
      <button type="submit" class="btn btn-primary">Salva</button>
      <a href="{{ route('materials.index') }}" class="btn btn-secondary">Indietro</a>
  </form>
</div>
@endsection

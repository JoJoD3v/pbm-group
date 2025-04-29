@extends('layouts.dashboard')

@section('content')
<div class="container">
  <h1 class="h3 mb-4 text-gray-800">Modifica Materiale</h1>
  
  @if($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif
  
  <form action="{{ route('materials.update', $material->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="mb-3">
          <label for="name" class="form-label">Nome</label>
          <input type="text" name="name" id="name" class="form-control" value="{{ $material->name }}" required>
      </div>
      <div class="mb-3">
          <label for="eer_code" class="form-label">EER Code</label>
          <input type="text" name="eer_code" id="eer_code" class="form-control" value="{{ $material->eer_code }}">
      </div>
      <button type="submit" class="btn btn-primary">Aggiorna</button>
      <a href="{{ route('materials.index') }}" class="btn btn-secondary">Indietro</a>
  </form>
</div>
@endsection

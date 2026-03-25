@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Scheda Materiale — {{ $material->name }}</h6>
    </div>
    <div class="card-body">

      <div class="row mb-3">
        <div class="col-md-4">
          <strong>Nome:</strong>
          <p>{{ $material->name }}</p>
        </div>
        <div class="col-md-4">
          <strong>EER Code:</strong>
          <p>{{ $material->eer_code ?? 'N/D' }}</p>
        </div>
        <div class="col-md-4">
          <strong>Prezzo:</strong>
          <p>{{ $material->prezzo !== null ? number_format($material->prezzo, 2, ',', '.') . ' €' : 'N/D' }}</p>
        </div>
      </div>

      @if($material->note)
      <div class="row mb-3">
        <div class="col-12">
          <strong>Note:</strong>
          <p class="mb-0" style="white-space: pre-wrap;">{{ $material->note }}</p>
        </div>
      </div>
      @endif

      <div class="mt-4">
        <a href="{{ route('materials.edit', $material->id) }}" class="btn btn-warning">
          <i class="bi bi-pencil"></i> Modifica
        </a>
        <a href="{{ route('materials.index') }}" class="btn btn-secondary">
          <i class="bi bi-arrow-left"></i> Indietro
        </a>
      </div>

    </div>
  </div>
</div>
@endsection

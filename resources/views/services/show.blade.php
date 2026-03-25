@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary">Scheda Servizio</h6>
      <div>
        <a href="{{ route('services.edit', $service->id) }}" class="btn btn-warning btn-sm">
          <i class="bi bi-pencil"></i> Modifica
        </a>
        <a href="{{ route('services.index') }}" class="btn btn-secondary btn-sm">
          <i class="bi bi-arrow-left"></i> Indietro
        </a>
      </div>
    </div>
    <div class="card-body">

      <div class="row mb-3">
        <div class="col-md-6">
          <strong>Nome Servizio</strong>
          <p>{{ $service->nome_servizio }}</p>
        </div>
        <div class="col-md-6">
          <strong>Prezzo Servizio</strong>
          <p>
            @if($service->prezzo_servizio !== null)
              € {{ number_format($service->prezzo_servizio, 2, ',', '.') }}
            @else
              <span class="text-muted">Non definito</span>
            @endif
          </p>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

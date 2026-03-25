@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary">Scheda Discarica</h6>
      <div>
        <a href="{{ route('deposits.edit', $deposit->id) }}" class="btn btn-warning btn-sm">
          <i class="bi bi-pencil"></i> Modifica
        </a>
        <a href="{{ route('deposits.index') }}" class="btn btn-secondary btn-sm">
          <i class="bi bi-arrow-left"></i> Indietro
        </a>
      </div>
    </div>
    <div class="card-body">

      <div class="row mb-3">
        <div class="col-md-6">
          <strong>Nome Discarica</strong>
          <p>{{ $deposit->name }}</p>
        </div>
        <div class="col-md-6">
          <strong>Indirizzo</strong>
          <p>
            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($deposit->address) }}" target="_blank">
              {{ $deposit->address }}
            </a>
          </p>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-3">
          <strong>N. Aut. Comunicazione</strong>
          <p>{{ $deposit->n_aut_comunicazione ?? 'N/D' }}</p>
        </div>
        <div class="col-md-3">
          <strong>Numero iscrizione albo</strong>
          <p>{{ $deposit->numero_iscrizione_albo ?? 'N/D' }}</p>
        </div>
        <div class="col-md-3">
          <strong>Tipo</strong>
          <p>{{ $deposit->tipo ?? 'N/D' }}</p>
        </div>
        <div class="col-md-3">
          <strong>Destinazione</strong>
          <p>{{ $deposit->destinazione ?? 'N/D' }}</p>
        </div>
      </div>

      @if($deposit->latitude && $deposit->longitude)
      <div class="row mb-3">
        <div class="col-md-3">
          <strong>Latitudine</strong>
          <p>{{ $deposit->latitude }}</p>
        </div>
        <div class="col-md-3">
          <strong>Longitudine</strong>
          <p>{{ $deposit->longitude }}</p>
        </div>
      </div>
      @endif

      <div class="row mb-3">
        <div class="col-12">
          <strong>Materiali Accettati</strong>
          <div class="mt-2">
            @if($deposit->materials->count())
              @foreach($deposit->materials as $material)
                <span class="badge bg-primary me-1">{{ $material->name }}@if($material->eer_code) &nbsp;|&nbsp; {{ $material->eer_code }}@endif</span>
              @endforeach
            @else
              <span class="text-muted">Nessun materiale associato</span>
            @endif
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

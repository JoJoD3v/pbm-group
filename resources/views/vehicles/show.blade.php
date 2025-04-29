@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Dettagli Automezzo</h6>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-8">
          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title">
                {{ $vehicle->nome }}
                <hr>
              </h5>
              
              <div class="row mb-3">
                <div class="col-md-6">
                  <strong>ID:</strong>
                  <p>{{ $vehicle->id }}</p>
                </div>
                <div class="col-md-6">
                  <strong>Nome:</strong>
                  <p>{{ $vehicle->nome }}</p>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <strong>Targa:</strong>
                  <p>{{ $vehicle->targa }}</p>
                </div>
                <div class="col-md-6">
                  <strong>Scadenza Assicurazione:</strong>
                  <p>
                    @if($vehicle->scadenza_assicurazione)
                      {{ \Carbon\Carbon::parse($vehicle->scadenza_assicurazione)->format('d/m/Y') }}
                    @else
                      N/D
                    @endif
                  </p>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <strong>Data Creazione:</strong>
                  <p>{{ \Carbon\Carbon::parse($vehicle->created_at)->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6">
                  <strong>Data Aggiornamento:</strong>
                  <p>{{ \Carbon\Carbon::parse($vehicle->updated_at)->format('d/m/Y H:i') }}</p>
                </div>
              </div>

              <div class="mt-3">
                <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn btn-warning">
                  <i class="fas fa-edit"></i> Modifica
                </a>
                <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">
                  <i class="fas fa-arrow-left"></i> Indietro
                </a>
                <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Sei sicuro di voler eliminare questo automezzo?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Elimina
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

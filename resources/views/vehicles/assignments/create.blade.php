@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Nuova Assegnazione Automezzo</h6>
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

      <form action="{{ route('vehicle.assignments.store') }}" method="POST">
        @csrf

        <!-- Selezione Automezzo -->
        <div class="mb-3">
          <label for="vehicle_id" class="form-label">Automezzo</label>
          <select name="vehicle_id" id="vehicle_id" class="form-select" required>
            <option value="">Seleziona Automezzo</option>
            @foreach($vehicles as $vehicle)
              <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                {{ $vehicle->nome }} ({{ $vehicle->targa }})
              </option>
            @endforeach
          </select>
        </div>

        <!-- Selezione Lavoratore -->
        <div class="mb-3">
          <label for="worker_id" class="form-label">Lavoratore</label>
          <select name="worker_id" id="worker_id" class="form-select" required>
            <option value="">Seleziona Lavoratore</option>
            @foreach($workers as $worker)
              <option value="{{ $worker->id }}" {{ old('worker_id') == $worker->id ? 'selected' : '' }}>
                {{ $worker->name_worker }} {{ $worker->cognome_worker }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Data e Ora Assegnazione -->
        <div class="row">
          <div class="col-md-4">
            <div class="mb-3">
              <label for="data_assegnazione" class="form-label">Data Assegnazione</label>
              <input type="date" name="data_assegnazione" id="data_assegnazione" class="form-control" 
                     value="{{ old('data_assegnazione') ?? date('Y-m-d') }}" required>
            </div>
          </div>
          <div class="col-md-4">
            <div class="mb-3">
              <label for="ora_assegnazione" class="form-label">Ora Assegnazione</label>
              <input type="time" name="ora_assegnazione" id="ora_assegnazione" class="form-control" 
                     value="{{ old('ora_assegnazione') ?? date('H:i') }}" required>
            </div>
          </div>
        </div>

        <!-- Note -->
        <div class="mb-3">
          <label for="note" class="form-label">Note</label>
          <textarea name="note" id="note" class="form-control" rows="3">{{ old('note') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Salva Assegnazione</button>
        <a href="{{ route('vehicle.assignments.index') }}" class="btn btn-secondary">Indietro</a>
      </form>
    </div>
  </div>
</div>
@endsection

@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Modifica Assegnazione Automezzo</h6>
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

      <form action="{{ route('vehicle.assignments.update', ['vehicle' => $vehicle->id, 'worker' => $worker->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Selezione Automezzo -->
        <div class="mb-3">
          <label for="vehicle_id" class="form-label">Automezzo</label>
          <select name="vehicle_id" id="vehicle_id" class="form-select" required>
            @foreach($vehicles as $v)
              <option value="{{ $v->id }}" {{ $v->id == $vehicle->id ? 'selected' : '' }}>
                {{ $v->nome }} ({{ $v->targa }})
              </option>
            @endforeach
          </select>
        </div>

        <!-- Selezione Lavoratore -->
        <div class="mb-3">
          <label for="worker_id" class="form-label">Lavoratore</label>
          <select name="worker_id" id="worker_id" class="form-select" required>
            @foreach($workers as $w)
              <option value="{{ $w->id }}" {{ $w->id == $worker->id ? 'selected' : '' }}>
                {{ $w->name_worker }} {{ $w->cognome_worker }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Data Assegnazione -->
        <div class="mb-3">
          <label for="data_assegnazione" class="form-label">Data Assegnazione</label>
          <input type="date" name="data_assegnazione" id="data_assegnazione" class="form-control" 
                 value="{{ old('data_assegnazione', $assignment->data_assegnazione ? \Carbon\Carbon::parse($assignment->data_assegnazione)->format('Y-m-d') : '') }}">
        </div>

        <!-- Note -->
        <div class="mb-3">
          <label for="note" class="form-label">Note</label>
          <textarea name="note" id="note" class="form-control" rows="3">{{ old('note', $assignment->note) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Aggiorna Assegnazione</button>
        <a href="{{ route('vehicle.assignments.index') }}" class="btn btn-secondary">Indietro</a>
      </form>
    </div>
  </div>
</div>
@endsection

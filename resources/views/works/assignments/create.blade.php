@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Assegna Lavori</h6>
    </div>
    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif
      
      @if(session('error'))
        <div class="alert alert-danger">
          {{ session('error') }}
        </div>
      @endif

      <!-- Form di assegnazione -->
      <div class="card">
        <div class="card-header">
          <h6 class="m-0 font-weight-bold text-primary">Nuova Assegnazione</h6>
        </div>
        <div class="card-body">
          <form action="{{ route('work.assignments.store') }}" method="POST">
            @csrf
            
            <div class="row">
              <div class="col-md-5">
                <div class="mb-3">
                  <label for="work_id" class="form-label">Seleziona Lavoro</label>
                  <select name="work_id" id="work_id" class="form-control" required>
                    <option value="">-- Seleziona un lavoro --</option>
                    @foreach($works as $work)
                      <option value="{{ $work->id }}">{{ $work->tipo_lavoro }} ({{ $work->customer->full_name ?? $work->customer->ragione_sociale ?? 'N/D' }})</option>
                    @endforeach
                  </select>
                </div>
              </div>
              
              <div class="col-md-5">
                <div class="mb-3">
                  <label for="worker_id" class="form-label">Seleziona Lavoratore</label>
                  <select name="worker_id" id="worker_id" class="form-control" required>
                    <option value="">-- Seleziona un lavoratore --</option>
                    @foreach($workers as $worker)
                      <option value="{{ $worker->id }}">{{ $worker->full_name }} ({{ $worker->id_worker }})</option>
                    @endforeach
                  </select>
                </div>
              </div>
              
              <div class="col-md-2 d-flex align-items-end">
                <div class="mb-3">
                  <button type="submit" class="btn btn-primary">Assegna</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

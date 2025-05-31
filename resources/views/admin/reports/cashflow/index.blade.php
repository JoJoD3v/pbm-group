@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Report Fondo Cassa</h1>
    </div>
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Seleziona Parametri Report</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.cashflow.generate') }}" method="POST">
                @csrf
                  <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="worker_id" class="form-label">Dipendente*</label>
                        <select class="form-control" id="worker_id" name="worker_id" required>
                            <option value="">Seleziona dipendente...</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}" {{ old('worker_id') == $worker->id ? 'selected' : '' }}>
                                    {{ $worker->name_worker }} {{ $worker->cognome_worker }} ({{ $worker->worker_email }})
                                </option>
                            @endforeach
                        </select>
                        @error('worker_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="data_inizio" class="form-label">Data Inizio*</label>
                        <input type="date" class="form-control" id="data_inizio" name="data_inizio" value="{{ old('data_inizio', now()->startOfMonth()->toDateString()) }}" required>
                        @error('data_inizio')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="data_fine" class="form-label">Data Fine*</label>
                        <input type="date" class="form-control" id="data_fine" name="data_fine" value="{{ old('data_fine', now()->toDateString()) }}" required>
                        @error('data_fine')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Genera Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 
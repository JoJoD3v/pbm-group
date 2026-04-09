@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Report Lavori</h1>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Seleziona Periodo</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.lavori.generate') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="data_inizio" class="form-label">Data Inizio *</label>
                        <input type="date"
                               class="form-control @error('data_inizio') is-invalid @enderror"
                               id="data_inizio" name="data_inizio"
                               value="{{ old('data_inizio', now()->startOfMonth()->toDateString()) }}"
                               required>
                        @error('data_inizio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="data_fine" class="form-label">Data Fine *</label>
                        <input type="date"
                               class="form-control @error('data_fine') is-invalid @enderror"
                               id="data_fine" name="data_fine"
                               value="{{ old('data_fine', now()->toDateString()) }}"
                               required>
                        @error('data_fine')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Genera Report
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

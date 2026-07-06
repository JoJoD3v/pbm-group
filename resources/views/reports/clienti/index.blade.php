@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Report Clienti</h1>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Seleziona Cliente</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.clienti.generate') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="customer_id" class="form-label">Cliente *</label>
                    <select name="customer_id" id="customer_id"
                            class="form-select @error('customer_id') is-invalid @enderror"
                            required>
                        <option value="">Seleziona Cliente</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                                {{ $customer->customer_type == 'fisica' ? $customer->full_name : $customer->ragione_sociale }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="data_inizio" class="form-label">Data Inizio</label>
                        <input type="date"
                               class="form-control @error('data_inizio') is-invalid @enderror"
                               id="data_inizio" name="data_inizio"
                               value="{{ old('data_inizio') }}">
                        @error('data_inizio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="data_fine" class="form-label">Data Fine</label>
                        <input type="date"
                               class="form-control @error('data_fine') is-invalid @enderror"
                               id="data_fine" name="data_fine"
                               value="{{ old('data_fine') }}">
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

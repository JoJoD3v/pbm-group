@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nuova Chiusura del giorno</h1>
        <a href="{{ route('chiusure.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Indietro
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('chiusure.store') }}">
                @csrf
                <div class="mb-3" style="max-width: 320px;">
                    <label for="data_chiusura" class="form-label">Data chiusura</label>
                    <input type="date" name="data_chiusura" id="data_chiusura"
                           class="form-control @error('data_chiusura') is-invalid @enderror"
                           value="{{ old('data_chiusura', date('Y-m-d')) }}" required>
                    @error('data_chiusura')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-journal-check"></i> Genera Chiusura
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

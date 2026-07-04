@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nuovo Pezzo Borderò</h1>
        <a href="{{ route('admin.pezzi-bordero.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Torna all'elenco
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.pezzi-bordero.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nome_pezzo" class="form-label">Nome Pezzo *</label>
                    <input type="text" name="nome_pezzo" id="nome_pezzo"
                           class="form-control @error('nome_pezzo') is-invalid @enderror"
                           value="{{ old('nome_pezzo') }}" required>
                    @error('nome_pezzo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Salva
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nuovo Incasso</h1>
        <a href="{{ route('worker.cashflow') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Torna all'elenco
        </a>
    </div>
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">Inserisci Dettagli Incasso</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('worker.cashflow.incasso.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="importo" class="form-label">Importo*</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" class="form-control" id="importo" name="importo" step="0.01" min="0.01" value="{{ old('importo') }}" required>
                        </div>
                        @error('importo')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="metodo_pagamento" class="form-label">Metodo di Pagamento*</label>
                        <select class="form-control" id="metodo_pagamento" name="metodo_pagamento" required>
                            <option value="">Seleziona metodo...</option>
                            <option value="contanti" {{ old('metodo_pagamento') == 'contanti' ? 'selected' : '' }}>Contanti</option>
                            <option value="dkv" {{ old('metodo_pagamento') == 'dkv' ? 'selected' : '' }}>DKV</option>
                            <option value="carta" {{ old('metodo_pagamento') == 'carta' ? 'selected' : '' }}>Carta di credito/debito</option>
                        </select>
                        @error('metodo_pagamento')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="work_id" class="form-label">Lavoro Associato*</label>
                        <select class="form-control" id="work_id" name="work_id" required>
                            <option value="">Seleziona lavoro...</option>
                            @foreach($lavoriAssegnati as $lavoro)
                                <option value="{{ $lavoro->id }}" {{ old('work_id') == $lavoro->id ? 'selected' : '' }}>
                                    Lavoro #{{ $lavoro->id }} - {{ $lavoro->tipo_lavoro }} - 
                                    {{ $lavoro->customer->ragione_sociale ?? $lavoro->customer->cognome_cliente . ' ' . $lavoro->customer->nome_cliente }}
                                </option>
                            @endforeach
                        </select>
                        @error('work_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Registra Incasso
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 
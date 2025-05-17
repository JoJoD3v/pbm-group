@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ricarica Fondo Cassa</h1>
    </div>
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Aggiungi Contanti al Fondo Cassa</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('worker.cash.recharge.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="worker_id" class="form-label">Dipendente*</label>
                        <select class="form-control" id="worker_id" name="worker_id" required>
                            <option value="">Seleziona dipendente...</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}" {{ old('worker_id') == $worker->id ? 'selected' : '' }} data-saldo="{{ $worker->fondo_cassa }}">
                                    {{ $worker->name_worker }} {{ $worker->cognome_worker }} - Fondo attuale: € {{ number_format($worker->fondo_cassa, 2, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('worker_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    
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
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="motivo" class="form-label">Motivazione*</label>
                        <input type="text" class="form-control" id="motivo" name="motivo" value="{{ old('motivo') }}" required>
                        @error('motivo')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3" id="saldoPreview" style="display: none;">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong>Fondo attuale:</strong> <span id="currentSaldo">€ 0,00</span><br>
                            <strong>Ricarica:</strong> <span id="addAmount">€ 0,00</span><br>
                            <strong>Nuovo fondo dopo ricarica:</strong> <span id="newSaldo">€ 0,00</span>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Esegui Ricarica
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const workerSelect = document.getElementById('worker_id');
        const importoInput = document.getElementById('importo');
        const saldoPreview = document.getElementById('saldoPreview');
        const currentSaldo = document.getElementById('currentSaldo');
        const addAmount = document.getElementById('addAmount');
        const newSaldo = document.getElementById('newSaldo');
        
        function updatePreview() {
            const selectedOption = workerSelect.options[workerSelect.selectedIndex];
            if (selectedOption.value) {
                const saldo = parseFloat(selectedOption.dataset.saldo) || 0;
                const amount = parseFloat(importoInput.value) || 0;
                
                // Aggiorna i valori nel preview
                currentSaldo.textContent = `€ ${saldo.toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                addAmount.textContent = `€ ${amount.toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                newSaldo.textContent = `€ ${(saldo + amount).toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                
                saldoPreview.style.display = 'block';
            } else {
                saldoPreview.style.display = 'none';
            }
        }
        
        // Ascolta i cambiamenti nei campi
        workerSelect.addEventListener('change', updatePreview);
        importoInput.addEventListener('input', updatePreview);
        
        // Inizializza la preview
        updatePreview();
    });
</script>
@endpush
@endsection 
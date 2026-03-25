@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Modifica Fondo Cassa</h1>
        <a href="{{ route('admin.fondo-cassa.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Torna alla lista
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="alert alert-info mb-4">
        <strong>Dipendente:</strong> {{ $worker->name_worker }} {{ $worker->cognome_worker }}
        ({{ $worker->worker_email }})<br>
        <strong>Fondo Cassa Attuale:</strong>
        <span class="font-weight-bold {{ $worker->fondo_cassa < 0 ? 'text-danger' : 'text-success' }}">
            € {{ number_format($worker->fondo_cassa, 2, ',', '.') }}
        </span>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Imposta Nuovo Valore Fondo Cassa</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.fondo-cassa.update', $worker) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nuovo_valore" class="form-label">Nuovo Valore Fondo Cassa (€)*</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number"
                                   class="form-control @error('nuovo_valore') is-invalid @enderror"
                                   id="nuovo_valore"
                                   name="nuovo_valore"
                                   step="0.01"
                                   value="{{ old('nuovo_valore', number_format($worker->fondo_cassa, 2, '.', '')) }}"
                                   required>
                        </div>
                        @error('nuovo_valore')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Inserisci il nuovo saldo totale del fondo cassa. La differenza rispetto al valore attuale
                            verrà registrata automaticamente come movimento.
                        </small>
                    </div>

                    <div class="col-md-6">
                        <label for="motivo" class="form-label">Nota / Motivazione (opzionale)</label>
                        <input type="text"
                               class="form-control @error('motivo') is-invalid @enderror"
                               id="motivo"
                               name="motivo"
                               maxlength="255"
                               value="{{ old('motivo') }}"
                               placeholder="Es. Correzione saldo, rettifica contabile…">
                        @error('motivo')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Anteprima differenza -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="alert alert-secondary" id="previewBox">
                            <strong>Fondo attuale:</strong>
                            € {{ number_format($worker->fondo_cassa, 2, ',', '.') }}<br>
                            <strong>Nuovo fondo:</strong> <span id="previewNuovo">—</span><br>
                            <strong>Differenza (movimento registrato):</strong>
                            <span id="previewDiff" class="font-weight-bold">—</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Salva Modifica
                        </button>
                        <a href="{{ route('admin.fondo-cassa.index') }}" class="btn btn-outline-secondary ms-2">
                            Annulla
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const currentValue = {{ (float) $worker->fondo_cassa }};
    const nuovoInput   = document.getElementById('nuovo_valore');
    const previewNuovo = document.getElementById('previewNuovo');
    const previewDiff  = document.getElementById('previewDiff');

    function formatEur(val) {
        return '€ ' + val.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function updatePreview() {
        const nuovo = parseFloat(nuovoInput.value);
        if (isNaN(nuovo)) {
            previewNuovo.textContent = '—';
            previewDiff.textContent  = '—';
            previewDiff.className    = 'font-weight-bold';
            return;
        }
        const diff = nuovo - currentValue;
        previewNuovo.textContent = formatEur(nuovo);
        if (diff > 0) {
            previewDiff.textContent   = '+' + formatEur(diff) + ' (entrata)';
            previewDiff.className     = 'font-weight-bold text-success';
        } else if (diff < 0) {
            previewDiff.textContent   = formatEur(diff) + ' (uscita)';
            previewDiff.className     = 'font-weight-bold text-danger';
        } else {
            previewDiff.textContent   = '€ 0,00 (nessuna modifica)';
            previewDiff.className     = 'font-weight-bold text-muted';
        }
    }

    nuovoInput.addEventListener('input', updatePreview);
    updatePreview();
});
</script>
@endpush
@endsection

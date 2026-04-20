@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Modifica Ricevuta {{ $ricevuta->numero_ricevuta }}</h1>
        <a href="{{ route('works.show', $ricevuta->work_id) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Torna alla Scheda Lavoro
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Riepilogo Lavoro -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riepilogo Lavoro</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Cliente:</strong> {{ $ricevuta->work->customer->ragione_sociale ?? $ricevuta->work->customer->full_name }}</p>
                    <p><strong>Tipo Lavoro:</strong> {{ $ricevuta->work->tipo_lavoro }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Data Lavoro:</strong> {{ $ricevuta->work->data_esecuzione ? \Carbon\Carbon::parse($ricevuta->work->data_esecuzione)->format('d/m/Y H:i') : 'Non specificata' }}</p>
                    <p><strong>Costo Lavoro:</strong> € {{ number_format($ricevuta->work->costo_lavoro, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Ricevuta -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Dati Ricevuta</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.ricevute.update', $ricevuta->id) }}" method="POST" enctype="multipart/form-data" id="ricevutaForm">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="numero_ricevuta" class="form-label">Numero Ricevuta</label>
                        <input type="text" class="form-control" id="numero_ricevuta" name="numero_ricevuta"
                               value="{{ old('numero_ricevuta', $ricevuta->numero_ricevuta) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Fattura</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="fattura" id="fatturaSi" value="1"
                                   {{ old('fattura', $ricevuta->fattura) == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="fatturaSi">Sì</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="fattura" id="fatturaNo" value="0"
                                   {{ old('fattura', $ricevuta->fattura) == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="fatturaNo">No</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Riserva di Controlli</label>
                        <input type="hidden" name="riserva_controlli" value="0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="riserva_controlli"
                                   id="riserva_controlli" value="1"
                                   {{ old('riserva_controlli', $ricevuta->riserva_controlli) ? 'checked' : '' }}>
                            <label class="form-check-label" for="riserva_controlli">Sì</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nome_ricevente" class="form-label">Nome e Cognome Soggetto Ricevente</label>
                        <input type="text" class="form-control" id="nome_ricevente" name="nome_ricevente"
                               value="{{ old('nome_ricevente', $ricevuta->nome_ricevente) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Firma</label>
                        @if($ricevuta->firma_base64)
                        <div class="mb-3">
                            <p class="text-muted mb-1">Firma attuale:</p>
                            <img src="{{ $ricevuta->firma_base64 }}" alt="Firma" class="border rounded"
                                 style="max-width: 400px; max-height: 150px; background: #fff;">
                        </div>
                        @endif
                        <div class="card p-3 border-primary">
                            <p class="text-muted small mb-2">
                                {{ $ricevuta->firma_base64 ? 'Disegna una nuova firma per sostituirla, oppure lascia vuoto per mantenerla.' : 'Disegna la firma del ricevente.' }}
                            </p>
                            <canvas id="signature-pad" style="touch-action: none; user-select: none; width: 100%; height: 200px;"></canvas>
                            <div class="mt-2">
                                <button type="button" id="clear-signature" class="btn btn-sm btn-secondary">
                                    Cancella firma
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Lascia vuoto per mantenere la firma attuale.</small>
                        <input type="hidden" name="firma_base64" id="firma_base64">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Pagamento Effettuato</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pagamento_effettuato"
                                   id="pagamentoSi" value="1"
                                   {{ old('pagamento_effettuato', $ricevuta->pagamento_effettuato) == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="pagamentoSi">Sì</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="pagamento_effettuato"
                                   id="pagamentoNo" value="0"
                                   {{ old('pagamento_effettuato', $ricevuta->pagamento_effettuato) != '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="pagamentoNo">No</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="somma_pagamento" class="form-label">Somma Pagamento</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" class="form-control" id="somma_pagamento"
                                   name="somma_pagamento"
                                   value="{{ old('somma_pagamento', $ricevuta->somma_pagamento) }}"
                                   {{ old('pagamento_effettuato', $ricevuta->pagamento_effettuato) == '1' ? '' : 'disabled' }}>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <label for="foto_bolla" class="form-label">Foto Bolla</label>
                        @if($ricevuta->foto_bolla)
                        <div class="mb-2">
                            <p class="text-muted mb-1">Bolla attuale:</p>
                            <a href="{{ route('ricevute.bolle.view', $ricevuta->id) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye"></i> Visualizza Bolla Attuale
                            </a>
                        </div>
                        @endif
                        <input type="file" class="form-control" id="foto_bolla" name="foto_bolla"
                               accept="image/*">
                        <small class="form-text text-muted">
                            {{ $ricevuta->foto_bolla ? 'Carica una nuova immagine per sostituire quella attuale.' : 'Formati accettati: JPEG, PNG, GIF. Max 5MB.' }}
                        </small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="submit-btn">
                    <i class="bi bi-save"></i> Aggiorna Ricevuta
                </button>
                <a href="{{ route('works.show', $ricevuta->work_id) }}" class="btn btn-secondary">Annulla</a>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas);

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        signaturePad.clear();
    }

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    document.getElementById('clear-signature').addEventListener('click', function () {
        signaturePad.clear();
    });

    document.getElementById('ricevutaForm').addEventListener('submit', function () {
        if (!signaturePad.isEmpty()) {
            document.getElementById('firma_base64').value = signaturePad.toDataURL();
        }
    });

    document.getElementById('pagamentoSi').addEventListener('change', function () {
        document.getElementById('somma_pagamento').disabled = false;
    });

    document.getElementById('pagamentoNo').addEventListener('change', function () {
        document.getElementById('somma_pagamento').disabled = true;
    });
});
</script>
@endsection

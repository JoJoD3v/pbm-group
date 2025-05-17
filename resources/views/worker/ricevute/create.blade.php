@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Crea Ricevuta per Lavoro #{{ $work->id }}</h1>
    
    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riepilogo Lavoro</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Cliente:</strong> {{ $work->customer->ragione_sociale ?? $work->customer->full_name }}</p>
                    <p><strong>Indirizzo Cliente:</strong> {{ $work->customer->address }}</p>
                    <p><strong>Tipo Lavoro:</strong> {{ $work->tipo_lavoro }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Data Lavoro:</strong> {{ $work->data_esecuzione ? date('d/m/Y', strtotime($work->data_esecuzione)) : 'Non specificata' }}</p>
                    <p><strong>Materiale:</strong> {{ $work->materiale }}</p>
                    <p><strong>Costo Lavoro:</strong> € {{ number_format($work->costo_lavoro, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Dati Ricevuta</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('worker.ricevute.store') }}" method="POST" enctype="multipart/form-data" id="ricevutaForm">
                @csrf
                <input type="hidden" name="work_id" value="{{ $work->id }}">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="numero_ricevuta">Numero Ricevuta</label>
                            <input type="text" class="form-control" id="numero_ricevuta" name="numero_ricevuta" value="{{ $numeroRicevuta }}" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fattura</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="fattura" id="fatturaSi" value="1">
                                <label class="form-check-label" for="fatturaSi">Sì</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="fattura" id="fatturaNo" value="0" checked>
                                <label class="form-check-label" for="fatturaNo">No</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="riserva_controlli">Riserva di Controlli</label>
                            <input type="hidden" name="riserva_controlli" value="0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="riserva_controlli" id="riserva_controlli" value="1">
                                <label class="form-check-label" for="riserva_controlli">Sì</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome_ricevente">Nome e Cognome Soggetto Ricevente</label>
                            <input type="text" class="form-control" id="nome_ricevente" name="nome_ricevente" required value="{{ old('nome_ricevente') }}">
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="signature-pad">Firma</label>
                            <div class="card p-3 border-primary">
                                <canvas id="signature-pad" width="664" height="292" style="touch-action: none; user-select: none;"></canvas>
                                <div class="mt-2">
                                    <button type="button" id="clear-signature" class="btn btn-sm btn-secondary">Cancella firma</button>
                                </div>
                            </div>
                            <input type="hidden" name="firma_base64" id="firma_base64">
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pagamento Effettuato</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pagamento_effettuato" id="pagamentoSi" value="1" {{ old('pagamento_effettuato') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="pagamentoSi">Sì</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pagamento_effettuato" id="pagamentoNo" value="0" {{ old('pagamento_effettuato') == '1' ? '' : 'checked' }}>
                                <label class="form-check-label" for="pagamentoNo">No</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="somma_pagamento">Somma Pagamento</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">€</span>
                                </div>
                                <input type="number" step="0.01" class="form-control" id="somma_pagamento" name="somma_pagamento" value="{{ old('somma_pagamento', $work->costo_lavoro) }}" {{ old('pagamento_effettuato') == '1' ? '' : 'disabled' }}>
                            </div>
                            <small class="form-text text-muted">Somma dovuta: € {{ number_format($work->costo_lavoro, 2, ',', '.') }}</small>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="foto_bolla">Foto Bolla</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="foto_bolla" name="foto_bolla" accept="image/*" capture="camera">
                                <label class="custom-file-label" for="foto_bolla">Scegli file o scatta foto</label>
                            </div>
                            <small class="form-text text-muted">Puoi scattare una foto con la fotocamera del dispositivo</small>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="submit-btn">Salva Ricevuta</button>
                <a href="{{ route('worker.jobs') }}" class="btn btn-secondary">Annulla</a>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
// Variabili globali per accedere a SignaturePad
let canvas;
let signaturePad;

document.addEventListener('DOMContentLoaded', function() {
    // Inizializza SignaturePad
    canvas = document.getElementById('signature-pad');
    signaturePad = new SignaturePad(canvas);
    
    // Risolve il problema di disallineamento ricalcolando le dimensioni del canvas
    resizeCanvas();
    
    // Aggiungi un event listener per ricalcolare in caso di ridimensionamento della finestra
    window.addEventListener('resize', resizeCanvas);
    
    // Gestione del pulsante per cancellare la firma
    document.getElementById('clear-signature').addEventListener('click', function() {
        signaturePad.clear();
    });
    
    // Prima dell'invio del form, salva la firma come base64 nel campo hidden
    document.getElementById('ricevutaForm').addEventListener('submit', function(e) {
        if (signaturePad.isEmpty()) {
            e.preventDefault();
            alert('Per favore inserisci la firma prima di procedere.');
            return false;
        }
        
        // Ottieni l'immagine base64 dalla firma
        const dataURL = signaturePad.toDataURL();
        document.getElementById('firma_base64').value = dataURL;
        
        console.log('Firma salvata:', dataURL.substring(0, 50) + '...');
        return true;
    });
    
    // GESTIONE CAMPO PAGAMENTO
    document.getElementById('pagamentoSi').addEventListener('change', function() {
        document.getElementById('somma_pagamento').disabled = false;
    });
    
    document.getElementById('pagamentoNo').addEventListener('change', function() {
        document.getElementById('somma_pagamento').disabled = true;
    });
    
    // GESTIONE FILE BOLLA
    document.getElementById('foto_bolla').addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            document.querySelector('[for="foto_bolla"]').textContent = this.files[0].name;
        } else {
            document.querySelector('[for="foto_bolla"]').textContent = 'Scegli file o scatta foto';
        }
    });
});

// Funzione per ricalcolare le dimensioni del canvas
function resizeCanvas() {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
    signaturePad.clear(); // Clear the canvas after resize
}
</script>
@endsection 
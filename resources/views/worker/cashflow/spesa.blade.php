@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nuova Spesa</h1>
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
    
    <div class="alert alert-info">
        <strong>Saldo Fondo Cassa disponibile:</strong> € {{ number_format($worker->fondo_cassa, 2, ',', '.') }}
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger">Inserisci Dettagli Spesa</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('worker.cashflow.spesa.store') }}" method="POST" id="spesaForm">
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
                            <option value="contanti" {{ old('metodo_pagamento') == 'contanti' ? 'selected' : '' }}>Contanti (Fondo cassa: € {{ number_format($worker->fondo_cassa, 2, ',', '.') }})</option>
                            <option value="dkv" {{ old('metodo_pagamento') == 'dkv' ? 'selected' : '' }}>DKV</option>
                            
                            @if($carteAssegnate->count() > 0)
                                @if($carteAssegnate->count() == 1)
                                    @php $carta = $carteAssegnate->first(); @endphp
                                    <option value="carta" {{ old('metodo_pagamento') == 'carta' ? 'selected' : '' }} data-card-id="{{ $carta->id }}">
                                        Carta {{ substr($carta->numero_carta, 0, 4) . ' **** **** ' . substr($carta->numero_carta, -4) }} 
                                        (Saldo: € {{ number_format($carta->fondo_carta, 2, ',', '.') }})
                                    </option>
                                @else
                                    <option value="carta" {{ old('metodo_pagamento') == 'carta' ? 'selected' : '' }}>Carta prepagata (Seleziona sotto)</option>
                                @endif
                            @endif
                        </select>
                        @error('metodo_pagamento')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Campo nascosto per l'ID della carta, sarà popolato via JavaScript -->
                <input type="hidden" id="hiddenCardId" name="credit_card_id" value="{{ old('credit_card_id') }}">
                
                <div class="row mb-3" id="cartaContainer" style="display: none;">
                    <div class="col-md-12">
                        <label for="visible_credit_card_id" class="form-label">Seleziona Carta*</label>
                        @if($carteAssegnate->count() > 1)
                            <div class="alert alert-info mb-2">
                                <i class="bi bi-info-circle"></i> Hai multiple carte prepagate assegnate. Seleziona quale carta utilizzare per questa spesa.
                            </div>
                        @endif
                        <select class="form-control" id="visible_credit_card_id">
                            <option value="">Seleziona carta...</option>
                            @foreach($carteAssegnate as $carta)
                                <option value="{{ $carta->id }}" {{ old('credit_card_id') == $carta->id ? 'selected' : '' }} data-saldo="{{ $carta->fondo_carta }}">
                                    Carta #{{ $carta->id }} - 
                                    {{ substr($carta->numero_carta, 0, 4) . ' **** **** ' . substr($carta->numero_carta, -4) }}
                                    (Saldo: € {{ number_format($carta->fondo_carta, 2, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('credit_card_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="motivo" class="form-label">Motivazione*</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3" required>{{ old('motivo') }}</textarea>
                        @error('motivo')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3" id="saldoPreview" style="display: none;">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <div id="saldoContanti" style="display: none;">
                                <strong>Fondo cassa attuale:</strong> € {{ number_format($worker->fondo_cassa, 2, ',', '.') }}<br>
                                <strong>Spesa:</strong> <span id="importoSpesa">€ 0,00</span><br>
                                <strong>Nuovo fondo cassa dopo la spesa:</strong> <span id="nuovoSaldoContanti">€ {{ number_format($worker->fondo_cassa, 2, ',', '.') }}</span>
                            </div>
                            <div id="saldoCarta" style="display: none;">
                                <strong>Saldo carta:</strong> <span id="saldoCartaAttuale">€ 0,00</span><br>
                                <strong>Spesa:</strong> <span id="importoSpesaCarta">€ 0,00</span><br>
                                <strong>Nuovo saldo carta dopo la spesa:</strong> <span id="nuovoSaldoCarta">€ 0,00</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-save"></i> Registra Spesa
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
        const metodoPagamento = document.getElementById('metodo_pagamento');
        const cartaContainer = document.getElementById('cartaContainer');
        const visibleCardSelect = document.getElementById('visible_credit_card_id');
        const hiddenCardIdField = document.getElementById('hiddenCardId');
        const importoInput = document.getElementById('importo');
        const saldoPreview = document.getElementById('saldoPreview');
        const saldoContanti = document.getElementById('saldoContanti');
        const saldoCarta = document.getElementById('saldoCarta');
        const importoSpesa = document.getElementById('importoSpesa');
        const importoSpesaCarta = document.getElementById('importoSpesaCarta');
        const nuovoSaldoContanti = document.getElementById('nuovoSaldoContanti');
        const saldoCartaAttuale = document.getElementById('saldoCartaAttuale');
        const nuovoSaldoCarta = document.getElementById('nuovoSaldoCarta');
        const form = document.getElementById('spesaForm');
        
        // Funzione per formattare gli importi in Euro
        function formatCurrency(amount) {
            return '€ ' + Number(amount).toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
        
        // Gestisce la selezione visibile della carta
        visibleCardSelect.addEventListener('change', function() {
            // Aggiorna il campo nascosto con l'ID carta selezionato
            hiddenCardIdField.value = this.value;
            updateCartaPreview();
        });
        
        // Funzione per mostrare/nascondere il selettore carta
        function toggleCartaSelector() {
            if (metodoPagamento.value === 'carta') {
                // Gestione singola carta vs multiple carte
                if (metodoPagamento.options[metodoPagamento.selectedIndex].hasAttribute('data-card-id')) {
                    // Se è selezionata l'opzione con una singola carta
                    const cardId = metodoPagamento.options[metodoPagamento.selectedIndex].getAttribute('data-card-id');
                    hiddenCardIdField.value = cardId;
                    cartaContainer.style.display = 'none';
                } else {
                    // Se ci sono più carte da selezionare
                    cartaContainer.style.display = 'block';
                }
                
                saldoContanti.style.display = 'none';
                saldoCarta.style.display = 'block';
                updateCartaPreview();
            } else if (metodoPagamento.value === 'contanti') {
                cartaContainer.style.display = 'none';
                hiddenCardIdField.value = '';
                saldoContanti.style.display = 'block';
                saldoCarta.style.display = 'none';
                updateContantiPreview();
            } else {
                cartaContainer.style.display = 'none';
                hiddenCardIdField.value = '';
                saldoPreview.style.display = 'none';
            }
        }
        
        // Funzione per aggiornare l'anteprima del saldo contanti
        function updateContantiPreview() {
            const fondoCassa = {{ $worker->fondo_cassa }};
            const importo = parseFloat(importoInput.value) || 0;
            
            importoSpesa.textContent = formatCurrency(importo);
            nuovoSaldoContanti.textContent = formatCurrency(fondoCassa - importo);
            
            if (importo > 0) {
                saldoPreview.style.display = 'block';
            } else {
                saldoPreview.style.display = 'none';
            }
        }
        
        // Funzione per aggiornare l'anteprima del saldo carta
        function updateCartaPreview() {
            let saldoCarta = 0;
            let cartaSelezionata = false;
            
            if (metodoPagamento.value === 'carta') {
                if (metodoPagamento.options[metodoPagamento.selectedIndex].hasAttribute('data-card-id')) {
                    // Caso carta singola
                    const cartaOption = metodoPagamento.options[metodoPagamento.selectedIndex];
                    const saldoText = cartaOption.textContent.match(/Saldo: € ([\d.,]+)/);
                    if (saldoText && saldoText[1]) {
                        saldoCarta = parseFloat(saldoText[1].replace('.', '').replace(',', '.'));
                        cartaSelezionata = true;
                    }
                } else if (visibleCardSelect.value) {
                    // Caso selezione da multiple carte
                    const selectedOption = visibleCardSelect.options[visibleCardSelect.selectedIndex];
                    saldoCarta = parseFloat(selectedOption.dataset.saldo) || 0;
                    cartaSelezionata = true;
                }
                
                if (cartaSelezionata) {
                    const importo = parseFloat(importoInput.value) || 0;
                    
                    saldoCartaAttuale.textContent = formatCurrency(saldoCarta);
                    importoSpesaCarta.textContent = formatCurrency(importo);
                    nuovoSaldoCarta.textContent = formatCurrency(saldoCarta - importo);
                    
                    if (importo > 0) {
                        saldoPreview.style.display = 'block';
                    } else {
                        saldoPreview.style.display = 'none';
                    }
                } else {
                    saldoPreview.style.display = 'none';
                }
            }
        }
        
        // Verifica i dati prima dell'invio del form
        form.addEventListener('submit', function(e) {
            if (metodoPagamento.value === 'carta' && !hiddenCardIdField.value) {
                e.preventDefault();
                alert('Seleziona una carta per proseguire.');
                return false;
            }
        });
        
        // Inizializza lo stato
        toggleCartaSelector();
        
        // Ascolta i cambiamenti
        metodoPagamento.addEventListener('change', toggleCartaSelector);
        importoInput.addEventListener('input', function() {
            if (metodoPagamento.value === 'contanti') {
                updateContantiPreview();
            } else if (metodoPagamento.value === 'carta') {
                updateCartaPreview();
            }
        });
    });
</script>
@endpush
@endsection 
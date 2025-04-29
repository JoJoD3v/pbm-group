@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifica Ricarica</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('credit-card-recharges.update', $recharge->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="credit_card_id">Carta Prepagata</label>
                            <select class="form-control @error('credit_card_id') is-invalid @enderror" id="credit_card_id" name="credit_card_id" required>
                                <option value="">Seleziona una carta</option>
                                @foreach($creditCards as $card)
                                    <option value="{{ $card->id }}" {{ $recharge->credit_card_id == $card->id ? 'selected' : '' }}>
                                        {{ $card->numero_carta }} - Saldo: € {{ number_format($card->fondo_carta, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('credit_card_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="importo">Importo Ricarica</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">€</span>
                                </div>
                                <input type="number" step="0.01" class="form-control @error('importo') is-invalid @enderror" id="importo" name="importo" value="{{ old('importo', $recharge->importo) }}" required>
                                @error('importo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="data_ricarica">Data e Ora Ricarica</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" class="form-control @error('data_ricarica_data') is-invalid @enderror" id="data_ricarica_data" name="data_ricarica_data" value="{{ old('data_ricarica_data', $dataRicaricaData) }}" required>
                                    @error('data_ricarica_data')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <input type="time" class="form-control @error('data_ricarica_ora') is-invalid @enderror" id="data_ricarica_ora" name="data_ricarica_ora" value="{{ old('data_ricarica_ora', $dataRicaricaOra) }}" required>
                                    @error('data_ricarica_ora')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="note">Note</label>
                            <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3">{{ old('note', $recharge->note) }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Aggiorna</button>
                            <a href="{{ route('credit-card-recharges.index') }}" class="btn btn-secondary">Annulla</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
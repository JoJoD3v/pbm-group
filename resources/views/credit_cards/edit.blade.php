@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifica Carta Prepagata</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('credit-cards.update', $creditCard) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="numero_carta">Numero Carta</label>
                            <input type="text" class="form-control @error('numero_carta') is-invalid @enderror" id="numero_carta" name="numero_carta" value="{{ old('numero_carta', $creditCard->numero_carta) }}" required>
                            @error('numero_carta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>                        <div class="form-group">
                            <label for="scadenza_carta">Data di Scadenza</label>
                            <div class="italian-date-input">
                                <input type="date" class="form-control @error('scadenza_carta') is-invalid @enderror" id="scadenza_carta" name="scadenza_carta" value="{{ old('scadenza_carta', $creditCard->scadenza_carta) }}" required>
                            </div>
                            @error('scadenza_carta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="fondo_carta">Fondo Carta</label>
                            <input type="number" step="0.01" class="form-control @error('fondo_carta') is-invalid @enderror" id="fondo_carta" name="fondo_carta" value="{{ old('fondo_carta', $creditCard->fondo_carta) }}" required>
                            @error('fondo_carta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Aggiorna</button>
                            <a href="{{ route('credit-cards.index') }}" class="btn btn-secondary">Annulla</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
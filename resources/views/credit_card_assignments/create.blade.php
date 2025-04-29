@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Nuova Assegnazione Carta</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('credit-card-assignments.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="credit_card_id">Carta Prepagata</label>
                            <select class="form-control @error('credit_card_id') is-invalid @enderror" id="credit_card_id" name="credit_card_id" required>
                                <option value="">Seleziona una carta</option>
                                @foreach($creditCards as $card)
                                    <option value="{{ $card->id }}">{{ $card->numero_carta }}</option>
                                @endforeach
                            </select>
                            @error('credit_card_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="worker_id">Lavoratore</label>
                            <select class="form-control @error('worker_id') is-invalid @enderror" id="worker_id" name="worker_id" required>
                                <option value="">Seleziona un lavoratore</option>
                                @foreach($workers as $worker)
                                    <option value="{{ $worker->id }}">{{ $worker->name_worker }} {{ $worker->cognome_worker }}</option>
                                @endforeach
                            </select>
                            @error('worker_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="data_assegnazione">Data di Assegnazione</label>
                            <input type="date" class="form-control @error('data_assegnazione') is-invalid @enderror" id="data_assegnazione" name="data_assegnazione" value="{{ old('data_assegnazione') }}" required>
                            @error('data_assegnazione')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Salva</button>
                            <a href="{{ route('credit-card-assignments.index') }}" class="btn btn-secondary">Annulla</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
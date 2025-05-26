@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Le Mie Carte</h1>
        
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
                <h6 class="m-0 font-weight-bold text-primary">Carte Prepagate Assegnate</h6>
            </div>
            <div class="card-body">
                @if($creditCards->count() > 0)                    <div class="table-responsive">
                        <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Numero Carta</th>
                                    <th>Saldo</th>
                                    <th class="date-column">Data Assegnazione</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($creditCards as $card)
                                    <tr>
                                        <td>{{ $card->id }}</td>
                                        <td>{{ substr($card->numero_carta, 0, 4) . ' **** **** ' . substr($card->numero_carta, -4) }}</td>
                                        <td>€ {{ number_format($card->fondo_carta, 2, ',', '.') }}</td>
                                        <td class="date-column">@formatDate($card->data_assegnazione)</td>
                                        <td>
                                            <a href="{{ route('worker.cards.show', $card->id) }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye"></i> Dettagli
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        Non hai carte assegnate al momento.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 
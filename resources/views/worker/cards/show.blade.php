@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dettagli Carta</h1>
            <a href="{{ route('worker.cards') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Torna all'elenco
            </a>
        </div>
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informazioni Carta</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="credit-card-box">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-3">
                                                <div>
                                                    <p class="mb-1">Carta Prepagata</p>
                                                    <h4>{{ substr($creditCardObj->numero_carta, 0, 4) }} **** **** {{ substr($creditCardObj->numero_carta, -4) }}</h4>
                                                </div>
                                                <div>
                                                    <i class="bi bi-credit-card" style="font-size: 3rem;"></i>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <p class="mb-1">Saldo</p>
                                                    <h3>€ {{ number_format($creditCardObj->fondo_carta, 2, ',', '.') }}</h3>
                                                </div>
                                                <div>
                                                    <p class="mb-1">Data Scadenza</p>
                                                    <h5>{{ $creditCardObj->scadenza_carta ?? 'N/A' }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th style="width: 40%">ID Carta:</th>
                                    <td>{{ $creditCardObj->id }}</td>
                                </tr>
                                <tr>
                                    <th>Numero Carta:</th>
                                    <td>{{ substr($creditCardObj->numero_carta, 0, 4) . ' **** **** ' . substr($creditCardObj->numero_carta, -4) }}</td>
                                </tr>
                                <tr>
                                    <th>Data Assegnazione:</th>
                                    <td>{{ \Carbon\Carbon::parse($creditCardObj->pivot->created_at)->format('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Utilizzo Recente</h6>
                    </div>
                    <div class="card-body">
                        @if(isset($creditCardObj->recharges) && count($creditCardObj->recharges) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Importo</th>
                                            <th>Autore</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($creditCardObj->recharges as $ricarica)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($ricarica->created_at)->format('d/m/Y H:i') }}</td>
                                                <td>€ {{ number_format($ricarica->importo, 2, ',', '.') }}</td>
                                                <td>{{ $ricarica->autore_ricarica ?? 'Sistema' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                Nessuna ricarica registrata per questa carta.
                            </div>
                        @endif
                        
                        <div class="text-center mt-3">
                            <p>Per richiedere una ricarica, contatta l'amministratore</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 
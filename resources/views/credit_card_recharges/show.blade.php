@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dettaglio Ricarica</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 200px">ID Ricarica</th>
                                    <td>{{ $recharge->id }}</td>
                                </tr>
                                <tr>
                                    <th>Carta</th>
                                    <td>{{ $recharge->numero_carta }}</td>
                                </tr>
                                <tr>
                                    <th>Importo</th>
                                    <td>€ {{ number_format($recharge->importo, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Data e Ora Ricarica</th>
                                    <td>{{ \Carbon\Carbon::parse($recharge->data_ricarica)->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <td>{{ $recharge->note ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Autore</th>
                                    <td>
                                        @if($recharge->user_id)
                                            {{ $recharge->autore_nome }} {{ $recharge->autore_cognome }}
                                        @else
                                            <span class="text-muted">Non specificato</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Data Creazione</th>
                                    <td>{{ $recharge->created_at }}</td>
                                </tr>
                                <tr>
                                    <th>Ultima Modifica</th>
                                    <td>{{ $recharge->updated_at }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('credit-card-recharges.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Torna alla Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

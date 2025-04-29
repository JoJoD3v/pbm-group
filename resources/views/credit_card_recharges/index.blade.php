@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Report Ricariche Carte</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <a href="{{ route('credit-card-recharges.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuova Ricarica
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Carta</th>
                                    <th>Importo</th>
                                    <th>Data Ricarica</th>
                                    <th>Note</th>
                                    <th>Autore</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recharges as $recharge)
                                <tr>
                                    <td>{{ $recharge->id }}</td>
                                    <td>{{ $recharge->numero_carta }}</td>
                                    <td>€ {{ number_format($recharge->importo, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($recharge->data_ricarica)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $recharge->note ?? '-' }}</td>
                                    <td>
                                        @if($recharge->user_id)
                                            {{ $recharge->autore_nome }} {{ $recharge->autore_cognome }}
                                        @else
                                            <span class="text-muted">Non specificato</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('credit-card-recharges.show', $recharge->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('credit-card-recharges.edit', $recharge->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('credit-card-recharges.destroy', $recharge->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questa ricarica?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

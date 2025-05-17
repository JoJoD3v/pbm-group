@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Fondo Cassa</h1>
        
        <form class="d-flex align-items-center" method="GET" action="{{ route('worker.cashflow') }}">
            <div class="input-group me-2" style="width: 200px;">
                <input type="date" name="data" class="form-control" value="{{ $data }}" required>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-calendar-check"></i>
                </button>
            </div>
        </form>
    </div>
    
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
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Totale Entrate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">€ {{ number_format($totaleEntrate, 2, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-arrow-up-circle-fill fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Totale Uscite</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">€ {{ number_format($totaleUscite, 2, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-arrow-down-circle-fill fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Fondo Cassa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">€ {{ number_format($fondoCassa, 2, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cash-coin fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <a href="{{ route('worker.cashflow.spesa.create') }}" class="btn btn-danger mr-2">
                <i class="bi bi-dash-circle"></i> Nuova Spesa
            </a>
            <a href="{{ route('worker.cashflow.incasso.create') }}" class="btn btn-success ml-2">
                <i class="bi bi-plus-circle"></i> Nuovo Incasso
            </a>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Movimenti del {{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}</h6>
        </div>
        <div class="card-body">
            @if($movimenti->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Ora</th>
                                <th>Tipo</th>
                                <th>Importo</th>
                                <th>Metodo</th>
                                <th>Motivazione</th>
                                <th>Riferimento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movimenti as $movimento)
                                <tr class="{{ $movimento->tipo_movimento == 'entrata' ? 'table-success' : 'table-danger' }}">
                                    <td>{{ $movimento->created_at->format('H:i') }}</td>
                                    <td>
                                        @if($movimento->tipo_movimento == 'entrata')
                                            <span class="badge bg-success">Entrata</span>
                                        @else
                                            <span class="badge bg-danger">Uscita</span>
                                        @endif
                                    </td>
                                    <td>€ {{ number_format($movimento->importo, 2, ',', '.') }}</td>
                                    <td>
                                        @if($movimento->metodo_pagamento == 'contanti')
                                            <i class="bi bi-cash"></i> Contanti
                                        @elseif($movimento->metodo_pagamento == 'dkv')
                                            <i class="bi bi-credit-card"></i> DKV
                                        @elseif($movimento->metodo_pagamento == 'carta')
                                            <i class="bi bi-credit-card-2-front"></i> Carta
                                            @if($movimento->credit_card_id)
                                                #{{ $movimento->credit_card_id }}
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $movimento->motivo }}</td>
                                    <td>
                                        @if($movimento->work_id)
                                            <a href="{{ route('worker.jobs.show', $movimento->work_id) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> Lavoro #{{ $movimento->work_id }}
                                            </a>
                                        @elseif($movimento->credit_card_id)
                                            <a href="{{ route('worker.cards.show', $movimento->credit_card_id) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> Carta #{{ $movimento->credit_card_id }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    Nessun movimento registrato per il giorno {{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 
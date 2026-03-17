@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestione Fondo Cassa</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Saldo Fondo Cassa per Dipendente</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Dipendente</th>
                            <th>Email</th>
                            <th class="text-right">Fondo Cassa Attuale</th>
                            <th class="text-center">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workers as $worker)
                            <tr>
                                <td>{{ $worker->name_worker }} {{ $worker->cognome_worker }}</td>
                                <td>{{ $worker->worker_email }}</td>
                                <td class="text-right font-weight-bold {{ $worker->fondo_cassa < 0 ? 'text-danger' : 'text-success' }}">
                                    € {{ number_format($worker->fondo_cassa, 2, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.fondo-cassa.edit', $worker) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil-square"></i> Modifica
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Nessun dipendente trovato.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

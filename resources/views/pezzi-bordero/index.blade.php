@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestione Pezzi Borderò</h1>
        <a href="{{ route('admin.pezzi-bordero.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nuovo Pezzo
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            @if($pezzi->isEmpty())
                <p class="text-muted mb-0">Nessun pezzo registrato nel catalogo.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%">
                        <thead class="table-dark">
                            <tr>
                                <th>Nome Pezzo</th>
                                <th style="width: 160px;">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pezzi as $pezzo)
                                <tr>
                                    <td>{{ $pezzo->nome_pezzo }}</td>
                                    <td>
                                        <a href="{{ route('admin.pezzi-bordero.edit', $pezzo->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.pezzi-bordero.destroy', $pezzo->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminare questo pezzo dal catalogo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

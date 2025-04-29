@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Assegnazioni Carte Prepagate</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <a href="{{ route('credit-card-assignments.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuova Assegnazione
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Carta</th>
                                    <th>Lavoratore</th>
                                    <th>Data Assegnazione</th>
                                    <th>Data Restituzione</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignments as $assignment)
                                <tr>
                                    <td>{{ $assignment->id }}</td>
                                    <td>{{ $assignment->numero_carta }}</td>
                                    <td>{{ $assignment->name_worker }} {{ $assignment->cognome_worker }}</td>
                                    <td>{{ $assignment->data_assegnazione }}</td>
                                    <td>{{ $assignment->data_restituzione ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('credit-card-assignments.edit', $assignment->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('credit-card-assignments.destroy', $assignment->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questa assegnazione?')">
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
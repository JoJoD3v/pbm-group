@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Carte Prepagate</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <a href="{{ route('credit-cards.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuova Carta
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Numero Carta</th>
                                    <th>Scadenza</th>
                                    <th>Fondo</th>
                                    <th>Assegnato</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($creditCards as $card)
                                <tr>
                                    <td>{{ $card->id }}</td>
                                    <td>{{ $card->numero_carta }}</td>
                                    <td>{{ $card->scadenza_carta }}</td>
                                    <td>€ {{ number_format($card->fondo_carta, 2) }}</td>
                                    <td>
                                        @if($card->assignedWorker->isNotEmpty())
                                            {{ $card->assignedWorker->first()->name_worker }} {{ $card->assignedWorker->first()->cognome_worker }}
                                        @else
                                            Nessuno
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('credit-cards.edit', $card) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('credit-cards.destroy', $card) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questa carta?')">
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
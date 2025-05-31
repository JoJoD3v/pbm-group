@extends('layouts.dashboard')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dettagli Utente: {{ $user->first_name }} {{ $user->last_name }}</h1>
    <div>
        <a href="{{ route('users.edit', $user) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
            <i class="bi bi-pencil fa-sm text-white-50"></i> Modifica
        </a>
        <a href="{{ route('users.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="bi bi-arrow-left fa-sm text-white-50"></i> Torna all'Elenco
        </a>
    </div>
</div>

<!-- User Info Card -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informazioni Utente</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Nome:</strong>
                        <p class="text-gray-900">{{ $user->first_name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Cognome:</strong>
                        <p class="text-gray-900">{{ $user->last_name }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Email:</strong>
                        <p class="text-gray-900">{{ $user->email }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Telefono:</strong>
                        <p class="text-gray-900">{{ $user->phone ?? 'Non specificato' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Ruolo:</strong>
                        <p>
                            <span class="badge badge-{{ $user->role === 'sviluppatore' ? 'danger' : ($user->role === 'amministratore' ? 'warning' : 'info') }} badge-lg">
                                {{ ucfirst($user->role) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Data Registrazione:</strong>
                        <p class="text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                @if($user->updated_at != $user->created_at)
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Ultimo Aggiornamento:</strong>
                        <p class="text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Actions Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Azioni</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-block">
                        <i class="bi bi-pencil"></i> Modifica Utente
                    </a>

                    @if($user->id !== auth()->id())
                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                          onsubmit="return confirm('Sei sicuro di voler eliminare questo utente? Questa azione non può essere annullata.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="bi bi-trash"></i> Elimina Utente
                        </button>
                    </form>
                    @else
                    <div class="alert alert-info" role="alert">
                        <small><i class="bi bi-info-circle"></i> Non puoi eliminare il tuo stesso account.</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Statistiche</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="mb-3">
                        <h4 class="text-primary">{{ $user->id }}</h4>
                        <small class="text-muted">ID Utente</small>
                    </div>

                    <div class="mb-3">
                        <h4 class="text-success">{{ $user->created_at->diffForHumans() }}</h4>
                        <small class="text-muted">Registrato</small>
                    </div>

                    @if($user->email_verified_at)
                    <div class="mb-3">
                        <span class="badge badge-success">Email Verificata</span>
                    </div>
                    @else
                    <div class="mb-3">
                        <span class="badge badge-warning">Email Non Verificata</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
.badge-lg {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
}
</style>
@endsection
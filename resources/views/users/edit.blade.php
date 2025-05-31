@extends('layouts.dashboard')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Modifica Utente: {{ $user->first_name }} {{ $user->last_name }}</h1>
    <a href="{{ route('users.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="bi bi-arrow-left fa-sm text-white-50"></i> Torna all'Elenco
    </a>
</div>

<!-- Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Modifica Dati Utente</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Nome -->
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                           id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Cognome -->
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Cognome <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                           id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <!-- Email -->
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Telefono -->
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Numero di Telefono</label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+39 123 456 7890">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Ruolo -->
            <div class="mb-3">
                <label for="role" class="form-label">Ruolo <span class="text-danger">*</span></label>
                <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                    <option value="">Seleziona un ruolo</option>
                    <option value="sviluppatore" {{ old('role', $user->role) === 'sviluppatore' ? 'selected' : '' }}>Sviluppatore</option>
                    <option value="amministratore" {{ old('role', $user->role) === 'amministratore' ? 'selected' : '' }}>Amministratore</option>
                    <option value="dipendente" {{ old('role', $user->role) === 'dipendente' ? 'selected' : '' }}>Dipendente</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr>

            <!-- Sezione Password -->
            <h5 class="mb-3">Modifica Password (opzionale)</h5>
            <p class="text-muted">Lascia vuoto se non vuoi modificare la password</p>

            <div class="row">
                <!-- Password -->
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Nuova Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" minlength="8">
                    <small class="form-text text-muted">Minimo 8 caratteri (lascia vuoto per non modificare)</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Conferma Password -->
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Conferma Nuova Password</label>
                    <input type="password" class="form-control" id="password_confirmation"
                           name="password_confirmation" minlength="8">
                </div>
            </div>

            <!-- Info Box -->
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Attenzione:</strong> Se modifichi la password, verrà inviata automaticamente una email
                con le nuove credenziali all'utente.
            </div>

            <!-- Pulsanti -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Annulla
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Aggiorna Utente
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Validazione password in tempo reale
    $('#password, #password_confirmation').on('keyup', function() {
        var password = $('#password').val();
        var confirmPassword = $('#password_confirmation').val();

        if (password !== '' && password !== confirmPassword) {
            $('#password_confirmation').addClass('is-invalid');
        } else {
            $('#password_confirmation').removeClass('is-invalid');
        }
    });
});
</script>
@endsection
@extends('layouts.dashboard')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Aggiungi Nuovo Utente</h1>
    <a href="{{ route('users.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="bi bi-arrow-left fa-sm text-white-50"></i> Torna all'Elenco
    </a>
</div>

<!-- Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Dati Utente</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="row">
                <!-- Nome -->
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                           id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Cognome -->
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Cognome <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                           id="last_name" name="last_name" value="{{ old('last_name') }}" required>
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
                           id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Telefono -->
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Numero di Telefono</label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                           id="phone" name="phone" value="{{ old('phone') }}" placeholder="+39 123 456 7890">
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
                    <option value="sviluppatore" {{ old('role') === 'sviluppatore' ? 'selected' : '' }}>Sviluppatore</option>
                    <option value="amministratore" {{ old('role') === 'amministratore' ? 'selected' : '' }}>Amministratore</option>
                    <option value="dipendente" {{ old('role') === 'dipendente' ? 'selected' : '' }}>Dipendente</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <!-- Password -->
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" required minlength="8">
                    <small class="form-text text-muted">Minimo 8 caratteri</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Conferma Password -->
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Conferma Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password_confirmation"
                           name="password_confirmation" required minlength="8">
                </div>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle"></i>
                <strong>Nota:</strong> Dopo la creazione dell'utente, verrà inviata automaticamente una email
                con le credenziali di accesso all'indirizzo specificato.
            </div>

            <!-- Pulsanti -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Annulla
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Crea Utente
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

        if (password !== confirmPassword) {
            $('#password_confirmation').addClass('is-invalid');
        } else {
            $('#password_confirmation').removeClass('is-invalid');
        }
    });
});
</script>
@endsection
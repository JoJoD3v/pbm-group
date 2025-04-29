@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <h2>Registrazione Utente</h2>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-3">
                <label for="first_name" class="form-label">Nome:</label>
                <input type="text" name="first_name" id="first_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Cognome:</label>
                <input type="text" name="last_name" id="last_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Indirizzo Email:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Ruolo:</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="sviluppatore">Sviluppatore</option>
                    <option value="amministratore">Amministratore</option>
                    <option value="dipendente">Dipendente</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Registra</button>
        </form>
    </div>
</div>
@endsection

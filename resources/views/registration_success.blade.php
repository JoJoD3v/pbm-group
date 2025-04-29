@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <h2>Registrazione Completata</h2>
        <p>L'utente {{ $user->first_name }} {{ $user->last_name }} è stato registrato con successo.</p>
        <p>Password generata: <strong>{{ $password }}</strong></p>
        <p>Ricorda di salvare questa password in un luogo sicuro!</p>
    </div>
</div>
@endsection

@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Modifica Lavoratore</h6>
    </div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('workers.update', $worker->id) }}" method="POST">
        @csrf
        @method('PUT')        <div class="mb-3">
          <label class="form-label">ID Lavoratore</label>
          <input type="text" class="form-control" value="{{ $worker->id_worker }}" readonly disabled>
          <div class="form-text">L'ID lavoratore non può essere modificato.</div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label for="name_worker" class="form-label">Nome</label>
              <input type="text" name="name_worker" id="name_worker" class="form-control" value="{{ $worker->name_worker }}" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label for="cognome_worker" class="form-label">Cognome</label>
              <input type="text" name="cognome_worker" id="cognome_worker" class="form-control" value="{{ $worker->cognome_worker }}" required>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label for="license_worker" class="form-label">Licenza</label>
              <input type="text" name="license_worker" id="license_worker" class="form-control" value="{{ $worker->license_worker }}" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label for="phone_worker" class="form-label">Numero di Telefono</label>
              <input type="tel" name="phone_worker" id="phone_worker" class="form-control" value="{{ $worker->phone_worker }}" placeholder="Es. +39 123 456 7890">
            </div>          </div>
        </div>

        <div class="mb-3">
          <label for="worker_email" class="form-label">Email</label>
          <input type="email" name="worker_email" id="worker_email" class="form-control" value="{{ $worker->worker_email }}" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Nuova Password (opzionale)</label>
          <input type="password" name="password" id="password" class="form-control" minlength="8">
          <div class="form-text">Lascia vuoto per mantenere la password attuale. Se inserita, deve essere di almeno 8 caratteri.</div>
        </div>

        <button type="submit" class="btn btn-primary">Aggiorna</button>
        <a href="{{ route('workers.index') }}" class="btn btn-secondary">Indietro</a>
      </form>
    </div>
  </div>
</div>
@endsection

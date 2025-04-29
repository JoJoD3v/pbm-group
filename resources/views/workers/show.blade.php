@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Dettagli Lavoratore</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <tr>
            <th style="width: 200px;">ID</th>
            <td>{{ $worker->id }}</td>
          </tr>
          <tr>
            <th>ID Lavoratore</th>
            <td>{{ $worker->id_worker }}</td>
          </tr>
          <tr>
            <th>Nome</th>
            <td>{{ $worker->name_worker }}</td>
          </tr>
          <tr>
            <th>Cognome</th>
            <td>{{ $worker->cognome_worker }}</td>
          </tr>
          <tr>
            <th>Licenza</th>
            <td>{{ $worker->license_worker }}</td>
          </tr>
          <tr>
            <th>Email</th>
            <td>{{ $worker->worker_email }}</td>
          </tr>
          <tr>
            <th>Data Creazione</th>
            <td>{{ $worker->created_at->format('d/m/Y H:i') }}</td>
          </tr>
          <tr>
            <th>Ultimo Aggiornamento</th>
            <td>{{ $worker->updated_at->format('d/m/Y H:i') }}</td>
          </tr>
        </table>
      </div>
      
      <div class="mt-3">
        <a href="{{ route('workers.edit', $worker->id) }}" class="btn btn-warning">
          <i class="bi bi-pencil"></i> Modifica
        </a>
        <a href="{{ route('workers.index') }}" class="btn btn-secondary">
          <i class="bi bi-arrow-left"></i> Indietro
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

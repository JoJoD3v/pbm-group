@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Elenco Lavoratori</h6>
    </div>
    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif

      <div class="mb-3">
        <a href="{{ route('workers.create') }}" class="btn btn-primary">
          <i class="bi bi-plus"></i> Aggiungi Lavoratore
        </a>
      </div>      <div class="table-responsive">
        <table id="workersTable" class="table table-bordered dataTable" width="100%" cellspacing="0">
          <thead class="thead-light">            <tr>
              <th>ID</th>
              <th>ID Lavoratore</th>
              <th>Nome</th>
              <th>Cognome</th>
              <th>Licenza</th>
              <th>Email</th>
              <th>Telefono</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody>
            @foreach($workers as $worker)            <tr>
              <td>{{ $worker->id }}</td>
              <td>{{ $worker->id_worker }}</td>
              <td>{{ $worker->name_worker }}</td>
              <td>{{ $worker->cognome_worker }}</td>
              <td>{{ $worker->license_worker }}</td>
              <td>{{ $worker->worker_email }}</td>
              <td>{{ $worker->phone_worker ?? 'N/D' }}</td>
              <td>
                <a href="{{ route('workers.show', $worker->id) }}" class="btn btn-info btn-sm">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('workers.edit', $worker->id) }}" class="btn btn-warning btn-sm">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('workers.destroy', $worker->id) }}" method="POST" style="display:inline-block;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Sei sicuro?')">
                    <i class="bi bi-trash"></i>
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
@endsection

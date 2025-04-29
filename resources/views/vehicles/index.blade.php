@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">Gestione Automezzi</h6>
      <a href="{{ route('vehicles.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> Nuovo Automezzo
      </a>
    </div>
    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif

      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nome</th>
              <th>Targa</th>
              <th>Scadenza Assicurazione</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody>
            @foreach($vehicles as $vehicle)
              <tr>
                <td>{{ $vehicle->id }}</td>
                <td>{{ $vehicle->nome }}</td>
                <td>{{ $vehicle->targa }}</td>
                <td>
                  @if($vehicle->scadenza_assicurazione)
                    {{ \Carbon\Carbon::parse($vehicle->scadenza_assicurazione)->format('d/m/Y') }}
                  @else
                    N/D
                  @endif
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{ route('vehicles.show', $vehicle->id) }}" class="btn btn-info btn-sm">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn btn-warning btn-sm">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Sei sicuro di voler eliminare questo automezzo?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('#dataTable').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json'
      }
    });
  });
</script>
@endsection

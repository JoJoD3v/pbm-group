@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Elenco Appaltatori</h6>
    </div>
    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif

      <div class="mb-3">
        <a href="{{ route('appaltatori.create') }}" class="btn btn-primary">
          <i class="bi bi-plus"></i> Aggiungi Appaltatore
        </a>
      </div>
      <div class="table-responsive">
        <table id="appaltatoriTable" class="table table-bordered dataTable" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th>Tipo</th>
              <th>Nome / Ragione Sociale</th>
              <th>Indirizzo</th>
              <th>Telefono</th>
              <th>Email</th>
              <th>Codice Fiscale / Partita Iva</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody>
            @foreach($appaltatori as $appaltatore)
            <tr>
              <td>{{ ucfirst($appaltatore->tipo_soggetto) }}</td>
              <td>
                @if($appaltatore->tipo_soggetto == 'fisica')
                  {{ $appaltatore->full_name }}
                @else
                  {{ $appaltatore->ragione_sociale }}
                @endif
              </td>
              <td>{{ $appaltatore->address }}</td>
              <td>{{ $appaltatore->phone }}</td>
              <td>{{ $appaltatore->email }}</td>
              <td>
                @if($appaltatore->tipo_soggetto == 'fisica')
                  {{ $appaltatore->codice_fiscale }}
                @else
                  {{ $appaltatore->partita_iva }}
                @endif
              </td>
              <td>
                <a href="{{ route('appaltatori.show', $appaltatore->id) }}" class="btn btn-info">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('appaltatori.edit', $appaltatore->id) }}" class="btn btn-warning">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('appaltatori.destroy', $appaltatore->id) }}" method="POST" style="display:inline-block;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger" onclick="return confirm('Sei sicuro?')">
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

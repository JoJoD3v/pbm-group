@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Elenco Lavori</h6>
    </div>
    <div class="card-body">
      @if(session('success'))
         <div class="alert alert-success">
           {{ session('success') }}
         </div>
      @endif

      <div class="mb-3">
         <a href="{{ route('works.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Aggiungi Lavoro
         </a>
      </div>

      <div class="table-responsive">
         <table id="worksTable" class="table table-bordered datatable" width="100%" cellspacing="0">
            <thead class="thead-light">
                <tr>
                    <th>Data</th>
                    <th>Tipo Lavoro</th>
                    <th>Cliente</th>
                    <th>Status</th>
                    <th>Materiali</th>
                    <th>Indirizzo Partenza</th>
                    <th>Indirizzo Destinazione</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
              @foreach($works as $work)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($work->created_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $work->tipo_lavoro }}</td>
                  <td>
                    @if($work->customer)
                      {{ $work->customer->customer_type == 'fisica' ? $work->customer->full_name : $work->customer->ragione_sociale }}
                    @else
                      N/D
                    @endif
                  </td>
                  <td>{{ $work->status_lavoro }}</td>
                  <td>
                    @if($work->materiale)
                      {{ $work->materiale }}
                      @if($work->codice_eer)
                        ({{ $work->codice_eer }})
                      @endif
                    @else
                      N/D
                    @endif
                  </td>
                  <td>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($work->indirizzo_partenza) }}" target="_blank">   
                      {{ $work->indirizzo_partenza }}
                    </a>
                  </td>
                  <td>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($work->indirizzo_destinazione) }}" target="_blank">   
                      {{ $work->indirizzo_destinazione }}
                    </a>
                  </td>
                  <td>
                    <a href="{{ route('works.show', $work->id) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-eye"></i>
                      </a>                    
                    <form action="{{ route('works.destroy', $work->id) }}" method="POST" style="display:inline-block;">
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

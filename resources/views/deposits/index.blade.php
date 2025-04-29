@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Gestione Depositi</h6>
    </div>
    <div class="card-body">
      @if(session('success'))
          <div class="alert alert-success">
              {{ session('success') }}
          </div>
      @endif

      <div class="mb-3">
        <a href="{{ route('deposits.create') }}" class="btn btn-primary">
          <i class="fas fa-plus"></i> Aggiungi Deposito
        </a>
      </div>

      <div class="table-responsive">
        <table id="depositsTable" class="table table-bordered datatable" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th width="20">ID</th>
              <th>Nome Deposito</th>
              <th>Indirizzo</th>
              <th>Materiali Accettati</th>
              <th>EER Materiali</th>
              <th>Distanza (km)</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody>
            @foreach($deposits as $deposit)
            <tr class="deposit" data-lat="{{ $deposit->latitude }}" data-lon="{{ $deposit->longitude }}">
              <td>{{ $deposit->id }}</td>
              <td>{{ $deposit->name }}</td>
              <td>
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($deposit->address) }}" target="_blank">                
                    {{ $deposit->address }}
                </a>    
            </td>
              <td>
                @if($deposit->materials->count())
                    @foreach($deposit->materials as $material)
                      <span class="btn btn-outline-primary btn-sm">{{ $material->name }}</span>
                    @endforeach
                @else
                  Nessun materiale
                @endif
              </td>
              <td>
                @if($deposit->materials->count())
                    @foreach($deposit->materials as $material)
                        <span class="btn btn-outline-danger btn-sm">{{ $material->eer_code ?? 'N/D' }}</span>
                    @endforeach
                @else
                  Nessun codice
                @endif
              </td>              
              <td class="distance">Calcolo...</td>
              <td>
                <a href="{{ route('deposits.edit', $deposit->id) }}" class="btn btn-warning btn-sm mb-1">
                  <i class="bi bi-pencil"></i> 
                </a>
               <form action="{{ route('deposits.destroy', $deposit->id) }}" method="POST" style="display:inline-block;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm" style="margin-top:-4px;" onclick="return confirm('Sei sicuro?')">
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


<!-- Includi il file JS esterno per il calcolo delle distanze -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places"></script>

<script src="{{ asset('js/deposit-distance.js') }}"></script>
@endsection

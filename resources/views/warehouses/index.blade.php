@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Gestione Cantieri</h6>
    </div>
    <div class="card-body">
      @if(session('success'))
          <div class="alert alert-success">
              {{ session('success') }}
          </div>
      @endif

      <div class="mb-3">
        <a href="{{ route('warehouses.create') }}" class="btn btn-primary">
          <i class="fas fa-plus"></i> Aggiungi Cantiere
        </a>
      </div>      <div class="table-responsive">
        <table id="warehousesTable" class="table table-bordered dataTable" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th width="20">ID</th>
              <th>Nome Cantiere</th>
              <th>Indirizzo</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody>
            @foreach($warehouses as $warehouse)
            <tr class="deposit" data-lat="{{ $warehouse->latitude }}" data-lon="{{ $warehouse->longitude }}">
              <td>{{ $warehouse->id }}</td>
              <td>{{ $warehouse->nome_sede }}</td>
              <td>
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($warehouse->indirizzo) }}" target="_blank">                
                    {{ $warehouse->indirizzo }}
                </a>    
            </td>
              <td>
                <a href="{{ route('warehouses.edit', $warehouse->id) }}" class="btn btn-warning btn-sm mb-1">
                  <i class="bi bi-pencil"></i> 
                </a>
               <form action="{{ route('warehouses.destroy', $warehouse->id) }}" method="POST" style="display:inline-block;">
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



@endsection

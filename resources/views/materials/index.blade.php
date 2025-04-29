@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Gestione Materiali</h6>
    </div>
    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif

      <div class="mb-3">
        <a href="{{ route('materials.create') }}" class="btn btn-primary">
          <i class="bi bi-plus"></i> Aggiungi Materiale
        </a>
      </div>

      <div class="table-responsive">
        <table id="materialsTable" class="table table-bordered datatable" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th width="20">ID</th>
              <th>Nome</th>
              <th>EER Code</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody>
            @foreach($materials as $material)
            <tr>
              <td>{{ $material->id }}</td>
              <td>{{ $material->name }}</td>
              <td>{{ $material->eer_code ?? 'N/D' }}</td>
              <td>
                <a href="{{ route('materials.edit', $material->id) }}" class="btn btn-warning btn-sm">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('materials.destroy', $material->id) }}" method="POST" style="display:inline-block;">
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

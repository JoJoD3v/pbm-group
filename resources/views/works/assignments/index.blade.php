@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Assegnazioni Lavori</h6>
    </div>
    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif
      
      @if(session('error'))
        <div class="alert alert-danger">
          {{ session('error') }}
        </div>
      @endif


        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered datatable" width="100%" cellspacing="0">
              <thead class="thead-light">
                <tr>
                  <th>Lavoro</th>
                  <th>Lavoratore</th>
                  <th>Cliente</th>
                  <th>Indirizzo Partenza</th>
                  <th>Indirizzo Destinazione</th>
                  <th>Materiali</th>
                  <th>Status</th>
                  <th>Azioni</th>
                </tr>
              </thead>
              <tbody>
                @forelse($assignments as $assignment)
                <tr>
                  <td>{{ $assignment['work_name'] }}</td>
                  <td>{{ $assignment['worker_name'] }}</td>
                  <td>{{ $assignment['customer_name'] }}</td>
                  <td>{{ $assignment['indirizzo_partenza'] }}</td>
                  <td>{{ $assignment['indirizzo_destinazione'] }}</td>
                  <td>{{ $assignment['materiale'] }}</td>
                  <td>{{ $assignment['status_lavoro'] }}</td>
                  <td>
                    <form action="{{ route('work.assignments.destroy') }}" method="POST" style="display:inline-block;">
                      @csrf
                      @method('DELETE')
                      <input type="hidden" name="work_id" value="{{ $assignment['work_id'] }}">
                      <input type="hidden" name="worker_id" value="{{ $assignment['worker_id'] }}">
                      <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Sei sicuro di voler rimuovere questa assegnazione?')">
                        <i class="bi bi-trash"></i> Rimuovi
                      </button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="8" class="text-center">Nessuna assegnazione trovata</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

  </div>
</div>
@endsection
